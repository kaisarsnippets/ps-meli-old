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

require_once('../../../../../../config/config.inc.php');
require_once('../../../../classes/kc-meli.php');
require_once('../../../../classes/kc-ps-db.php');
require_once('../../../../classes/helper-functions.php');

$internal_access_token = Configuration::get('MERCADOLIBRE_MODULE_TOKEN');
$internal_access_token_req = Tools::getValue('token');

if ($internal_access_token === $internal_access_token_req) {
    
    $serial = Tools::getValue('serial');
    $serial = explode(',', $serial);
    $appid = Configuration::get('MERCADOLIBRE_APP_ID');
    $secret = Configuration::get('MERCADOLIBRE_APP_SECRET');
    $meli = new KcMeli($appid, $secret);
    
    $csvsuffix = '';
    
    $prods = array();
    
    if (!$serial[5]) {
        $serial[5] = 0;
    }
    if (!$serial[6]) {
        $serial[6] = 0;
    }
    
    $serial[5] = (int) $serial[5];
    $serial[6] = (int) $serial[6];
    
    $csvsuffix = '-'.$serial[5].'-'.$serial[6];
    
    $config = new stdClass();
    $config->meli = $meli;
    $config->filters = $serial;
    $prods = KcPsDb::setMeliProductExportRow($config);
    
    if ($serial[7]) {
        $prods = array_reverse($prods);
    }
    
    $columns = array(
        'MELI ID',
        'Name',
        'Description',
        'Final Price',
        'Quantity',
        'Condition',
        'Images'
    );
    
    $delimiter = ';';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=meli-prods'.$csvsuffix.'.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, $columns, $delimiter);
    
    foreach ($prods as $prod) {
        $row=array();
        array_push($row, 'ml-'.$prod->id.'-ml');
        array_push($row, trim($prod->title));
        $descr = $meli->getProductDescription($prod->id)->text;
        array_push($row, $descr);
        array_push($row, $prod->price);
        array_push($row, $prod->available_quantity);
        if ($prod->condition!='new' || $prod->condition!='used') {
            $prod->condition='new';
        }
        array_push($row, $prod->condition);
        if ($prod->pictures) {
            $pparr=array();
            if (HelperFunctions::isHttp()) {
                foreach ($prod->pictures as $pp) {
                    array_push($pparr, $pp->url);
                }
            } else {
                foreach ($prod->pictures as $pp) {
                    array_push($pparr, $pp->secure_url);
                }
            }
            $ppstr=join(',', $pparr);
            array_push($row, $ppstr);
        } else {
            array_push($row, '');
        }
        fputcsv($output, $row, $delimiter);
    }
    fclose($output);
    exit();
}
