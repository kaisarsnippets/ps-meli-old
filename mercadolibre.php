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

class MercadoLibre extends Module
{
    
    public function __construct()
    {
        $this->name = 'mercadolibre';
        $this->tab = 'market_place';
        $this->version = '1.2.2';
        $this->author = 'KaisarCode';
        $this->author_uri = 'http://m4p.kaisarcode.com/';
        $this->need_instance = 0;
        $this->module_key = 'f78f652c0075eabc6d5570148eb9f72a';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MercadoLibre');
        $this->description = $this->l('MeLi - MercadoLibre Integration Module');
        $this->description_full = $this->l('MeLi allows you to integrate ');
        $this->description_full .= $this->l('the MercadoLibre operations within PrestaShop, ');
        $this->description_full .= $this->l('in other words, to manage your MercadoLibre items, ');
        $this->description_full .= $this->l('clients, orders, shippings and questions, ');
        $this->description_full .= $this->l('without leaving your own e-commerce!');
        $this->confirmUninstall = $this->l('Are you sure? You will lose all the data regarding to MercadoLibre.');
        
        $this->registerHook('displayAdminProductsExtra');
        $this->registerHook('actionProductSave');
        $this->registerHook('displayadminOrder');
        $this->registerHook('displayBackOfficeHeader');
        $this->registerHook('actionValidateOrder');
        $this->registerHook('actionProductUpdate');
        $this->registerHook('actionUpdateQuantity');
        
        /**
         * this config vars are reset
         * when the module is disconnected
         * */
        $this->configVars = array(
            'MERCADOLIBRE_APP_ID',
            'MERCADOLIBRE_APP_SECRET',
            'MERCADOLIBRE_IMAGES_TOKEN',
            'MERCADOLIBRE_QUESTIONS_TOKEN',
            'MERCADOLIBRE_MODULE_SETTINGS'
        );
        
        /**
         * this config vars are kept even if
         * the module is disconnected
         * */
        $this->staticConfigVars = array('MERCADOLIBRE_MODULE_TOKEN');
    }
    
    /**
     * Module Hooks
     * */
    public function getContent()
    {
        $output = '';
        $m4p = self::setM4PDefault($this);
        /**
         * *************************************************************
         ** ***********************************************************/
        
        require_once($m4p->paths->includes.'/admin/configuration/body.php');
        
        /**
         * *************************************************************
         * ************************************************************/
        $this->context->smarty->assign('m4p', $m4p);
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/global.tpl');
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configuration.tpl');
        return $output;
    }
    
    /**
     * MercadoLibre Tab
     * */
    public function hookDisplayAdminProductsExtra($params)
    {
        $output = '';
        $m4p = self::setM4PDefault($this);
        /**
         * *************************************************************
         ** ***********************************************************/
        
        require_once($m4p->paths->includes.'/admin/products/products-extra/body.php');
        
        /**
         * *************************************************************
         * ************************************************************/
        $this->context->smarty->assign('m4p', $m4p);
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/global.tpl');
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/products-extra.tpl');
        return $output;
    }
    
    public function hookActionProductSave($params)
    {
        $m4p = self::setM4PDefault($this);
        /**
         * *************************************************************
         ** ***********************************************************/
        
        require_once($m4p->paths->includes.'/admin/products/product-save/body.php');
        
        /**
         * *************************************************************
         ** ***********************************************************/
    }
    
    public function hookActionProductUpdate($params)
    {
         $m4p = self::setM4PDefault($this);
        /**
         * *************************************************************
         ** ***********************************************************/
        
        require_once($m4p->paths->includes.'/admin/products/product-update/body-update.php');
        
        /**
         * *************************************************************
         ** ***********************************************************/
    }
    
    public function hookActionUpdateQuantity($params)
    {
        $m4p = self::setM4PDefault($this);
        /**
         * *************************************************************
         ** ***********************************************************/
        
        require_once($m4p->paths->includes.'/admin/products/product-update/body-quant.php');
        
        /**
         * *************************************************************
         ** ***********************************************************/
    }
    
