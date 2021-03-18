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

header('Content-Type: application/json');

require_once('../../../../../../config/config.inc.php');
require_once('../../../../classes/kc-meli.php');
require_once('../../../../classes/kc-ps-db.php');

$internal_access_token = Configuration::get('MERCADOLIBRE_MODULE_TOKEN');
$internal_access_token_req = Tools::getValue('token');

if ($internal_access_token === $internal_access_token_req) {
    
    $appid = Configuration::get('MERCADOLIBRE_APP_ID');
    $secret = Configuration::get('MERCADOLIBRE_APP_SECRET');
    $meli = new KcMeli($appid, $secret);
    $psdb = new KcPsDb($meli);
    
    $recent = $psdb->getRecentlyImportedMeliProds();
    foreach ($recent as $prd) {
        $idarr = array();
        $meli_id_container = preg_match("/ml-.*-ml/", $prd->meta_keywords, $idarr);
        if ($idarr) {
            $pid = $prd->id_product;
            $meli_id = str_replace('-ml', '', str_replace('ml-', '', $idarr[0]));
            $prd->meta_keywords = preg_replace("/ml-.*-ml/", '', $prd->meta_keywords);
            $meta_upd_sql = "UPDATE `"._DB_PREFIX_."product_lang`
            SET `meta_keywords` = '".$prd->meta_keywords."'
            WHERE `id_product` =".$pid;
            Db::getInstance()->Execute($meta_upd_sql);
            $mid = $meli->getProductEdgeID($meli_id);
            $psdb->updateProductMeliData($pid, $mid);
            
            /** ********************************************************
             * UPDATE FLAG DATA ON PRODUCT
             * (Same changes must be done in notifications.php)
             * (Same changes must be done in import.php)
             * (This can be used to show extra info on Products List)
             * ********************************************************/
            $prod = $meli->getProduct($mid);
            $data = new stdClass();
            
            //Add status flag
            $data->status = $prod->status;
            //Add quantities flag
            /**
             * [0] = Global Stock
             * [1] to [N] = Variation Stock
             * */
            $data->quantities = array($prod->available_quantity);
            if ($prod->variations) {
                foreach ($prod->variations as $vars) {
                    array_push($data->quantities, $vars->available_quantity);
                }
            }
            
            $psdb->updateFlagMeliData($pid, $mid, $data);
            
        }
    }
}
