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
$m4p->ps=new stdClass();
$m4p->ps->product = new stdClass();
$m4p->ps->product->id = Tools::getValue('id_product');

$meli = self::initMeliSDK();
$psdb = self::initPSDB($meli);

if (Tools::getValue('submitAddproductAndStay') == 'import') {
    
    $mid = Tools::getValue('m4p-item-id');
    $mid = str_replace('-', '', $mid);
    $url = $m4p->paths->services_abs.
    '/internal/admin/items/import.php?pid='.$m4p->ps->product->id.
    '&mid='.$mid.
    '&token='.$m4p->tokens->meli_token;
    
    $res = Tools::file_get_contents($url);
    $m4p->this->context->cookie->__set('meli_upd_res', $res);
    
    $notif = $m4p->paths->services_abs.
    '/external/notifications/notifications.php?iid='.$mid.'&token='.
    $m4p->tokens->meli_token;
    Tools::file_get_contents($notif);
}

if (Tools::getValue('submitAddproductAndStay') == 'remove') {
    
    $url = $m4p->paths->services_abs.
    '/internal/admin/items/remove.php?pid='.$m4p->ps->product->id.
    '&token='.$m4p->tokens->meli_token;
    
    Tools::file_get_contents($url);
}

if (Tools::getValue('submitAddproductAndStay') == 'create') {
    
    $meli_data = Tools::getValue('meli-data');
    
    $meli_data = Tools::jsonDecode($meli_data);
    if (!isset($meli_data->description)) {
        $meli_data->description = '';
    }
    if (!$meli_data->description) {
        $meli_data->description = '';
    }
    
    $description = htmlentities($meli_data->description);
    $description = str_replace('&lt;', '<', $description);
    $description = str_replace('&gt;', '>', $description);
    $description = str_replace('&quot;', '\"', $description);
    $meli_data->description = $description;
    
    if (isset($meli_data->images_processing)) {
        unset($meli_data->images_processing);
    }
    unset($meli_data->id);
    unset($meli_data->permalink);
    unset($meli_data->sold_quantity);
    unset($meli_data->status);
    unset($meli_data->sub_status);
    if ($meli_data->condition == 'not_allowed') {
        unset($meli_data->condition);
    }
    if ($meli_data->listing_type_id == 'free') {
        if (isset($meli_data->automatic_relist)) {
            unset($meli_data->automatic_relist);
        }
    }
    if ($meli_data->buying_mode == 'auction') {
        if (isset($meli_data->automatic_relist)) {
            unset($meli_data->automatic_relist);
        }
    }
    
    /**
     * Check if is attributed
     * */
    $catdata = $meli->getCategory($meli_data->category_id);
    $is_attributed = false;
    if ($catdata->attribute_types == 'attributes') {
        $is_attributed = true;
    }
    
    if ($is_attributed) {
        
        $meli_data->attributes = array();
        
        if ($meli_data->variations) {
            foreach ($meli_data->variations as $vars) {
                $cnt = 0;
                foreach ($vars->attribute_combinations as $attr) {
                    if (!$attr->value_id || !$attr->value_name) {
                        array_push($meli_data->attributes, $attr);
                    }
                }
            }
            unset($meli_data->variations);
        }
        
        if ($meli_data->buying_mode == 'classified') {
            unset($meli_data->automatic_relist);
        }
        
    } else {
        
        $combination_links = array();
        
        if ($meli_data->variations) {
            foreach ($meli_data->variations as $vars) {
                $cnt = 0;
                foreach ($vars->attribute_combinations as $attr) {
                    if (!$attr->value_id && !$attr->value_name) {
                        unset($vars->attribute_combinations[$cnt]);
                    }
                    $cnt++;
                }
                array_push($combination_links, $vars->combination_link);
                unset($vars->combination_link);
            }
        }
        
    }
    
    unset($meli_data->has_attributes);
    
    $meli_data = Tools::jsonEncode($meli_data);
    
    $meli_data = str_replace('\\\"', '"', $meli_data);
    $meli_data = str_replace('<p>n</p>"', '<p></p>', $meli_data);
    
    $res = $meli->validateProduct($meli_data);
    
    if (!$res || $res->site_id) {
        
        $res = $meli->listProduct($meli_data);
        
        $mid = $res->id;
        $mid = str_replace('-', '', $mid);
        $url = $m4p->paths->services_abs.
        '/internal/admin/items/import.php?pid='.$m4p->ps->product->id.
        '&mid='.$mid.
        '&token='.$m4p->tokens->meli_token;
        Tools::file_get_contents($url);
        
        if ($combination_links) {
            $psdb->updateProductCombinationLinks($m4p->ps->product->id, $combination_links);
        }
        
        $notif = $m4p->paths->services_abs.
        '/external/notifications/notifications.php?iid='.$mid.'&token='.
        $m4p->tokens->meli_token;
        Tools::file_get_contents($notif);
        
    } else {
        $m4p->this->context->cookie->__set('meli_upd_res', Tools::jsonEncode($res));
    }
    
}

