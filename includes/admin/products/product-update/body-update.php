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

$m4puallowed = true;

$dummy = false;
if ($dummy) {
    $m4p = new stdClass();
    $cookie = new stdClass();
}

$meli = self::initMeliSDK();
$psdb = self::initPSDB();

$params = $psdb->toObject($params);

$id_product = $params->id_product;
$ps_prod = $psdb->getProduct($id_product);
$ps_prod = $psdb->toObject($ps_prod);
$ps_core_prod = new Product($id_product);
$meli_data = $ps_prod->meli_data;

if ($meli_data) {
    $meli_data = Tools::jsonDecode($meli_data);
    if ($meli_data->id && Tools::getValue('key_tab') != 'ModuleMercadolibre') {
        $ml_prod = $meli->getProduct($meli_data->id);
        $cat = $meli->getCategory($ml_prod->category_id);
        
        $sold_quantity = $ml_prod->sold_quantity;
        $data = new stdClass();
        
        /**
         * Detect if has variations
         * */
        include($m4p->paths->includes.'/admin/products/product-update/detect-variations.php');
        
        /**
         * Update Stock
         * */
        include($m4p->paths->includes.'/admin/products/product-update/set-stock.php');
        
        /**
         * Update Price
         * */
        include($m4p->paths->includes.'/admin/products/product-update/set-price.php');
        
        /**
         * Updating
         * */
        include($m4p->paths->includes.'/admin/products/product-update/update.php');
    }
}
