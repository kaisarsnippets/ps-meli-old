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
if (false) {
    $m4p = new stdClass();
    $cookie = new stdClass();
}

/**
 * Body Start
 * */
$m4p->msg = new stdClass();
$m4p->msg->type = '';
$m4p->msg->text = '';

if (Tools::getValue('m4p-cfg-sbmt') == 'reset') {
    self::resetMeliConfig();
}

if (
Tools::getValue('m4p-app-id') &&
Tools::getValue('m4p-app-key')
) {
    Configuration::updateValue('MERCADOLIBRE_APP_ID', Tools::getValue('m4p-app-id'));
    Configuration::updateValue('MERCADOLIBRE_APP_SECRET', Tools::getValue('m4p-app-key'));
    if (!Configuration::get('MERCADOLIBRE_MODULE_TOKEN')) {
        $m4p->tokens->meli_token = md5(uniqid(rand(), true));
        Configuration::updateValue('MERCADOLIBRE_MODULE_TOKEN', $m4p->tokens->meli_token);
    }
    if (!Configuration::get('MERCADOLIBRE_IMAGES_TOKEN')) {
        $m4p->tokens->images_token = md5(uniqid(rand(), true));
        Configuration::updateValue('MERCADOLIBRE_IMAGES_TOKEN', $m4p->tokens->images_token);
    }
    if (!Configuration::get('MERCADOLIBRE_QUESTIONS_TOKEN')) {
        $m4p->tokens->questions_token = md5(uniqid(rand(), true));
        Configuration::updateValue('MERCADOLIBRE_QUESTIONS_TOKEN', $m4p->tokens->questions_token);
    }
}

$meli = self::initMeliSDK();
$m4p->meli = new stdClass();
$m4p->meli->user = new stdClass();
$m4p->meli->app = new stdClass();
$m4p->meli->user->nick = '';
$m4p->meli->app->id = '';
if ($meli) {
    $myInfo = $meli->getMyInfo();
    if (isset($myInfo->nickname)) {
        $m4p->meli->user->nick = $meli->getMyInfo()->nickname;
        $m4p->meli->app->id = Configuration::get('MERCADOLIBRE_APP_ID');
        
        /**
         * App login
         * */
        if (Tools::getValue('m4p-cfg-sbmt') == 'submit') {
            $m4p->msg->type = 'success';
            $m4p->msg->text = $m4p->this->l('MercadoLibre App connected successfully.');
        }
        
        /**
         * Module settings
         * */
        if (Tools::getValue('m4p-cfg-sbmt') == 'settings') {
            
            /**
             * Shop selection settings
             * */
            if (Tools::getValue('m4p-target-shop')) {
                $m4p->settings->shop->target_id = Tools::getValue('m4p-target-shop');
            }
            
            /**
             * Stock Upd Settings
             * */
            if (Tools::getValue('m4p-product-auto-upd-stock')) {
                $m4p->settings->product->auto_upd_stock = true;
            } else {
                $m4p->settings->product->auto_upd_stock = false;
            }
            if (Tools::getValue('m4p-product-pause-on-out-of-stock')) {
                $m4p->settings->product->pause_on_out_of_stock = true;
            } else {
                $m4p->settings->product->pause_on_out_of_stock = false;
            }
            
            if (Tools::getValue('m4p-product-getting-out-of-stock')) {
                $m4p->settings->product->notif_getting_out_of_stock = true;
            } else {
                $m4p->settings->product->notif_getting_out_of_stock = false;
            }
            if (Tools::getValue('m4p-product-minimum-out-of-stock')) {
                $notifstock = Tools::getValue('m4p-product-minimum-out-of-stock');
                $m4p->settings->product->minimum_out_of_stock = $notifstock;
            }
            
            if (Tools::getValue('m4p-product-pause-getting-out-of-stock')) {
                $m4p->settings->product->pause_getting_out_of_stock = true;
            } else {
                $m4p->settings->product->pause_getting_out_of_stock = false;
            }
            if (Tools::getValue('m4p-product-pause-minimum-out-of-stock')) {
                $pausestock = Tools::getValue('m4p-product-pause-minimum-out-of-stock');
                $m4p->settings->product->pause_minimum_out_of_stock = $pausestock;
            }
            
            /**
             * Price Upd Settings
             * */
            if (Tools::getValue('m4p-product-auto-upd-price')) {
                $m4p->settings->product->auto_upd_price = true;
            } else {
                $m4p->settings->product->auto_upd_price = false;
            }
            if (Tools::getValue('m4p-product-auto-upd-price-tax')) {
                $m4p->settings->product->auto_upd_price_tax = true;
            } else {
                $m4p->settings->product->auto_upd_price_tax = false;
            }
            if (Tools::getValue('m4p-product-auto-upd-price-add-perc')) {
                $perc = (float) Tools::getValue('m4p-product-auto-upd-price-add-perc');
                $perc = number_format($perc, 2, '.', '');
                $m4p->settings->product->auto_upd_price_add_perc = $perc;
            } else {
                $m4p->settings->product->auto_upd_price_add_perc = 0;
            }
            
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
            
            $m4p->msg->type = 'success';
            $m4p->msg->text = $m4p->this->l('Settings updated.');
        }
        
        $m4p->settings = Tools::jsonDecode(Configuration::get('MERCADOLIBRE_MODULE_SETTINGS'));
        
    } else {
        self::resetMeliConfig();
        if (Tools::getValue('m4p-cfg-sbmt')) {
            $m4p->msg->type = 'danger';
            $m4p->msg->text = $m4p->this->l('Couldn\'t connect. Check your credentials');
        }
    }
} else {
    if (Tools::getValue('m4p-cfg-sbmt') == 'reset') {
        $m4p->msg->type = 'success';
        $m4p->msg->text = $m4p->this->l('You are disconnected now.');
    } else {
        if (Tools::getValue('m4p-cfg-sbmt') == 'submit') {
            $m4p->msg->type = 'danger';
            $m4p->msg->text = $m4p->this->l('Couldn\'t connect. Check your credentials');
        }
    }
    self::resetMeliConfig();
}

$m4p->form = new stdClass();
$m4p->form->sbmt = false;
if (Tools::getValue('m4pcfgsbmt')) {
    $m4p->form->sbmt = true;
}
