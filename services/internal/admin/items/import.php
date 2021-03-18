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
//if (true) {
    
    $appid = Configuration::get('MERCADOLIBRE_APP_ID');
    $secret = Configuration::get('MERCADOLIBRE_APP_SECRET');
    $meli = new KcMeli($appid, $secret);
    
    $pid = Tools::getValue('pid');
    $mid = Tools::getValue('mid');
    
    if ($mid) {
        $currentpsprod = KcPsDb::getMeliProducts($mid);
        if (!$currentpsprod || true) {
            $mprod = $meli->getProduct($mid);
            
            $sub_statuses = $mprod->sub_status;
            $deleted = false;
            foreach ($sub_statuses as $substat) {
                if ($substat == 'deleted') {
                    $deleted = true;
                }
            }
            
            if ($mprod->status == 'closed' && $deleted) {
                
                $psprod = new stdClass();
                $psprod->error = 'item_deleted';
                $psprod->message = "Can't import";
                $causemsg = new stdClass();
                $causemsg->message = 'Item was deleted in MercadoLibre';
                $psprod->cause = array($causemsg);
                
            } else {
                
                if (!isset($mprod->error)) {
                    $psprod = KcPsDb::updateProductMeliData($pid, $mid);

                    /** ********************************************************
                     * UPDATE FLAG DATA ON PRODUCT
                     * (Same changes must be done in notifications.php)
                     * (Same changes must be done in import-ps.php)
                     * (This can be used to show extra info on Products List)
                     * ********************************************************/
                    
                    $data = new stdClass();
                    
                    //Add status flag
                    $data->status = $mprod->status;
                    //Add quantities flag
                    /**
                     * [0] = Global Stock
                     * [1] to [N] = Variation Stock
                     * */
                    $data->quantities = array($mprod->available_quantity);
                    
                    if ($mprod->variations) {
                        foreach ($mprod->variations as $vars) {
                            array_push($data->quantities, $vars->available_quantity);
                        }
                    }
                    
                    KcPsDb::updateFlagMeliData($pid, $mid, $data);

                } else {
                    $psprod = new stdClass();
                    $psprod->error = 'item_not_found';
                    $psprod->message = "Can't import";
                    $causemsg = new stdClass();
                    $causemsg->message = 'Item not found';
                    $psprod->cause = array($causemsg);
                }
                    
            }
            
        } else {
            $psprod = new stdClass();
            $psprod->error = 'item_already_linked';
            $psprod->message = "Can't import";
            $causemsg = new stdClass();
            $causemsg->message = 'Item already linked with product #'.$currentpsprod[0]->id_product;
            $psprod->cause = array($causemsg);
        }
    } else {
        $psprod = new stdClass();
        $psprod->error = 'item_id_required';
        $psprod->message = "Can't import";
        $causemsg = new stdClass();
        $causemsg->message = 'You need to provide an Item ID';
        $psprod->cause = array($causemsg);
    }
    
    echo Tools::jsonEncode($psprod);
}
