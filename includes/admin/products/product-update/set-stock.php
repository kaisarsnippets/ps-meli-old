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

$update_stock_selected = $m4p->settings->product->auto_upd_stock;

if (isset($m4puallowed) && $update_stock_selected) {
    $ps_stock = $psdb->getProductStock($id_product, (int) $m4p->settings->shop->target_id);
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
            //Flag to pause product in update.php
            $pause_prod = true;
        }
        
    } else {
        
        $data->available_quantity = (int) $ps_stock_arr[0];
        
        if ($data->available_quantity == 0) {
            $data->available_quantity = 1;
            //Flag to pause product in update.php
            $pause_prod = true;
        } else {
            $unpause_prod = true;
        }
    }
}
