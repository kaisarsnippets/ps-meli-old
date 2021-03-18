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

if (isset($m4puallowed)) {
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
}