if (Tools::getValue('submitAddproductAndStay') == 'update') {
    
    $meli_data = Tools::getValue('meli-data');
    
    $meli_data = $m4p->functions->parseEscapedToJSON($meli_data);
    
    $meli_data = Tools::jsonDecode($meli_data);
    
    $cat_data = $meli->getCategory($meli_data->category_id);
    $is_attribute_attribute = false;
    if ($cat_data->attribute_types == 'attributes') {
        $is_attribute_attribute = true;
    }
    
    if ($meli_data->status == 'closed') {
        
        $res = $meli->closeProduct($meli_data->id);
        
    } else {
        
        $description = htmlentities($meli_data->description);
        $description = str_replace('&lt;', '<', $description);
        $description = str_replace('&gt;', '>', $description);
        $description = str_replace('&quot;', '\"', $description);
        
        $res = $meli->updateProductDescription($meli_data->id, $description);
        
        $meli_data_upd = new stdClass();
        
        if ($meli_data->sold_quantity == 0) {
            $meli_data_upd->title = str_replace('"', '\"', $meli_data->title);
            $meli_data_upd->warranty = str_replace('"', '\"', $meli_data->warranty);
            $meli_data_upd->condition = $meli_data->condition;
        }
        $meli_data_upd->video_id = $meli_data->video_id;
        
        if ($meli_data->location) {
            $meli_data_upd->location = $meli_data->location;
            if (isset($meli_data_upd->location->address_line)) {
                unset($meli_data_upd->location->address_line);
            }
            if (isset($meli_data_upd->location->zip_code)) {
                unset($meli_data_upd->location->zip_code);
            }
            if (isset($meli_data_upd->location->open_hours)) {
                unset($meli_data_upd->location->open_hours);
            }
        }
        
        if ($meli_data->coverage_areas) {
            $meli_data_upd->coverage_areas = $meli_data->coverage_areas;
        }
        
        $combination_links = array();
        
        if ($meli_data->variations) {
            if ($meli_data->buying_mode == 'classified') {
                
                if ($meli_data->pictures) {
                    $meli_data_upd->pictures = $meli_data->pictures;
                }
                
                $meli_data_upd->attributes = array();
                foreach ($meli_data->variations as $variation) {
                    
                    foreach ($variation->attribute_combinations as $attr_comb) {
                        $attr = new stdClass();
                        $attr->id = $attr_comb->id;
                        if ($attr_comb->value_id) {
                            $attr->value_id = $attr_comb->value_id;
                        }
                        if ($attr_comb->value_name) {
                            $attr->value_name = $attr_comb->value_name;
                        }
                        array_push($meli_data_upd->attributes, $attr);
                    }
                }
                
            } else {
                
                if ($meli_data->sold_quantity == 0) {
                    /**
                     * If Product hasn't been sold
                     * */
                    $meli_data_upd->variations = array();
                    $meli_data_upd->pictures = array();
                    foreach ($meli_data->variations as $variation) {
                        $upd_variation = new stdClass();
                        $upd_variation->picture_ids = array();
                        $upd_variation->id = $variation->id;
                        $upd_variation->price = $variation->price;
                        $upd_variation->available_quantity = $variation->available_quantity;
                        array_push($combination_links, $variation->combination_link);
                        $upd_variation->attribute_combinations = array();
                        
                        foreach ($variation->attribute_combinations as $attr_comb) {
                            if ($attr_comb->value_id) {
                                array_push($upd_variation->attribute_combinations, $attr_comb);
                            }
                            if ($attr_comb->value_name) {
                                array_push($upd_variation->attribute_combinations, $attr_comb);
                            }
                        }
                        
                        foreach ($variation->picture_ids as $pic) {
                            array_push($upd_variation->picture_ids, $pic);
                            $pic_obj = new stdClass();
                            $pic_obj->source = $pic;
                            array_push($meli_data_upd->pictures, $pic_obj);
                        }
                        array_push($meli_data_upd->variations, $upd_variation);
                    }
                } else {
                    /**
                     * If Product has been sold
                     * */
                    $meli_data_upd->variations = array();
                    foreach ($meli_data->variations as $variation) {
                        $upd_variation = new stdClass();
                        $upd_variation->id = $variation->id;
                        $upd_variation->price = $variation->price;
                        $upd_variation->available_quantity = $variation->available_quantity;
                        array_push($meli_data_upd->variations, $upd_variation);
                        
                        array_push($combination_links, $variation->combination_link);
                    }
                }
                
            }
        } else {
            $meli_data_upd->price = $meli_data->price;
            $meli_data_upd->available_quantity = $meli_data->available_quantity;
            $meli_data_upd->pictures = $meli_data->pictures;
        }
        
        $picstr = Tools::jsonEncode($meli_data_upd->pictures);
        if (strpos($picstr, 'proccesing_image') == false) {
            $meli_data_upd = Tools::jsonEncode($meli_data_upd);
            
            //Update Product
            $res = $meli->updateProduct($meli_data->id, $meli_data_upd);
            
            $meli->changeStatusProduct($meli_data->id, $meli_data->status);
            
            //Set combination correspondence (If applies)
            if ($combination_links) {
                $psdb->updateProductCombinationLinks($m4p->ps->product->id, $combination_links);
            }
            
            //Notify
            $mid = $meli_data->id;
            $mid = str_replace('-', '', $mid);
            $notif = $m4p->paths->services_abs.
            '/external/notifications/notifications.php?iid='.$mid.'&token='.
            $m4p->tokens->meli_token;
            Tools::file_get_contents($notif);
        }
        
        if (gettype($res) == 'string') {
            $res = Tools::jsonDecode($res);
            if (isset($res->error)) {
                $res = Tools::jsonEncode($res);
                $m4p->this->context->cookie->__set('meli_upd_res', $res);
            }
        }
    }
}

