<?php
/**
* 2015 KaisarCode
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    KaisarCode <kaisar@kaisarcode.com>
*  @copyright 2015 KaisarCode.com
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/**
 * Define vars to pass validator
 * (this definitions are functionally irrelevant)
 * */
$dummy = false;
if ($dummy) {
    $m4p = new stdClass();
    $cookie = new stdClass();
}

/**
 * Initializing
 * */
$pid = Tools::getValue('id_product');
$meli = self::initMeliSDK();
$psdb = self::initPSDB($meli);

$m4p->meli_tab = Tools::getValue('meli_tab');
if (!$m4p->meli_tab) {
    $m4p->meli_tab = 'edit';
}

/**
 * Setting up base MELI Data
 * */
$m4p->meli = new stdClass();
$m4p->meli->user = new stdClass();
$m4p->meli->user->nick = '';
if ($meli) {
    $m4p->meli->user->nick = $meli->getMyInfo()->nickname;
}

/**
 * Setting up base PS data
 * */

if ($pid != '0') {
    $m4p->ps->product = $psdb->getProduct($pid);
    $tmp_prod = new Product($pid, false, null, (int) $m4p->current_shop_id);
} else {
    $m4p->ps->product = new stdClass();
    $tmp_prod = new stdClass();
}

/**
 * Placeholders for ML values
 * */
$m4p->meli->item = new stdClass();
$m4p->meli->item->id = '';
$m4p->meli->item->currency_id = '';
$m4p->meli->item->title = '';
$m4p->meli->item->description = ' ';
$m4p->meli->item->permalink = '';
$m4p->meli->item->sold_quantity = '';
$m4p->meli->item->status = '';
$m4p->meli->item->sub_status = array();
$m4p->meli->item->condition = 'new';
$m4p->meli->item->listing_type_id = 'bronze';
$m4p->meli->item->buying_mode = 'buy_it_now';
$m4p->meli->item->price = 1;
$m4p->meli->item->available_quantity = 1;
$m4p->meli->item->warranty = '';
$m4p->meli->item->video_id = '';
$m4p->meli->item->automatic_relist = false;

$m4p->meli->item->pictures = array();
$m4p->meli->item->images_processing = 0;

/**
 * Checking if product is linked with ML
 * */