    public function hookDisplayBackOfficeHeader($params)
    {
        $output = '';
        $m4p = self::setM4PDefault($this);
        /**
         * *************************************************************
         ** ***********************************************************/
        
        require_once($m4p->paths->includes.'/admin/header/body.php');
        
        /**
         * *************************************************************
         ** ***********************************************************/
        $this->context->smarty->assign('m4p', $m4p);
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/global.tpl');
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/header.tpl');
        return $output;
    }
    
    /**
     * Helper Classes
     * */
    public function helperFunctions()
    {
        require_once(dirname(__FILE__) .'/classes/helper-functions.php');
        return new HelperFunctions();
    }
    
    public function initMeliSDK()
    {
        require_once(dirname(__FILE__) .'/classes/kc-meli.php');
        if (
        Configuration::get("MERCADOLIBRE_APP_ID") &&
        Configuration::get("MERCADOLIBRE_APP_SECRET")
       ) {
            $meli = new KcMeli(
                Configuration::get("MERCADOLIBRE_APP_ID"),
                Configuration::get("MERCADOLIBRE_APP_SECRET")
            );
            return $meli;
        }
        return false;
    }
    
    public function initPSDB($meli = null)
    {
        require_once(dirname(__FILE__) .'/classes/kc-ps-db.php');
        $psDB = new KcPsDb();
        if ($meli) {
            $psDB->meli = $meli;
        }
        return $psDB;
    }
    
    /**
     * Configuration Methods
     * */
    public function resetMeliConfig()
    {
        foreach ($this->configVars as $cv) {
             Configuration::updateValue($cv, false);
        }
    }
    
    public function deleteMeliConfig()
    {
        foreach ($this->configVars as $cv) {
             Configuration::deleteByName($cv);
        }
        foreach ($this->staticConfigVars as $scv) {
            Configuration::deleteByName($scv);
        }
    }
    
    public function setM4PDefault($main)
    {
        $m4p = new stdClass();
        $m4p->this = $main;
        
        /**
         * DB Functions
         * */
        $m4p->psdb = self::initPSDB();
        
        /**
         * Helper Functions
         * */
        $m4p->functions = self::helperFunctions();
        
        /**
         * Global Info
         * */
         $m4p->global_info = new stdClass();
         $m4p->global_info->is_http = $m4p->functions->isHttp();
         
        /**
         * Shops Info
         * */
        $m4p->current_shop_id = Shop::getCurrentShop();
        $m4p->shop_list = $m4p->psdb->getShops();
        
        /**
         * Prestashop Data
         * */
        $m4p->ps = new stdClass();
        $m4p->ps->lang_id = $main->context->language->id;
        $m4p->ps->lang = $main->context->language->iso_code;
        
        /**
         * General Tokens
         * */
        $m4p->tokens = new stdClass();
        $m4p->tokens->admin_token = Tools::getAdminTokenLite('AdminProducts');
        $m4p->tokens->orders_token = Tools::getAdminTokenLite('AdminOrders');
        $m4p->tokens->admin_module = Tools::getAdminTokenLite('AdminModules');
        $m4p->tokens->meli_token = '';
        if (Configuration::get('MERCADOLIBRE_MODULE_TOKEN')) {
            $m4p->tokens->meli_token = Configuration::get('MERCADOLIBRE_MODULE_TOKEN');
        }
        $m4p->tokens->images_token = '';
        if (Configuration::get('MERCADOLIBRE_IMAGES_TOKEN')) {
            $m4p->tokens->images_token = Configuration::get('MERCADOLIBRE_IMAGES_TOKEN');
        }
        $m4p->tokens->questions_token = '';
        if (Configuration::get('MERCADOLIBRE_QUESTIONS_TOKEN')) {
            $m4p->tokens->questions_token = Configuration::get('MERCADOLIBRE_QUESTIONS_TOKEN');
        }
        
        /**
         * General Paths
         * */
        $m4p->paths = new stdClass();
        $m4p->paths->root = dirname(__FILE__);
        $m4p->paths->includes =  $m4p->paths->root.'/includes';
        $m4p->paths->shop = rtrim(_PS_BASE_URL_, '/');
        $m4p->paths->shop_uri =  $m4p->paths->shop.rtrim(__PS_BASE_URI__, '/');
        $m4p->paths->module = _MODULE_DIR_.$this->name;
        $m4p->paths->images = $m4p->paths->module.'/views/img';
        $m4p->paths->services = $m4p->paths->module.'/services';
        $m4p->paths->services_abs = $m4p->paths->shop.$m4p->paths->services;
        $m4p->paths->current = AdminController::$currentIndex.
        '&configure='.$this->name.
        '&token='. $m4p->tokens->admin_token;
        $m4p->paths->config_sbmt = AdminController::$currentIndex.
        '&configure='.$this->name.
        '&token='. $m4p->tokens->admin_module;
        $m4p->paths->admin_name = explode('/index.php', $_SERVER['REQUEST_URI']);
        $m4p->paths->admin_name = $m4p->paths->admin_name[0];
        $m4p->paths->admin_name = explode('/', $m4p->paths->admin_name);
        $m4p->paths->admin_name = array_pop($m4p->paths->admin_name);
        
        /**
         * Module Info
         * */
        $m4p->module = new stdClass();
        $m4p->module->author = $this->author;
        $m4p->module->author_uri = $this->author_uri;
        $m4p->module->displayName = $this->displayName;
        $m4p->module->description =  $this->description;
        $m4p->module->description_full = $this->description_full;
        
        /**
         * Settings
         * */
        $m4p->settings = self::bootSettings($m4p);
        
        return $m4p;
    }
    
