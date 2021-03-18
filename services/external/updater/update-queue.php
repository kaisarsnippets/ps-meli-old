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

// Script start
$start = microtime(true);

set_time_limit(0);

header("HTTP/1.1 200 OK");

require_once('../../../../../config/config.inc.php');
require_once('../../../classes/kc-meli.php');
require_once('../../../classes/kc-ps-db.php');

$internal_access_token = Configuration::get('MERCADOLIBRE_MODULE_TOKEN');
$internal_access_token_req = Tools::getValue('token');
$settings = Tools::jsonDecode(Configuration::get('MERCADOLIBRE_MODULE_SETTINGS'));
$target_shop = $settings->shop->target_id;

if ($internal_access_token === $internal_access_token_req) {
    
    $appid = Configuration::get('MERCADOLIBRE_APP_ID');
    $secret = Configuration::get('MERCADOLIBRE_APP_SECRET');
    $meli = new KcMeli($appid, $secret);
    $psdb = new KcPsDb();
    $shop_url = rtrim(_PS_BASE_URL_, '/').rtrim(__PS_BASE_URI__, '/');
    $service_url = '/modules/mercadolibre/services/external/updater/updater.php';
    
    $first = 0;
    if (file_exists('last-upd.txt')) {
        $first = (int) Tools::file_get_contents('last-upd.txt');
    } else {
        file_put_contents('last-upd.txt', 0);
    }
    
    if (Tools::getValue('a')) {
        $amount = (int) Tools::getValue('a');
    } else {
        $amount = 10;
    }
    
    $last = $first + $amount;
    
    $ml_prods = $psdb->getMeliProducts();
    
    if (!isset($ml_prods[$first])) {
        $first = 0;
        $last = $first + $amount;
        file_put_contents('last-upd.txt', 0);
    }
    
    $i = 0;
    $pi = 0;
    foreach ($ml_prods as $p) {
        
        if ($i >= $first && $i < $last) {
                
            $pid = $p->id_product;
            $url = $shop_url.$service_url.'?pid='.$pid.'&token='.$internal_access_token;
            $res = $meli->curlGet($url);
            $pi++;
            print_r($url.'<br />');
            
            file_put_contents('last-upd.txt', $first + $pi);
        }
        
        $i++;
        
    }
        
}

$time_elapsed_secs = microtime(true) - $start;
$time_elapsed_min = $time_elapsed_secs / 60;
$time_elapsed = number_format((float) $time_elapsed_min, 2, '.', '');
echo $pi.' items updated in '.$time_elapsed.' minutes. ('.$last.'/'.$i.')';