if ($pid != '0') {
    
    if ($m4p->ps->product->meli_data) {
        $m4p->ps->product->meli_data = Tools::jsonDecode($m4p->ps->product->meli_data);
        
        /**
         * If linked
         * */
        if ($meli) {
            if ($m4p->ps->product->meli_data->id) {
                
                /**
                 * Get data
                 * */
                $m4p->meli->item = $meli->getProduct($m4p->ps->product->meli_data->id);
                $description = $meli->getProductDescription($m4p->ps->product->meli_data->id);
                
                if (isset($m4p->meli->item->attributes) && $m4p->meli->item->buying_mode == 'classified') {
                    $m4p->meli->item->variations = array();
                    $variation = new stdClass();
                    $variation->attribute_combinations = array();
                    foreach ($m4p->meli->item->attributes as $attribute) {
                        array_push($variation->attribute_combinations, $attribute);
                    }
                    $variation->price = $m4p->meli->item->price;
                    $variation->quantity = $m4p->meli->item->available_quantity;
                    $variation->picture_ids = array();
                    if ($m4p->meli->item->pictures) {
                        foreach ($m4p->meli->item->pictures as $pic) {
                            array_push($variation->picture_ids, $pic->id);
                        }
                    }
                    array_push($m4p->meli->item->variations, $variation);
                }
                
                if (isset($m4p->meli->item->variations)) {
                    $i = 0;
                    foreach ($m4p->meli->item->variations as $variation) {
                        if (isset($m4p->ps->product->meli_data->combination_links)) {
                            if ($m4p->ps->product->meli_data->combination_links[$i]) {
                                $cmblnk = (int) $m4p->ps->product->meli_data->combination_links[$i];
                                $variation->combination_link = $cmblnk;
                            } else {
                                $variation->combination_link = 0;
                            }
                        } else {
                            $variation->combination_link = 0;
                        }
                        $i++;
                    }
                }
                
                $description = $meli->getProductDescription($m4p->ps->product->meli_data->id);
                
                /**
                 * Prevent JSON Errors on text fields
                 * */
                if (isset($description->text)) {
                    $m4p->meli->item->description = $m4p->functions->parseDefaultMeliDataValue($description->text);
                }
                $m4p->meli->item->title = $m4p->functions->parseDefaultMeliDataValue($m4p->meli->item->title);
                $m4p->meli->item->warranty = $m4p->functions->parseDefaultMeliDataValue($m4p->meli->item->warranty);
                
                if (isset($m4p->meli->item->sub_status)) {
                    foreach ($m4p->meli->item->sub_status as $subst) {
                        if ($subst == 'deleted') {
                            $url = $m4p->paths->services_abs.
                            '/internal/admin/items/remove.php?pid='.$pid.
                            '&token='.$m4p->tokens->meli_token;
                            Tools::file_get_contents($url);
                            $m4p->functions->redirect();
                            break;
                        }
                    }
                }
                
                $m4p->meli->item->images_processing = 0;
                $picstr = Tools::jsonEncode($m4p->meli->item->pictures);
                if (strpos($picstr, 'proccesing_image') !== false) {
                    $m4p->meli->item->images_processing = 1;
                }
            }
        }
        
    } else {
        
        $id_lang = $m4p->this->context->cookie->id_lang;
        
        /**
         * If not linked, leave placeholders
         * */
        $m4p->ps->product->meli_data = new stdClass();
        $m4p->ps->product->meli_data->id = '';
        
        /**
         * Default Values
         * */
        $m4p->meli->item->title = $tmp_prod->name[$id_lang];
        $m4p->meli->item->title =  $m4p->functions->parseDefaultMeliDataValue($m4p->meli->item->title);
        $m4p->meli->item->title = str_replace('"', '&quot;', $m4p->meli->item->title);
        if (Tools::strlen($m4p->meli->item->title) > 60) {
            $m4p->meli->item->title = Tools::substr($m4p->meli->item->title, 0, 60);
        }
        $m4p->meli->item->description = $tmp_prod->description[$m4p->this->context->cookie->id_lang];
        $m4p->meli->item->description = $m4p->functions->parseDefaultMeliDataValue($m4p->meli->item->description);
        if (Tools::strlen($m4p->meli->item->description) > 50000) {
            $m4p->meli->item->description = Tools::substr($m4p->meli->item->description, 0, 60);
        }
        $m4p->meli->item->price = number_format((float) $tmp_prod->price, 2, '.', '');
        
        if ($m4p->settings->product->auto_upd_price_tax) {
            $sql = "SELECT * FROM `"._DB_PREFIX_."tax` WHERE
            id_tax = ".$m4p->ps->product->id_tax_rules_group;
            $prc_res = Db::getInstance()->ExecuteS($sql);
            $prc_res = $psdb->toObject($prc_res);
            $prc_res = $prc_res[0];
            $tax_rate = (float) $prc_res->rate;
            $tax = ($tax_rate * $m4p->meli->item->price) / 100;
            $m4p->meli->item->price += $tax;
        }
        if ($m4p->settings->product->auto_upd_price_add_perc) {
            $update_price_add_perc = $m4p->settings->product->auto_upd_price_add_perc;
            $update_price_add_perc = (float) $update_price_add_perc;
            $m4p->meli->item->price += ($update_price_add_perc * $m4p->meli->item->price) / 100;
        }
        
        /**
         * Default Images
         * */
        $images = $psdb->getProductImages((int) $pid, (int) $id_lang);
        $m4p->meli->item->pictures = array();
        $i = 0;
        foreach ($images as $image) {
            
            if ($i < 12) {
            
                $img = new Image((int) $image->id_image, (int) $image->id_lang);
                $pic = new stdClass();
                $pic->url = _PS_BASE_URL_._THEME_PROD_DIR_.$img->getExistingImgPath().'.'.$img->image_format;
                array_push($m4p->meli->item->pictures, $pic);
                
            }
            
            $i++;
        }
        
    }

}

