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

set_time_limit(0);

$dummy = false;
if ($dummy) {
    $m4p = new stdClass();
    $cookie = new stdClass();
    
}

/**
 * Start functionality
 * */
 
header("HTTP/1.1 200 OK");

require_once('../../../../../config/config.inc.php');
require_once('../../../classes/kc-meli.php');
require_once('../../../classes/kc-ps-db.php');
require_once('../../../classes/process-order.php');

$internal_access_token = Configuration::get('MERCADOLIBRE_MODULE_TOKEN');
$internal_access_token_req = Tools::getValue('token');
$settings = Tools::jsonDecode(Configuration::get('MERCADOLIBRE_MODULE_SETTINGS'));
$target_shop = KcPsDb::getShop($settings->shop->target_id);
$target_shop = $target_shop[0];
$shop_id = $target_shop->id_shop;
$shop_group_id = $target_shop->id_shop_group;

//if ($internal_access_token === $internal_access_token_req) {
if (true) {
    
    $notif = Tools::jsonDecode(Tools::file_get_contents("php://input"));
    if (!$notif) {
        $notif = new stdClass();
        $notif->user_id = '';
        $notif->resource = '';
        $notif->topic = '';
        $notif->received = '';
        $notif->sent = '';
    }
    
    $appid = Configuration::get('MERCADOLIBRE_APP_ID');
    $secret = Configuration::get('MERCADOLIBRE_APP_SECRET');
    $meli = new KcMeli($appid, $secret);
    
    /**
     * Orders
     * */
    if (
        ($notif->user_id &&
        $notif->resource &&
        $notif->topic == 'orders' &&
        $notif->received &&
        $notif->sent) ||
        Tools::getValue('oid')
    ) {
        if (Tools::getValue('oid')) {
            $id = Tools::getValue('oid');
        } else {
            $resource = $notif->resource;
            $id = str_replace('/orders/', '', $resource);
        }
        M4POrders::processOrder($id, $meli, $cookie);
    }
    
    /**
     * Payments
     * */
    if (
        ($notif->user_id &&
        $notif->resource &&
        $notif->topic == 'payments' &&
        $notif->received &&
        $notif->sent)
    ) {
        $resource = $notif->resource;
        $payment_id = str_replace('/payments/', '', $resource);
        $meli_payment = $meli->getPaymentInfo($payment_id);
        $id = (int) $meli_payment->order_id;
        M4POrders::processOrder($id, $meli, $cookie);
    }
    
    /**
     * Items
     * */
    if (
        ($notif->user_id &&
        $notif->resource &&
        $notif->topic == 'items' &&
        $notif->received &&
        $notif->sent) ||
        Tools::getValue('iid')
    ) {
        if (Tools::getValue('iid')) {
            $id = Tools::getValue('iid');
        } else {
            $resource = $notif->resource;
            $id = str_replace('/items/', '', $resource);
        }
        
        /**
         * Check if ID Changed
         * */
        $nwid = $meli->getProductEdgeID($id);
        $prod = $meli->getProduct($nwid);
        $ps_prod = KcPsDb::getMeliProducts($nwid);
        if ($ps_prod) {
            $id_product = (int) $ps_prod[0]->id_product;
            $repeated = KcPsDb::getNotificationsIdChanged($id_product, $nwid);
            if ($prod->status == 'closed' && !$repeated) {
                KcPsDb::setNotification(
                    'item_closed',
                    "{\"id_product\":\"". (int) $id_product ."\",\"id_meli\":\"". pSQL($nwid) ."\"}"
                );
            }
        }
        if ($id != $nwid) {
            /**
             * Update ID
             * */
            KcPsDb::changeProductsMeliId($id, $nwid);
        }
        
        /** ************************************************************
         * UPDATE FLAG DATA ON PRODUCT
         * (Same changes must be done in import-ps.php)
         * (Same changes must be done in import.php)
         * (This can be used to show extra info on Products List)
         * ************************************************************/
        if ($ps_prod) {
            $pid = $ps_prod[0]->id_product;
            $mid = $nwid;
            $data = Tools::jsonDecode($ps_prod[0]->meli_data);
            
            //Add status flag
            $data->status = pSQL($prod->status);
            //Add quantities flag
            /**
             * [0] = Global Stock
             * [1] to [N] = Variation Stock
             * */
            $data->quantities = array((int) $prod->available_quantity);
            if ($prod->variations) {
                foreach ($prod->variations as $vars) {
                    array_push($data->quantities, (int) $vars->available_quantity);
                }
            }
            
            KcPsDb::updateFlagMeliData($pid, $mid, $data);
        }
        
        KcPsDb::printPre($data);
    }
    
    /**
     * Questions
     * */
    if (
        ($notif->user_id &&
        $notif->resource &&
        $notif->topic == 'questions' &&
        $notif->received &&
        $notif->sent) ||
        Tools::getValue('qid')
    ) {
        
        if (Tools::getValue('qid')) {
            $id = Tools::getValue('qid');
        } else {
            $resource = $notif->resource;
            $id = str_replace('/questions/', '', $resource);
        }

        $id = (int) $id;
        
        $question = $meli->getQuestion($id);
        $item_id = $question->item_id;
        $from = $question->from->id;
        $ps_prod = KcPsDb::getMeliProducts($item_id);
        $user = $meli->getUserInfo($from);
        
        $obj = new stdClass();
        $obj->nickname = pSQL($user->nickname);
        $obj->id_product = (int) $ps_prod[0]->id_product;
        $obj->id_question = (int) $id;
        
        $repeated = KcPsDb::getExistentNotificationQuestion($id);

        if ($ps_prod && !$repeated) {
            KcPsDb::setNotification('question', Tools::jsonEncode($obj));
        }
    }
}