if (Tools::getValue('submitAddproductAndStay') == 'relist') {
    
    $meli_data = Tools::getValue('meli-data');
    $meli_data = $m4p->functions->parseEscapedToJSON($meli_data);
    
    $meli_data = Tools::jsonDecode($meli_data);
    
    $meli_data_upd = new stdClass();
    
    if ($meli_data->variations) {
        if ($meli_data->buying_mode == 'classified') {
            
            $meli_data_upd->price = (float) $meli_data->price;
            $meli_data_upd->quantity = (float) $meli_data->available_quantity;
            
        } else {
            
            $meli_data_upd->variations = array();
            foreach ($meli_data->variations as $variation) {
                $upd_variation = new stdClass();
                $upd_variation->id = $variation->id;
                $upd_variation->price = (float) $variation->price;
                $upd_variation->quantity = (float) $variation->available_quantity;
                array_push($meli_data_upd->variations, $upd_variation);
            }
            
        }
    } else {
        $meli_data_upd->price = (float) $meli_data->price;
        $meli_data_upd->quantity = (float) $meli_data->available_quantity;
    }
    $meli_data_upd->listing_type_id = $meli_data->listing_type_id;
    
    $meli_data_upd = Tools::jsonEncode($meli_data_upd);
    
    $res = $meli->relistProduct($meli_data->id, $meli_data_upd, false);
    
    $has_errors = false;
    if (gettype($res) == 'string') {
        $res = Tools::jsonDecode($res);
        if (isset($res->error)) {
            $has_errors = true;
            $res = Tools::jsonEncode($res);
            $m4p->this->context->cookie->__set('meli_upd_res', $res);
        }
    }
    
    if (!$has_errors) {
        if (gettype($res) == 'string') {
            $res = Tools::jsonDecode($res);
        }
        $mid = $res->id;
        $mid = str_replace('-', '', $mid);
        $url = $m4p->paths->services_abs.
        '/internal/admin/items/import.php?pid='.$m4p->ps->product->id.
        '&mid='.$mid.
        '&token='.$m4p->tokens->meli_token;
        Tools::file_get_contents($url);
        
        $notif = $m4p->paths->services_abs.
        '/external/notifications/notifications.php?iid='.$mid.'&token='.
        $m4p->tokens->meli_token;
        Tools::file_get_contents($notif);
    }
    
}

if (Tools::getValue('submitAddproductAndStay') == 'delete') {
    $meli_data = Tools::getValue('meli-data');
    $meli_data = $m4p->functions->parseEscapedToJSON($meli_data);
    $meli_data = Tools::jsonDecode($meli_data);
    $mid = $meli_data->id;
    $pid = Tools::getValue('id_product');
    
    $meli->deleteProduct($mid);
    $url = $m4p->paths->services_abs.
    '/internal/admin/items/remove.php?pid='.$pid.
    '&token='.$m4p->tokens->meli_token;
    Tools::file_get_contents($url);
}