if ($meli) {

    /**
     * Setting Up Meli Info
     * */
    $m4p->meli->info = new stdClass();
    $m4p->meli->info->sites = new stdClass();
    $m4p->meli->info->sites->all = $meli->getSites();
    $m4p->meli->info->sites->avail = array();
    $m4p->meli->info->sites->amnt = 0;

    /**
     * Extending Item Pictures Data
     * */
    $cnt = 0;
    foreach ($m4p->meli->item->pictures as $pic) {
        if ($m4p->global_info->is_http) {
            $file = $pic->url;
        } else {
            if (isset($pic->secure_url)) {
                $file = $pic->secure_url;
            } else {
                $file = $pic->url;
            }
        }
        if (isset($m4p->meli->item->pictures[$cnt]->id)) {
            $m4p->meli->item->pictures[$cnt]->id = $m4p->meli->item->pictures[$cnt]->id;
        } else {
            $m4p->meli->item->pictures[$cnt]->id = uniqid('pic');
        }
        $m4p->meli->item->pictures[$cnt]->protocol_based_url = $file;
        if (isset($m4p->meli->item->pictures[$cnt]->weight)) {
            $m4p->meli->item->pictures[$cnt]->weight = $m4p->functions->getExternalFileSize($file);
        }
        if (isset($m4p->meli->item->pictures[$cnt]->fname)) {
            $m4p->meli->item->pictures[$cnt]->fname = basename($file);
        }
        if (isset($m4p->meli->item->pictures[$cnt]->path)) {
            $m4p->meli->item->pictures[$cnt]->path = rtrim($file, $m4p->meli->item->pictures[$cnt]->fname);
        }
        $cnt++;
    }

    /**
     * Get Item's Available ML Sites
     * */
    if ($m4p->meli->item->id) {
        $site = $meli->getSite($m4p->meli->item->site_id);
        $site->flag = $m4p->paths->module.'/views/img/flags/'.$site->id.'.png';
        $m4p->meli->info->sites->amnt = 1;
        array_push($m4p->meli->info->sites->avail, $site);
    } else {
        
        $myInfo = $meli->getMyInfo();
        $site_id = $myInfo->site_id;
        $site = $meli->getSite($site_id);
        $site->flag = $m4p->paths->module.'/views/img/flags/'.$site_id.'.png';
        array_push($m4p->meli->info->sites->avail, $site);
        
    }
    
    /**
     * Setting Up Meli Item Currency & Price
     * */
    $m4p->meli->item->currency_id = $site->default_currency_id;
    $m4p->ps->product->currency = new stdClass();
    $m4p->ps->product->currency->iso_code = $m4p->this->context->currency->iso_code;
    
    if (!$m4p->meli->item->id) {
        if ($m4p->ps->product->currency->iso_code != $m4p->meli->item->currency_id) {
            $amount = urlencode($m4p->meli->item->price);
            $from_Currency = urlencode($m4p->ps->product->currency->iso_code);
            $to_Currency = urlencode($m4p->meli->item->currency_id);
            $gdomain = 'http://www.google.com/finance/converter';
            $gurl = $gdomain."?a=".$amount."&from=".$from_Currency."&to=".$to_Currency;
            $get = Tools::file_get_contents($gurl);
            $get = explode("<span class=bld>", $get);
            $get = explode("</span>", $get[1]);
            $m4p->meli->item->price = preg_replace("/[^0-9\.]/", null, $get[0]);
            $m4p->meli->item->price = number_format((float) $m4p->meli->item->price, 2, '.', '');
        }
    }
    $m4p->meli->item->price = number_format((float) $m4p->meli->item->price, 2, '.', '');
    
    /**
    * PS Product Stock
    * */
    $m4p->ps->product->comb_stock = $psdb->getProductStock($pid, $m4p->current_shop_id);
    $m4p->ps->product->comb_stock_json = Tools::jsonEncode($m4p->ps->product->comb_stock);
    if (!$m4p->meli->item->id) {
        $m4p->meli->item->available_quantity = $m4p->ps->product->comb_stock[0]->quantity;
    }
    
    /**
     * Meli Data JSON
     * */
    $m4p->meli->item->title = str_replace('\\', '', $m4p->meli->item->title);
    $m4p->meli->item->warranty = str_replace('\\', '', $m4p->meli->item->warranty);
    $m4p->meli->item_json = Tools::jsonEncode($m4p->meli->item);
    $m4p->meli->meli_data = html_entity_decode(Tools::jsonEncode($m4p->meli->item));

    /**
     * Return error message from
     * create or update
     * */
    $m4p->err_msg = new stdClass();
    $m4p->err_msg->error = null;
    $m4p->err_msg->message = null;
    $m4p->err_msg->cause = array();
    
    if (isset($m4p->this->context->cookie->meli_upd_res)) {
        
        $msg = Tools::jsonDecode($m4p->this->context->cookie->meli_upd_res);
        $m4p->err_msg = new stdClass();
        if (isset($msg->error)) {
            $m4p->err_msg->error = $msg->error;
        }
        if (isset($msg->message)) {
            $m4p->err_msg->message = $msg->message;
        }
        if (isset($msg->cause)) {
            $m4p->err_msg->cause = $msg->cause;
        }
    }
    $m4p->this->context->cookie->__unset('meli_upd_res');

}
