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

$update_price_selected = $m4p->settings->product->auto_upd_price;
$update_price_tax_selected = $m4p->settings->product->auto_upd_price_tax;
$update_price_add_perc = $m4p->settings->product->auto_upd_price_add_perc;

if (isset($m4puallowed) && $update_price_selected) {
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
    $ps_curr = $m4p->this->context->currency->iso_code;
    
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
        $prod_attr_comb = $ps_core_prod->getAttributeCombinations($m4p->this->context->language->id);
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
