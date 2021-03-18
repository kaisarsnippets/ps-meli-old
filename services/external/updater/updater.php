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

set_time_limit(0);

$dummy = false;
if ($dummy) {
    $m4p = new stdClass();
    $cookie = new stdClass();
    $m4puallowed = false;
    $psdb = new stdClass();
    $id_product = 1;
    $ml_has_variations = false;
    $data = new stdClass();
    $ml_variations = new stdClass();
    $meli = new stdClass();
    $ps_core_prod = new stdClass();
    $ml_prod = new stdClass();
    $ps_prod = new stdClass();
    $meli_data = new stdClass();
    $ps_price = 1;
}

/**
 * Start functionality
 * */
 
header("HTTP/1.1 200 OK");

require_once('../../../../../config/config.inc.php');
require_once('../../../classes/kc-meli.php');
require_once('../../../classes/kc-ps-db.php');

$internal_access_token = Configuration::get('MERCADOLIBRE_MODULE_TOKEN');
$internal_access_token_req = Tools::getValue('token');
$settings = Configuration::get('MERCADOLIBRE_MODULE_SETTINGS');

if ($internal_access_token === $internal_access_token_req) {
    
    $id_product = Tools::getValue('pid');
    if ($id_product) {
        
        if ($settings) {
            
            $settings = Tools::jsonDecode($settings);
            $appid = Configuration::get('MERCADOLIBRE_APP_ID');
            $secret = Configuration::get('MERCADOLIBRE_APP_SECRET');
            $meli = new KcMeli($appid, $secret);
            $psdb = new KcPsDb();
            
            $ps_prod = $psdb->getProduct($id_product);
            $ps_prod = $psdb->toObject($ps_prod);
            $ps_core_prod = new Product($id_product);
            $meli_data = $ps_prod->meli_data;
            
            if ($meli_data) {
                $meli_data = Tools::jsonDecode($meli_data);
                if ($meli_data->id) {
                    
                    $ml_prod = $meli->getProduct($meli_data->id);
                    $cat = $meli->getCategory($ml_prod->category_id);
                    
                    $sold_quantity = $ml_prod->sold_quantity;
                    $data = new stdClass();
                    
                    /**
                     * Detect if has variations
                     * */
                    $ml_variations_len = count($meli_data->quantities);
                    $ml_has_variations = false;
                    if ($ml_variations_len > 1) {
                        $ml_has_variations = true;
                        $data->variations = array();
                    }
                    if ($ml_has_variations) {
                        $ml_variations = $ml_prod->variations;
                        foreach ($ml_variations as $mlv) {
                            $mdvo = new stdClass();
                            array_push($data->variations, $mdvo);
                        }
                    }
                    
                    /**
                     * Update Stock
                     * */
                    $update_stock_selected = $settings->product->auto_upd_stock;
                    $ps_stock = $psdb->getProductStock($id_product);
                    $ps_stock_arr = array();
                    if ($ml_has_variations) {
                        $ml_variat_len = count($data->variations);
                    }
                    if ($ml_has_variations) {
                        foreach ($ps_stock as $pss) {
                            array_push($ps_stock_arr, $pss->quantity);
                        }
                    } else {
                        $pss = (int) $ps_stock[0]->quantity;
                        array_push($ps_stock_arr, $pss);
                    }
                    
                    $var_in_zero_cnt = 0;
                    if ($ml_has_variations) {
                        $i = 0;
                        foreach ($data->variations as $data_variat) {
                            $comb_link_num = $meli_data->combination_links[$i];
                            $data_variat->id = $ml_variations[$i]->id;
                            $data_variat->available_quantity = (int) $ps_stock_arr[$comb_link_num];
                            if ($data_variat->available_quantity == 0) {
                                $var_in_zero_cnt++;
                            }
                            $i++;
                        }
                        if ($var_in_zero_cnt == $ml_variat_len) {
                            $last_variat = count($data_variat) -1;
                            $data->variations[$last_variat]->available_quantity = 1;
                            if ($m4p->settings->product->pause_on_out_of_stock) {
                                $meli->pauseProduct($meli_data->id);
                                $psdb->setNotification(
                                    'item_out_stock',
                                    '{"id_product":"'. (int) $id_product .'",
                                    "id_meli":"'. pSQL($meli_data->id) .'"}'
                                );
                            }
                        }
                    } else {
                        
                        $data->available_quantity = (int) $ps_stock_arr[0];
                        
                        if ($data->available_quantity == 0) {
                            $data->available_quantity = 1;
                            if ($m4p->settings->product->pause_on_out_of_stock) {
                                $meli->pauseProduct($meli_data->id);
                                $psdb->setNotification(
                                    'item_out_stock',
                                    '{"id_product":"'. (int) $id_product .'",
                                    "id_meli":"'. pSQL($meli_data->id) .'"}'
                                );
                            }
                        }
                    }
                    
                    /**
                     * Price
                     * */
                    $update_price_selected = $settings->product->auto_upd_price;
                    $update_price_tax_selected = $settings->product->auto_upd_price_tax;
                    $update_price_add_perc = $settings->product->auto_upd_price_add_perc;
                    if ($update_price_selected) {
                        
                        $ps_price = (float) $ps_prod->price;
                        if ($update_price_tax_selected) {
                            $sql = "SELECT * FROM `"._DB_PREFIX_."tax` WHERE
                            id_tax = ".$ps_prod->id_tax_rules_group;
                            $prc_res = Db::getInstance()->ExecuteS($sql);
                            $prc_res = $psdb->toObject($prc_res);
                            $prc_res = $prc_res[0];
                            $tax_rate = (float) $prc_res->rate;
                            $tax = ($tax_rate * $ps_price) / 100;
                            $ps_price += $tax;
                        }
                        
                        if ($update_price_add_perc > 0) {
                            $update_price_add_perc = (float) $update_price_add_perc;
                            $ps_price += ($update_price_add_perc * $ps_price) / 100;
                        }
                        
                        $ps_price = number_format($ps_price, 2, '.', '');
                        
                        $my_site = $meli->getMyInfo();
                        $my_site = $my_site->site_id;
                        $meli_curr = $meli->getSite($my_site);
                        $meli_curr = $meli_curr->default_currency_id;
                        $curr_obj = Configuration::get('PS_CURRENCY_DEFAULT');
                        $curr_obj = Currency::getCurrency($curr_obj);
                        $curr_obj = $psdb->toObject($curr_obj);
                        
                        $ps_curr = $curr_obj->iso_code;
                        
                        if ($meli_curr != $ps_curr) {
        
                            $amount = urlencode($ps_price);
                            $from_Currency = urlencode($ps_curr);
                            $to_Currency = urlencode($meli_curr);
                            $gdomain = 'http://www.google.com/finance/converter';
                            $gurl = $gdomain."?a=".$amount."&from=".$from_Currency."&to=".$to_Currency;
                            $get = Tools::file_get_contents($gurl);
                            $get = explode("<span class=bld>", $get);
                            $get = explode("</span>", $get[1]);
                            $ps_price = preg_replace("/[^0-9\.]/", null, $get[0]);
                            $ps_price = number_format((float) $ps_price, 2, '.', '');
                            
                        }
                        
                        if ($ml_has_variations) {
                            $prod_attr_comb = $ps_core_prod->getAttributeCombinations(1);
                            $prod_attr_comb = $psdb->toObject($prod_attr_comb);
                            $variat_prices = new stdClass();
                            foreach ($prod_attr_comb as $pac) {
                                $variat_prices->{$pac->id_product_attribute} = $pac->price;
                            }
                            
                            $variat_prices_by_index = array();
                            foreach ($variat_prices as $vp) {
                                array_push($variat_prices_by_index, $vp);
                            }
                            
                            $i = 0;
                            foreach ($data->variations as $data_variat) {
                                $data_variat->price = 0;
                                $data_variat->price += $ps_price;
                                $i++;
                            }
                            
                        } else {
                            $data->price = $ps_price;
                        }
                        
                    }
                    
                    /**
                     * Updating
                     * */
                    if ($data) {
                        $res = $meli->updateProduct($meli_data->id, Tools::jsonEncode($data));
                    }
                    
                }
            }
            
        }
    }
}