    public function bootSettings($m4p)
    {
        $m4p->settings = new stdClass();
        $m4p->settings->shop = new stdClass();
        $m4p->settings->product = new stdClass();
        if (!Configuration::get('MERCADOLIBRE_MODULE_SETTINGS')) {
            $settings = Tools::jsonEncode($m4p->settings);
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', $settings);
        } else {
            $m4p->settings = Tools::jsonDecode(Configuration::get('MERCADOLIBRE_MODULE_SETTINGS'));
            if (
                !isset($m4p->settings->shop) ||
                !isset($m4p->settings->product)
            ) {
                Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
            }
        }
        
        /**
         * Shop settings
         * */
        if (!isset($m4p->settings->shop)) {
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        if (!isset($m4p->settings->shop->target_id)) {
            $default_shop = $m4p->shop_list[0];
            $m4p->settings->shop->target_id = $default_shop->id_shop;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        
        /**
         * Product Settings
         * */
        if (!isset($m4p->settings->product)) {
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        
        /**
         * Stock Upd Settings
         * */
        if (!isset($m4p->settings->product->auto_upd_stock)) {
            $m4p->settings->product->auto_upd_stock = false;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        if (!isset($m4p->settings->product->pause_on_out_of_stock)) {
            $m4p->settings->product->pause_on_out_of_stock = true;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        if (!isset($m4p->settings->product->notif_getting_out_of_stock)) {
            $m4p->settings->product->notif_getting_out_of_stock = true;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        if (!isset($m4p->settings->product->minimum_out_of_stock)) {
            $m4p->settings->product->minimum_out_of_stock = 5;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        
        /**
         * Price Upd Settings
         * */
        if (!isset($m4p->settings->product->auto_upd_price)) {
            $m4p->settings->product->auto_upd_price = false;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        if (!isset($m4p->settings->product->auto_upd_price_tax)) {
            $m4p->settings->product->auto_upd_price_tax = true;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        if (!isset($m4p->settings->product->auto_upd_price_add_perc)) {
            $m4p->settings->product->auto_upd_price_add_perc = 0;
            Configuration::updateValue('MERCADOLIBRE_MODULE_SETTINGS', Tools::jsonEncode($m4p->settings));
        }
        
        return $m4p->settings;
    }
    
    /**
     * Installation Methods
     * */
    public function install()
    {
        self::resetMeliConfig();
        self::initPSDB();
        KcPsDb::installDB();

        return parent::install();
    }

    public function uninstall()
    {
        self::deleteMeliConfig();
        self::initPSDB();
        KcPsDb::uninstallDB();
        
        return parent::uninstall();
    }
}
