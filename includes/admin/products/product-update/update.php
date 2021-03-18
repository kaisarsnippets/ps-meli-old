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
    $pause_prod = false;
    $unpause_prod = false;
}

if ($data && isset($m4puallowed)) {
    $res = $meli->updateProduct($meli_data->id, Tools::jsonEncode($data));
    
    if (isset($pause_prod)) {
        if ($m4p->settings->product->pause_on_out_of_stock) {
            $meli->pauseProduct($meli_data->id);
            $psdb->setNotification(
                'item_out_stock',
                '{"id_product":"'. (int) $id_product .'",
                "id_meli":"'. pSQL($meli_data->id) .'"}'
            );
        }
    }
    if (isset($unpause_prod)) {
        $meli->activateProduct($meli_data->id);
    }
}
