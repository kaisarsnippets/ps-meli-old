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

class M4POrders
{
    public function __construct()
    {
    }
    public static function processOrder($id, $meli, $cookie)
    {
        
        $module_settings = Tools::jsonDecode(Configuration::get('MERCADOLIBRE_MODULE_SETTINGS'));
        
        $id = (int) $id;
        $meli = (object) $meli;
        $cookie = (object) $cookie;
        
        $mo = $meli->getSellerOrder($id);
        
        /*Verify Currency *************************/
        $melicurrency = $mo->currency_id;
        $currencysql = "SELECT * FROM "._DB_PREFIX_."currency WHERE iso_code='".pSQL($melicurrency)."'";
        $pscurrency = Db::getInstance()->ExecuteS($currencysql);
        $pscurrency = KcPsDb::toObject($pscurrency);
        $id_currency = $pscurrency[0]->id_currency;
        /*Verify Currency *************************/
        
        /*Verify Item *****************************/
        $meli_order_items = $mo->order_items;
        foreach ($meli_order_items as $moi) {
            $item_id = $meli->getProductEdgeID($moi->item->id);
            $ps_items = KcPsDb::getMeliProducts($item_id);
            $meli_order_item = $moi->item;
            
            $ps_items_computed = false;
            foreach ($ps_items as $psi) {
                $ps_item = $psi;
                $meli_item = $meli->getProduct($item_id);
                $id_product = $psi->id_product;
                $product = new Product($id_product);
                $prod_attr_comb = $product->getAttributeCombinations($cookie->id_lang);
                $prod_attr_comb = KcPsDb::toObject($prod_attr_comb);
                
                //Get Product Shop ID
                $product_shop_sql = "SELECT * FROM `"._DB_PREFIX_."product_shop`
                WHERE id_product = ". (int) $id_product;
                $product_shop = Db::getInstance()->ExecuteS($product_shop_sql);
                $product_shop = KcPsDb::toObject($product_shop);
                $product_shop_id = $product_shop[0]->id_shop;
                
                //Get Product Shop Group ID
                $product_shop_group_sql = "SELECT * FROM `"._DB_PREFIX_."shop`
                WHERE id_shop = ". (int) $product_shop_id;
                $product_shop_group = Db::getInstance()->ExecuteS($product_shop_group_sql);
                $product_shop_group = KcPsDb::toObject($product_shop_group);
                $product_shop_group_id = $product_shop_group[0]->id_shop_group;
                
                //Get ps avail stock
                $product_stock_sql = "SELECT * FROM `"._DB_PREFIX_."stock_available`
                WHERE id_product = ". (int) $id_product;
                $product_stock = Db::getInstance()->ExecuteS($product_stock_sql);
                $product_stock = KcPsDb::toObject($product_stock);
                
                $variation_index = 0;
                $comb_link_num = 0;
                
                $ps_product_sql = "SELECT * FROM `"._DB_PREFIX_."product`
                WHERE id_product = ". (int) $id_product;
                $ps_product = Db::getInstance()->ExecuteS($ps_product_sql);
                $ps_product = KcPsDb::toObject($ps_product);
                $meli_data = $ps_product[0]->meli_data;
                $meli_data = Tools::jsonDecode($meli_data);
                if (!isset($meli_data->combination_links)) {
                    $meli_data->combination_links = array();
                }
                
                if (isset($meli_order_item->variation_id)) {
                    $i = 0;
                    $mvi = 0;
                    foreach ($meli_item->variations as $mlv) {
                        if ($mlv->id == $meli_order_item->variation_id) {
                            $mvi = $i;
                        }
                        $i++;
                    }
                    
                    if ($meli_data->combination_links) {
                        $comb_link_num = $meli_data->combination_links[$mvi];
                    }
                    
                    $variation_index = $product_stock[$comb_link_num]->id_product_attribute;
                    
                }
                
                if (isset($product_stock[$comb_link_num])) {
                    
                    /*Get PS Stock*************************************/
                    $ps_quantity_stock = $product_stock[$comb_link_num]->quantity;
                    $ps_general_quantity_stock = $product_stock[0]->quantity;
                    
                    /*Get PS Stock*************************************/
                    
                    /*Get PS Variation*********************************/
                    $ps_variation_id = 0;
                    $ps_product_attribute_name_str = '';
                    if ($variation_index != 0) {
                        $get_variation_id_sql = "SELECT * FROM `"._DB_PREFIX_."product_attribute`
                        WHERE id_product = ". (int) $id_product;
                        $ps_variation_ids = Db::getInstance()->ExecuteS($get_variation_id_sql);
                        $ps_variation_ids = KcPsDb::toObject($ps_variation_ids);
                        $ps_variation_ids_obj = array();
                        foreach ($ps_variation_ids as $psvi) {
                            $ps_variation_ids_obj[$psvi->id_product_attribute] = $psvi;
                        }
                        
                        /**
                         * Set Order Attribute Names
                         * */
                        if (isset($ps_variation_ids_obj[$variation_index])) {
                            
                            $ps_variation_id = $variation_index;
                            
                            foreach ($prod_attr_comb as $ac) {
                                if ($ps_variation_id == $ac->id_product_attribute) {
                                    
                                    $ps_product_attribute_name_str .= '- '.$ac->group_name.': ';
                                    $ps_product_attribute_name_str .= $ac->attribute_name.' ';
                                    
                                }
                            }
                            
                        }
                    }
                    /*Get PS Variation*********************************/
                    
                    /*Get/Set customer*********************************/
                    if (!$ps_items_computed) {
                        $buyer_id = $mo->buyer->id;
                        $customer_sql = "SELECT * FROM `"._DB_PREFIX_."customer`
                        WHERE meli_data REGEXP '\"id\":\"". (int) $buyer_id ."\"'";
                        $ps_customer = Db::getInstance()->ExecuteS($customer_sql);
                        $ps_customer = KcPsDb::toObject($ps_customer);
                        $secure_key = md5(uniqid(rand(), true));
                        $customermeliid = $mo->buyer->id;
                        $customerfirstname = Tools::ucfirst($mo->buyer->first_name);
                        $customerlastname = Tools::ucfirst($mo->buyer->last_name);
                        $customeremail = $mo->buyer->email;
                        $customernickname = $mo->buyer->nickname;
                        $docreatecustgroup = false;
                        if ($ps_customer) {
                            //UPDATE
                            $customer_id = $ps_customer[0]->id_customer;
                            $setcustomersql = "UPDATE `"._DB_PREFIX_."customer` SET
                            `firstname` = '". pSQL($customerfirstname) ."',
                            `lastname` = '". pSQL($customerlastname) ."',
                            `email` = '". pSQL($customeremail) ."'
                            WHERE `id_customer` = ". (int) $customer_id;
                        } else {
                            //CREATE
                            $setcustomersql = "INSERT INTO `"._DB_PREFIX_."customer`
                            (`id_customer`,
                            `id_shop_group`,
                            `id_shop`,
                            `id_gender`,
                            `id_default_group`,
                            `id_lang`, `id_risk`,
                            `company`,
                            `siret`,
                            `ape`,
                            `firstname`,
                            `lastname`,
                            `email`,
                            `passwd`,
                            `last_passwd_gen`,
                            `birthday`,
                            `newsletter`,
                            `ip_registration_newsletter`,
                            `newsletter_date_add`,
                            `optin`,
                            `website`,
                            `outstanding_allow_amount`,
                            `show_public_prices`,
                            `max_payment_days`,
                            `secure_key`,
                            `note`,
                            `active`,
                            `is_guest`,
                            `deleted`,
                            `date_add`,
                            `date_upd`,
                            `meli_data`) VALUES
                            (NULL,
                            ". (int) $product_shop_group_id.",
                            ". (int) $product_shop_id.",
                            1,
                            1,
                            ". (int) $cookie->id_lang .",
                            1,
                            NULL,
                            NULL,
                            NULL,
                            '". pSQL($customerfirstname) ."',
                            '". pSQL($customerlastname) ."',
                            '". pSQL($customeremail) ."',
                            MD5('12345678'),
                            CURRENT_TIMESTAMP,
                            NULL,
                            0,
                            NULL,
                            NULL,
                            0,
                            'http://mercadolibre.com/',
                            0.000000,
                            0,
                            60,
                            '". pSQL($secure_key) ."',
                            NULL,
                            1,
                            0,
                            0,
                            NOW(),
                            NOW(),
                            '{\"id\":\"". (int) $customermeliid ."\"}');";
                            $docreatecustgroup = true;
                        }
                        
                        Db::getInstance()->Execute($setcustomersql);
                        $ps_customer = Db::getInstance()->ExecuteS($customer_sql);
                        $ps_customer = KcPsDb::toObject($ps_customer);
                        
                        $customer_id = $ps_customer[0]->id_customer;
                        
                        /**
                         * Create Customer Group
                         * */
                        if ($docreatecustgroup) {
                            $getgroupssql = "SELECT * FROM `"._DB_PREFIX_."group`";
                            $psgroups = Db::getInstance()->ExecuteS($getgroupssql);
                            $psgroups = KcPsDb::toObject($psgroups);
                            $id_group = $psgroups[0]->id_group;
                            $setcustgroupsql = "INSERT INTO `"._DB_PREFIX_."customer_group` 
                            (`id_customer`, `id_group`) VALUES
                            (". (int) $customer_id .", ". (int )$id_group .");";
                            Db::getInstance()->Execute($setcustgroupsql);
                        }
                    }
                    /*Get/Set customer*********************************/
                    
                    /*Get/Set carrier/shipping*************************/
                    if (!$ps_items_computed) {
                        if ($mo->shipping->status=='to_be_agreed') {
                            $melicarrier = '[ML] To Be Agreed';
                        } else {
                            if ($mo->shipping->shipment_type == 'custom_shipping') {
                                $melicarrier = '[ML] Custom';
                            }
                            if ($mo->shipping->shipment_type == 'shipping') {
                                $melicarrier = '[ML] '.$mo->shipping->shipping_option->name;
                            }
                        }
                        $getpscarriersql = "SELECT * FROM "._DB_PREFIX_."carrier
                        WHERE name='". pSQL($melicarrier) ."'";
                        $pscarrier = Db::getInstance()->ExecuteS($getpscarriersql);
                        $pscarrier = KcPsDb::toObject($pscarrier);

                        if ($pscarrier) {
                            $id_carrier = $pscarrier[0]->id_carrier;
                        } else {
                            $is_free = 1;
                            if (isset($mo->shipping->shipping_option->cost)) {
                                $is_free = 0;
                            }
                            $tmpref = str_pad(rand(0, 999999), 6, "0", STR_PAD_LEFT);
                            $setcarriersql="INSERT INTO `"._DB_PREFIX_."carrier`
                            (`id_carrier`,
                            `id_reference`,
                            `id_tax_rules_group`,
                            `name`,
                            `url`,
                            `active`,
                            `deleted`,
                            `shipping_handling`,
                            `range_behavior`,
                            `is_module`,
                            `is_free`,
                            `shipping_external`,
                            `need_range`,
                            `external_module_name`,
                            `shipping_method`,
                            `position`,
                            `max_width`,
                            `max_height`,
                            `max_depth`,
                            `max_weight`,
                            `grade`) VALUES
                            (NULL,
                            ". pSQL($tmpref) .",
                            0,
                            '". pSQL($melicarrier) ."',
                            '',
                            1,
                            0,
                            1,
                            0,
                            0,
                            ". (int) $is_free .",
                            0,
                            0,
                            '',
                            0,
                            0,
                            0,
                            0,
                            0,
                            '0.000000',
                            0);";
                            Db::getInstance()->Execute($setcarriersql);
                            $getpscarriersql = "SELECT * FROM `"._DB_PREFIX_."carrier`
                            WHERE id_reference = ". pSQL($tmpref);
                            $pscarrier = Db::getInstance()->ExecuteS($getpscarriersql);
                            $pscarrier = KcPsDb::toObject($pscarrier);
                            $id_carrier = $pscarrier[0]->id_carrier;
                            
                            //Updating final carrier reference
                            $pscarrierrefsql = "UPDATE `"._DB_PREFIX_."carrier` SET
                            `id_reference` = ". (int) $id_carrier ."
                            WHERE `id_carrier` = ". (int) $id_carrier;
                            Db::getInstance()->Execute($pscarrierrefsql);
                            
                            //Setting Carrier groups
                            $getgroupssql = "SELECT * FROM `"._DB_PREFIX_."group`";
                            $psgroups = Db::getInstance()->ExecuteS($getgroupssql);
                            $psgroups = KcPsDb::toObject($psgroups);
                            $id_group = $psgroups[0]->id_group;
                            $setcarriergroupsql = "INSERT INTO `"._DB_PREFIX_."carrier_group`
                            (`id_carrier`, `id_group`)
                            VALUES (". (int) $id_carrier .", ". (int) $id_group .");";
                            Db::getInstance()->Execute($setcarriergroupsql);
                            
                            //Setting shop dependant carrier data
                            $getshopsql = "SELECT * FROM `"._DB_PREFIX_."shop`";
                            $psshops = Db::getInstance()->ExecuteS($getshopsql);
                            $psshops = KcPsDb::toObject($psshops);
                            foreach ($psshops as $pss) {
                                $id_shop = $pss->id_shop;
                                
                                //Setting up carrier lang
                                $setcarrierlangsql = "INSERT INTO `"._DB_PREFIX_."carrier_lang`
                                (`id_carrier`, `id_shop`, `id_lang`, `delay`)
                                VALUES (
                                ". (int) $id_carrier .",
                                ". (int) $id_shop .",
                                ". (int) $cookie->id_lang.",
                                '[ML] Standard');";
                                Db::getInstance()->Execute($setcarrierlangsql);
                                
                                //Setting Carrier shops
                                $setcarriershopsql = "INSERT INTO `"._DB_PREFIX_."carrier_shop`
                                (`id_carrier`, `id_shop`)
                                VALUES (
                                ". (int) $id_carrier .",
                                ". (int) $id_shop .");";
                                Db::getInstance()->Execute($setcarriershopsql);
                                
                                //Setting Carrier tax rules group
                                $setcarriertrgsql = "INSERT INTO `"._DB_PREFIX_."carrier_tax_rules_group_shop`
                                (`id_carrier`, `id_tax_rules_group`, `id_shop`)
                                VALUES (
                                ". (int) $id_carrier .",
                                0,
                                ". (int) $id_shop .");";
                                Db::getInstance()->Execute($setcarriertrgsql);
                            }
                            
                            //Getting available delivery zones
                            $getzonessql = "SELECT * FROM `"._DB_PREFIX_."zone`";
                            $pszones = Db::getInstance()->ExecuteS($getzonessql);
                            $pszones = KcPsDb::toObject($pszones);
                            foreach ($pszones as $psz) {
                                //Setting carrier delivery zones
                                $id_zone = $psz->id_zone;
                                $setcarriertrgsql = "INSERT INTO `"._DB_PREFIX_."carrier_zone`
                                (`id_carrier`, `id_zone`)
                                VALUES (
                                ". (int) $id_carrier .",
                                ". (int) $id_zone .");";
                                Db::getInstance()->Execute($setcarriertrgsql);
                            }
                        }
                    }
                    /*Get/Set carrier/shipping*************************/
                    
                    /*Get/Set address**********************************/
                    if (!$ps_items_computed) {
                        @$meliaddress = $mo->shipping->receiver_address;
                        $customerfirstname = $mo->buyer->first_name;
                        $customerlastname = $mo->buyer->last_name;
                        $customeremail = $mo->buyer->email;
                        $customernickname = $mo->buyer->nickname;
                        $stateid = 0;
                        if ($meliaddress) {
                            $addresscountry = $meliaddress->country->id;
                            $postcode = $meliaddress->zip_code;
                            $address1 = $meliaddress->address_line;
                            if ($address1) {
                                $fulladdress = $address1;
                            }
                            if ($meliaddress->city->name) {
                                $city = $meliaddress->city->name;
                            }
                            if ($meliaddress->state->name) {
                                $addressstate = $meliaddress->state->name;
                            }
                        } else {
                            $provaddress = $meli->getUserInfo($mo->buyer->id);
                            $addresscountry = $provaddress->country_id;
                        }
                        
                        if (isset($mo->shipping->receiver_address)) {
                            $ra = $mo->shipping->receiver_address;
                            if ($ra->address_line) {
                                $fulladdress = $ra->address_line;
                            }
                            if ($ra->zip_code) {
                                $postcode = $ra->zip_code;
                            }
                            if ($ra->city) {
                                $city = $ra->city->name;
                            }
                            if ($ra->state) {
                                $addressstate = $ra->state->name;
                                $getstateidsql = "SELECT * FROM `"._DB_PREFIX_."state`
                                WHERE name = '". pSQL($addressstate) ."'";
                                $getstateid = Db::getInstance()->ExecuteS($getstateidsql);
                                if ($getstateid) {
                                    $stateid = $getstateid[0]['id_state'];
                                }
                            }
                            if ($ra->country) {
                                $addresscountry=$ra->country->id;
                            }
                        }
                        
                        $phone = 'NULL';
                        if ($mo->buyer->phone) {
                            $phone = "";
                            if ($mo->buyer->phone->area_code) {
                                $phone .= '('. $mo->buyer->phone->area_code. ') ';
                            }
                            $phone .= $mo->buyer->phone->number;
                            if ($mo->buyer->phone->extension) {
                                $phone .= ' ext:'.$mo->buyer->phone->extension;
                            }
                            $phone .= "";
                        }
                        
                        $dni = '';
                        if ($mo->buyer->billing_info->doc_number) {
                            $dni = $mo->buyer->billing_info->doc_number;
                        }

                        if (isset($addressstate)) {
                            if ($addressstate == 'Capital Federal') {
                                $addressstate = 'Ciudad de Buenos Aires';
                            }
                            $getstateidsql = "SELECT * FROM `"._DB_PREFIX_."state`
                            WHERE name = '". pSQL($addressstate) ."'";
                            $getstateid = Db::getInstance()->ExecuteS($getstateidsql);
                            if ($getstateid) {
                                $stateid = $getstateid[0]['id_state'];
                            }
                        }


                        if (!$fulladdress) {
                            $fulladdress = '-';
                        }
                        if (!$postcode) {
                            $postcode = '-';
                        }
                        if (!$city) {
                            $city = '-';
                        }
                        if (!$dni) {
                            $dni = '-';
                        }

                        
                        $getcountryidsql = "SELECT * FROM `"._DB_PREFIX_."country`
                        WHERE iso_code = '". pSQL($addresscountry) ."'";
                        $countryid = Db::getInstance()->ExecuteS($getcountryidsql);
                        $countryid = KcPsDb::toObject($countryid);
                        foreach ($countryid as $cid) {
                            $meli_country_id = $cid->id_country;
                            
                            //Set address
                            $getpsaddress = "SELECT * FROM `"._DB_PREFIX_."address`
                            WHERE alias = '". pSQL($customernickname) ."'";
                            $psaddress = Db::getInstance()->ExecuteS($getpsaddress);
                            
                            /**
                             * If DNI is on payment, overwrite
                             * It is better to have the payer's DNI even if it's not the
                             * person who did the purchase
                             * */
                            $meli_payments = $mo->payments;
                            foreach ($meli_payments as $meli_payment) {
                                $payment_id = $meli_payment->id;
                                $payment_info = $meli->getPaymentInfo($payment_id);
                                if (isset($payment_info->cardholder->identification->number)) {
                                    $dni = $payment_info->cardholder->identification->number;
                                }
                            }
                            
                            if (!$psaddress) {
                                //CREATE
                                @$setaddresssql = "INSERT INTO `"._DB_PREFIX_."address`
                                (`id_address`,
                                `id_country`,
                                `id_state`,
                                `id_customer`,
                                `id_manufacturer`,
                                `id_supplier`,
                                `id_warehouse`,
                                `alias`,
                                `company`,
                                `lastname`,
                                `firstname`,
                                `address1`,
                                `address2`,
                                `postcode`,
                                `city`,
                                `other`,
                                `phone`,
                                `phone_mobile`,
                                `vat_number`,
                                `dni`,
                                `date_add`,
                                `date_upd`,
                                `active`,
                                `deleted`) VALUES
                                (NULL,
                                ". (int) $meli_country_id .",
                                ". (int) $stateid .",
                                ". (int) $customer_id .",
                                0,
                                0,
                                0,
                                '". pSQL($customernickname) ."',
                                NULL,
                                '". pSQL($customerlastname) ."',
                                '". pSQL($customerfirstname) ."',
                                '". pSQL($fulladdress) ."',
                                NULL,
                                '". pSQL($postcode) ."',
                                '". pSQL($city) ."',
                                NULL,
                                '". pSQL($phone) ."',
                                NULL,
                                NULL,
                                '". pSQL($dni) ."',
                                NOW(),
                                NOW(),
                                1,
                                0);";
                                @Db::getInstance()->Execute($setaddresssql);
                            } else {

                                //UPDATE
                                $psaddress = KcPsDb::toObject($psaddress);
                                foreach ($psaddress as $psa) {
                                    $id_address = $psa->id_address;
                                    @$setaddresssql = "UPDATE `"._DB_PREFIX_."address` SET
                                    id_country = ". (int) $meli_country_id .",
                                    id_state = ". (int) $stateid .",
                                    address1 = '". pSQL($fulladdress) ."',
                                    postcode = '". pSQL($postcode) ."',
                                    city = '". pSQL($city) ."',
                                    phone = '". pSQL($phone) ."',
                                    dni = '". pSQL($dni) ."'
                                    WHERE `id_address` = ". (int) $id_address;
                                    @Db::getInstance()->Execute($setaddresssql);
                                }
                            }
                            $psaddress = Db::getInstance()->ExecuteS($getpsaddress);
                            $psaddress = KcPsDb::toObject($psaddress);
                            $id_address = $psaddress[0]->id_address;
                        }
                    }
                    /*Get/Set address**********************************/
                    
                    /*Get/Set order status*****************************/
                    if (!$ps_items_computed) {
                        $mlorderstatus = $mo->status;
                        $ispaid = false;
                        $pmnt_amnt = 0;
                        $check_approved = true;
                        $some_appr_or_accr = false;
                        
                        /**
                         * Prevent to show status "Cancelled"
                         * if the payment was already approved
                         * (This happens due to user's errors)
                         * */
                        foreach ($mo->payments as $pmnt) {
                            $pmnt_amnt++;
                            $mlorderstatus = $pmnt->status;
                            $ispaid = true;
                            if ($mlorderstatus == 'accredited') {
                                $last_not_cancelled = $mlorderstatus;
                                $check_approved = false;
                                $some_appr_or_accr = true;
                                break;
                            }
                        }
                        $pmnt_amnt = 0;
                        if ($check_approved) {
                            foreach ($mo->payments as $pmnt) {
                                $pmnt_amnt++;
                                $mlorderstatus = $pmnt->status;
                                $ispaid = true;
                                if ($mlorderstatus == 'approved') {
                                    $last_not_cancelled = $mlorderstatus;
                                    $some_appr_or_accr = true;
                                    break;
                                }
                            }
                        }
                        if (
                            $mlorderstatus == 'cancelled' &&
                            $last_not_cancelled &&
                            $some_appr_or_accr &&
                            $pmnt_amnt>1
                        ) {
                            $mlorderstatus = $last_not_cancelled;
                        }
                        
                        //Check if is paid
                        $paidval = 0;
                        if (!$mo->payments) {
                            foreach ($mo->tags as $motags) {
                                if ($motags == 'paid') {
                                    $mlorderstatus = $motags;
                                    $ispaid = true;
                                    $paidval = 1;
                                }
                            }
                        }
                        
                        //Set "cancelled" flag
                        $is_cancelled = false;
                        if ($mlorderstatus == 'cancelled') {
                            $is_cancelled = true;
                        }
                        
                        //Set order status
                        $mlorderstatus = '[ML] '.$mlorderstatus;
                        
                        //Check saved order status
                        $checkmeliorderstatesql = "SELECT * FROM `"._DB_PREFIX_."order_state`
                        WHERE module_name='". pSQL($mlorderstatus) ."'";
                        $checkmeliorderstate = Db::getInstance()->ExecuteS($checkmeliorderstatesql);
                        if (!$checkmeliorderstate) {
                            $mlcolor = '#F1E200';
                            $setorderstatesql = "INSERT INTO `"._DB_PREFIX_."order_state`
                            (`id_order_state`,
                            `invoice`,
                            `send_email`,
                            `module_name`,
                            `color`,
                            `unremovable`,
                            `hidden`,
                            `logable`,
                            `delivery`,
                            `shipped`,
                            `paid`,
                            `deleted`) VALUES
                            (NULL,
                            NULL,
                            0,
                            '". pSQL($mlorderstatus) ."',
                            '". pSQL($mlcolor) ."',
                            0,
                            0,
                            0,
                            0,
                            0,
                            ". (int) $paidval .",
                            0);";
                            Db::getInstance()->Execute($setorderstatesql);
                            $checkmeliorderstate = Db::getInstance()->ExecuteS($checkmeliorderstatesql);
                        }
                        $checkmeliorderstate = KcPsDb::toObject($checkmeliorderstate);
                        foreach ($checkmeliorderstate as $psos) {
                            $current_state = $psos->id_order_state;
                        }
                    }
                    /*Get/Set order status*****************************/
                    
                    /*Get/Set order status text************************/
                    if (!$ps_items_computed) {
                        $checkmeliorderstatetextsql = "SELECT * FROM `"._DB_PREFIX_."order_state_lang`
                        WHERE id_order_state = ". (int) $current_state;
                        $checkmeliorderstatetext = Db::getInstance()->ExecuteS($checkmeliorderstatetextsql);
                        if (!$checkmeliorderstatetext) {
                            $checkpslangssql = "SELECT * FROM "._DB_PREFIX_."lang";
                            $checkpslangs = Db::getInstance()->ExecuteS($checkpslangssql);
                            foreach ($checkpslangs as $lng) {
                                $id_lang = $lng['id_lang'];
                                $setorderstatetextsql = "INSERT INTO `"._DB_PREFIX_."order_state_lang`
                                (`id_order_state`, `id_lang`, `name`, `template`)
                                VALUES(
                                ". (int) $current_state .",
                                ". (int) $id_lang .",
                                '". pSQL($mlorderstatus) ."',
                                'mercadolibre');";
                                Db::getInstance()->Execute($setorderstatetextsql);
                            }
                        }
                    }
                    /*Get/Set order status text************************/
                    
                    /*Get/Set Dates********************************/
                    if (!$ps_items_computed) {
                        $mldateacreated = strtotime($mo->date_created);
                        $date_add = date('Y-m-d H:i:s', $mldateacreated);
                        $date_upd = $date_add;
                        if (isset($mo->date_last_updated)) {
                            $mldateupd = strtotime($mo->date_last_updated);
                            $date_upd = date('Y-m-d H:i:s', $mldateupd);
                        }
                    }
                    /*Get/Set Dates********************************/
                    
                    /*Get/Set Order *******************************/
                    if (!$ps_items_computed) {
                        
                        $orderref = Order::generateReference();
                        $ps_prod_reference = $ps_item->reference;
                        
                        $meli_order_quantity = (int) $mo->order_items[0]->quantity;
                        $meli_price = $mo->order_items[0]->unit_price;
                        $meli_total_paid = number_format((float) $mo->total_amount, 2, '.', '');
                        $meli_total_paid_tax = $meli_total_paid;
                        if ($mo->total_amount_with_shipping) {
                            $meli_total_paid_tax = number_format((float) $mo->total_amount_with_shipping, 2, '.', '');
                        }
                        
                        $meli_shipping_price=(float)'0.000000';
                        if (isset($mo->shipping->cost)) {
                            $meli_shipping_price = number_format((float) $mo->shipping->cost, 6, '.', '');
                        }
                        
                        $payment = '-';
                        $card_number = '';
                        $card_brand = '-';
                        $meli_payments = $mo->payments;
                        foreach ($meli_payments as $meli_payment) {
                            $payment = $meli_payment->payment_type;
                            $card_number = $meli_payment->card_id;
                            $card_brand = $meli_payment->payment_method_id;
                        }
                        if ($ispaid && $card_brand=='-') {
                            $card_brand='outside_ml';
                        }
                        
                        $ps_order_sql = "SELECT * FROM `"._DB_PREFIX_."orders`
                        WHERE meli_data REGEXP '\"id\":\"". (int) $id ."\"'";
                        $ps_order = Db::getInstance()->ExecuteS($ps_order_sql);
                        $ps_order = KcPsDb::toObject($ps_order);
                        if ($ps_order) {
                            
                            //UPDATE
                            $new_order = false;
                            $id_order = $ps_order[0]->id_order;
                            $insertordersql="UPDATE `"._DB_PREFIX_."orders` SET
                            `id_shop_group` = ". (int) $product_shop_group_id .",
                            `id_shop` = ". (int) $product_shop_id .",
                            `id_carrier` = ". (int) $id_carrier .",
                            `id_customer` = ". (int) $customer_id .",
                            `id_currency` = ". (int) $id_currency .",
                            `id_address_delivery` = ". (int) $id_address .",
                            `id_address_invoice` = ". (int) $id_address .",
                            `current_state` = ". (int) $current_state .",
                            `payment` = '". pSQL($card_brand) ."',
                            `total_paid` = ". pSQL($meli_total_paid) .",
                            `total_paid_tax_incl` = ". pSQL($meli_total_paid) .",
                            `total_paid_tax_excl` = ". pSQL($meli_total_paid) .",
                            `total_products` = ". pSQL($meli_total_paid) .",
                            `total_products_wt` = ". pSQL($meli_total_paid) .",
                            `total_shipping` = ". pSQL($meli_shipping_price) .",
                            `total_shipping_tax_incl` = ". pSQL($meli_shipping_price) .",
                            `total_shipping_tax_excl` = ". pSQL($meli_shipping_price) .",
                            `date_upd` = '". pSQL($date_upd) ."'
                            WHERE `id_order` = ". (int) $id_order;
                            
                        } else {
                            //CREATE
                            $new_order = true;
                            $insertordersql = "INSERT INTO `"._DB_PREFIX_."orders`
                            (`id_order`,
                            `reference`,
                            `id_shop_group`,
                            `id_shop`,
                            `id_carrier`,
                            `id_lang`,
                            `id_customer`,
                            `id_cart`,
                            `id_currency`,
                            `id_address_delivery`,
                            `id_address_invoice`,
                            `current_state`,
                            `secure_key`,
                            `payment`,
                            `conversion_rate`,
                            `module`,
                            `recyclable`,
                            `gift`,
                            `gift_message`,
                            `mobile_theme`,
                            `shipping_number`,
                            `total_discounts`,
                            `total_discounts_tax_incl`,
                            `total_discounts_tax_excl`,
                            `total_paid`,
                            `total_paid_tax_incl`,
                            `total_paid_tax_excl`,
                            `total_paid_real`,
                            `total_products`,
                            `total_products_wt`,
                            `total_shipping`,
                            `total_shipping_tax_incl`,
                            `total_shipping_tax_excl`,
                            `carrier_tax_rate`,
                            `total_wrapping`,
                            `total_wrapping_tax_incl`,
                            `total_wrapping_tax_excl`,
                            `invoice_number`,
                            `delivery_number`,
                            `invoice_date`,
                            `delivery_date`,
                            `valid`,
                            `date_add`,
                            `date_upd`,
                            `meli_data`) VALUES
                            (NULL,
                            '". pSQL($orderref) ."',
                            ". (int) $product_shop_group_id.",
                            ". (int) $product_shop_id.",
                            ". (int) $id_carrier .",
                            1,
                            ". (int) $customer_id .",
                            1,
                            ". (int) $id_currency .",
                            ". (int) $id_address .",
                            ". (int) $id_address .",
                            ". (int) $current_state .",
                            '". pSQL($secure_key) ."',
                            '". pSQL($card_brand) ."',
                            1.000000,
                            'mercadolibre',
                            0,
                            0,
                            NULL,
                            0,
                            NULL,
                            0.00,
                            0.00,
                            0.00,
                            ". pSQL($meli_total_paid) .",
                            ". pSQL($meli_total_paid) .",
                            ". pSQL($meli_total_paid) .",
                            ". pSQL($meli_total_paid) .",
                            ". pSQL($meli_total_paid) .",
                            ". pSQL($meli_total_paid) .",
                            0.00,
                            ". pSQL($meli_shipping_price) .",
                            ". pSQL($meli_shipping_price) .",
                            ". pSQL($meli_shipping_price) .",
                            0.00,
                            0.00,
                            0.00,
                            0,
                            0,
                            CURDATE(),
                            CURDATE(),
                            1,
                            '". pSQL($date_add) ."',
                            '". pSQL($date_upd) ."',
                            '{\"id\":\"". (int) $id ."\"}')";
                        }
                        
                        Db::getInstance()->Execute($insertordersql);
                        $ps_order = Db::getInstance()->ExecuteS($ps_order_sql);
                        $ps_order = KcPsDb::toObject($ps_order);
                        $id_order = $ps_order[0]->id_order;
                        $order_ref = $ps_order[0]->reference;
                    }
                    
                    /*Get/Set Order *******************************/
                    
                    /*Set Order history ***************************/
                    if (!$ps_items_computed) {
                        if (strpos($mlorderstatus, '[ML]') !== false) {
                            $sethistory = true;
                            $thisdate = date('Y-m-d H:i:s');
                            $lasthistorysql = "SELECT * FROM `"._DB_PREFIX_."order_history`
                            WHERE id_order = ". (int) $id_order ." ORDER BY id_order_history DESC LIMIT 1;";
                            $lasthistory = Db::getInstance()->ExecuteS($lasthistorysql);
                            $lasthistory = KcPsDb::toObject($lasthistory);
                            
                            foreach ($lasthistory as $lh) {
                                $last_state = $lh->id_order_state;
                                if ($current_state == $last_state) {
                                    $sethistory=false;
                                }
                            }
                            if ($sethistory) {
                                $insertorderhistorysql = "INSERT INTO `"._DB_PREFIX_."order_history`
                                (`id_order_history`, `id_employee`, `id_order`, `id_order_state`, `date_add`)
                                VALUES (
                                NULL,
                                1,
                                ". (int) $id_order .",
                                ". (int) $current_state .",
                                '". pSQL($thisdate) ."')";
                                Db::getInstance()->Execute($insertorderhistorysql);
                            }
                        }
                    }
                    /*Set Order history ***************************/
                    
                    /*Get/Set Order Carrier ***********************/
                    if (!$ps_items_computed) {
                        $ps_order_carrier_sql = "SELECT * FROM `"._DB_PREFIX_."order_carrier`
                        WHERE `id_order` = ". (int) $id_order;
                        $ps_order_carrier = Db::getInstance()->ExecuteS($ps_order_carrier_sql);
                        if (isset($mo->shipping->id)) {
                            $shp_id = $mo->shipping->id;
                            $shp = $meli->getShipmentInfo($shp_id);
                            $tracking_number = '';
                            if ($shp->tracking_number) {
                                $tracking_number = $shp->tracking_number;
                            }
                            if ($ps_order_carrier) {
                                //UPDATE
                                $ps_add_order_carrier_sql = "UPDATE `"._DB_PREFIX_."order_carrier` SET
                                `id_order` = ". (int) $id_order .",
                                `id_carrier` = ". (int) $id_carrier .",
                                `shipping_cost_tax_excl` = ". pSQL($meli_shipping_price) .",
                                `shipping_cost_tax_incl` = ". pSQL($meli_shipping_price) .",
                                `tracking_number` = '". pSQL($tracking_number) ."'
                                WHERE `id_order` = ". (int) $id_order;
                            } else {
                                //CREATE
                                $ps_add_order_carrier_sql = "INSERT INTO `"._DB_PREFIX_."order_carrier`
                                (`id_order`,
                                `id_carrier`,
                                `id_order_invoice`,
                                `weight`,
                                `shipping_cost_tax_excl`,
                                `shipping_cost_tax_incl`,
                                `tracking_number`,
                                `date_add`) VALUES(
                                ". (int) $id_order .",
                                ". (int) $id_carrier .",
                                NULL,
                                '0.000000',
                                ". pSQL($meli_shipping_price) .",
                                ". pSQL($meli_shipping_price) .",
                                '". pSQL($tracking_number) ."',
                                NOW());";
                            }
                            Db::getInstance()->Execute($ps_add_order_carrier_sql);
                        } else {
                            if ($ps_order_carrier) {
                                //UPDATE
                                $ps_add_order_carrier_sql = "UPDATE `"._DB_PREFIX_."order_carrier` SET
                                `id_order` = ". (int) $id_order .",
                                `id_carrier` = ". (int) $id_carrier .",
                                `shipping_cost_tax_excl` = ". pSQL($meli_shipping_price) .",
                                `shipping_cost_tax_incl` = ". pSQL($meli_shipping_price) .",
                                `tracking_number` = ''
                                WHERE `id_order` = ". (int) $id_order;
                            } else {
                                //CREATE
                                $ps_add_order_carrier_sql = "INSERT INTO `"._DB_PREFIX_."order_carrier`
                                (`id_order`,
                                `id_carrier`,
                                `id_order_invoice`,
                                `weight`,
                                `shipping_cost_tax_excl`,
                                `shipping_cost_tax_incl`,
                                `tracking_number`,
                                `date_add`) VALUES(
                                ". (int) $id_order .",
                                ". (int) $id_carrier .",
                                NULL,
                                '0.000000',
                                ". pSQL($meli_shipping_price) .",
                                ". pSQL($meli_shipping_price) .",
                                '',
                                NOW());";
                            }
                            Db::getInstance()->Execute($ps_add_order_carrier_sql);
                            
                        }
                    }
                    /*Get/Set Order Carrier ***********************/
                    
                    /*Get/Set Order Detail ************************/
                    if (!$ps_items_computed) {
                        $ps_order_detail_sql = "SELECT * FROM `"._DB_PREFIX_."order_detail`
                        WHERE `id_order` = ". (int) $id_order;
                        $ps_order_detail = Db::getInstance()->ExecuteS($ps_order_detail_sql);
                        $ps_order_detail = KcPsDb::toObject($ps_order_detail);
                        $meli_item_name = $product->name[$cookie->id_lang];
                        
                        foreach ($ps_order_detail as $psod) {
                            $id_order_detail = $psod->id_order_detail;
                        }
                        
                        if ($ps_order_detail) {
                            //UPDATE
                            $ps_add_order_detail_sql = "UPDATE `"._DB_PREFIX_."order_detail` SET 
                            `id_order` = ". (int) $id_order .",
                            `product_id` = ". (int) $id_product .",
                            `product_attribute_id` = ". (int) $ps_variation_id .",
                            `product_name` = '". pSQL($meli_item_name.' '.$ps_product_attribute_name_str) ."',
                            `product_quantity` = ". (int) $meli_order_quantity .",
                            `product_quantity_in_stock` = ". (int) $ps_quantity_stock .",
                            `product_price` = ". pSQL($meli_price) .",
                            `product_reference` = '". pSQL($ps_prod_reference) ."',
                            `total_price_tax_incl` = ". pSQL($meli_total_paid_tax) .",
                            `total_price_tax_excl` = ". pSQL($meli_total_paid) .",
                            `unit_price_tax_incl` = ". pSQL($meli_price) .",
                            `unit_price_tax_excl` = ". pSQL($meli_price) .",
                            `total_shipping_price_tax_incl` = ". pSQL($meli_shipping_price) .",
                            `total_shipping_price_tax_excl` = ". pSQL($meli_shipping_price) .",
                            `original_product_price` = ". pSQL($meli_total_paid_tax) ."
                            WHERE `id_order_detail` = ". (int) $id_order_detail;
                        } else {
                            //CREATE
                            $ps_add_order_detail_sql = "INSERT INTO `"._DB_PREFIX_."order_detail`
                            (`id_order_detail`,
                            `id_order`,
                            `id_order_invoice`,
                            `id_warehouse`,
                            `id_shop`,
                            `product_id`,
                            `product_attribute_id`,
                            `product_name`,
                            `product_quantity`,
                            `product_quantity_in_stock`,
                            `product_quantity_refunded`,
                            `product_quantity_return`,
                            `product_quantity_reinjected`,
                            `product_price`,
                            `reduction_percent`,
                            `reduction_amount`,
                            `reduction_amount_tax_incl`,
                            `reduction_amount_tax_excl`,
                            `group_reduction`,
                            `product_quantity_discount`,
                            `product_ean13`,
                            `product_upc`,
                            `product_reference`,
                            `product_supplier_reference`,
                            `product_weight`,
                            `tax_computation_method`,
                            `tax_name`,
                            `tax_rate`,
                            `ecotax`,
                            `ecotax_tax_rate`,
                            `discount_quantity_applied`,
                            `download_hash`,
                            `download_nb`,
                            `download_deadline`,
                            `total_price_tax_incl`,
                            `total_price_tax_excl`,
                            `unit_price_tax_incl`,
                            `unit_price_tax_excl`,
                            `total_shipping_price_tax_incl`,
                            `total_shipping_price_tax_excl`,
                            `purchase_supplier_price`,
                            `original_product_price`) VALUES
                            (NULL,
                            ". (int) $id_order .",
                            0,
                            0,
                            1,
                            ". (int) $id_product .",
                            ". (int) $ps_variation_id .",
                            '". pSQL($meli_item_name.' '.$ps_product_attribute_name_str) ."',
                            ". (int) $meli_order_quantity .",
                            ". (int) $ps_quantity_stock .",
                            0,
                            0,
                            0,
                            ". pSQL($meli_price) .",
                            0.00, 0.000000,
                            0.000000,
                            0.000000,
                            0.00,
                            0.000000,
                            '',
                            '',
                            '". pSQL($ps_prod_reference) ."',
                            '',
                            0.000000,
                            0,
                            'mercadolibre fee',
                            0.000,
                            0.000000,
                            0.000,
                            0,
                            '',
                            0,
                            '',
                            ". pSQL($meli_total_paid_tax) .",
                            ". pSQL($meli_total_paid) .",
                            ". pSQL($meli_price) .",
                            ". pSQL($meli_price) .",
                            ". pSQL($meli_shipping_price) .",
                            ". pSQL($meli_shipping_price) .",
                            0.000000,
                            ". pSQL($meli_price) .");";
                        }
                        Db::getInstance()->Execute($ps_add_order_detail_sql);
                        $ps_order_detail = Db::getInstance()->ExecuteS($ps_order_detail_sql);
                        $ps_order_detail = KcPsDb::toObject($ps_order_detail);
                    }
                    /*Get/Set Order Detail ************************/
                    
                    /*Get/Set Order Payment ***********************/
                    if (!$ps_items_computed) {
                        $ps_payment_sql = "SELECT * FROM `"._DB_PREFIX_."order_payment`
                        WHERE `order_reference` = '". pSQL($order_ref) ."'";
                        $ps_payment = Db::getInstance()->ExecuteS($ps_payment_sql);
                        $ps_payment = KcPsDb::toObject($ps_payment);
                        if ($payment && $payment!='-') {
                            $meliorderlastupd = date('Y-m-d H:i:s', strtotime($mo->date_created));
                            $paymentmethodstr = $card_brand;
                            $transaction_id=' - ';
                            if (isset($payment_info->transaction_order_id)) {
                                $transaction_id = $payment_info->transaction_order_id;
                            }
                            if ($ps_payment) {
                                //UPDATE
                                foreach ($ps_payment as $psp) {
                                    $id_order_payment = $psp->id_order_payment;
                                }
                                $paymentsql="UPDATE `"._DB_PREFIX_."order_payment` SET
                                `id_currency` = ". (int) $id_currency .",
                                `amount` = ". pSQL($meli_total_paid_tax) .",
                                `payment_method` = '". pSQL($paymentmethodstr) ."',
                                `transaction_id` = '". pSQL($transaction_id) ."',
                                `card_number` = '". pSQL($card_number) ."',
                                `card_brand` = '". pSQL($card_brand) ."',
                                `date_add` = '". pSQL($meliorderlastupd) ."'
                                WHERE`id_order_payment` = ". (int) $id_order_payment;
                            } else {
                                //CREATE
                                $paymentsql="INSERT INTO `"._DB_PREFIX_."order_payment`
                                (`id_order_payment`,
                                `order_reference`,
                                `id_currency`,
                                `amount`,
                                `payment_method`,
                                `conversion_rate`,
                                `transaction_id`,
                                `card_number`,
                                `card_brand`,
                                `date_add`,
                                `meli_data`)
                                VALUES (NULL ,
                                '". pSQL($order_ref) ."',
                                ". (int) $id_currency .",
                                ". pSQL($meli_total_paid_tax) .",
                                '". pSQL($paymentmethodstr) ."',
                                '1.000000',
                                '". pSQL($transaction_id) ."'
                                ,'". pSQL($card_number) ."',
                                '". pSQL($card_brand) ."',
                                '". pSQL($meliorderlastupd) ."',
                                '{\"id\":\"". (int) $payment_id ."\"}');";
                            }
                            Db::getInstance()->Execute($paymentsql);
                        } else {
                            if ($ps_payment) {
                                $deletepaymentsql = "DELETE FROM `"._DB_PREFIX_."orders`
                                WHERE `order_reference` = '". pSQL($orderref) ."'";
                                Db::getInstance()->Execute($deletepaymentsql);
                            }
                        }
                    }
                    /*Get/Set Order Payment ***********************/
                    
                    /*Update Stock on Prestashop ******************/
                    if (!$ps_items_computed) {
                        $general_stock_reduced = $ps_general_quantity_stock - $meli_order_quantity;
                        $stock_reduced = $ps_quantity_stock - $meli_order_quantity;
                        
                        if ($new_order) {
                            
                            KcPsDb::setNotification(
                                'order_new',
                                '{"id_product":"'. (int) $id_product .'",
                                "id_meli":"'. pSQL($meli_data->id) .'"}'
                            );
                            
                            $minimum_stock = $module_settings->product->minimum_out_of_stock;
                            if ($module_settings->product->notif_getting_out_of_stock) {
                                
                                $quant = $meli_data->quantities;
                                $small_stock = false;
                                foreach ($quant as $q) {
                                    if ($q <= $minimum_stock) {
                                        $small_stock = true;
                                        break;
                                    }
                                }
                                if ($small_stock) {
                                    KcPsDb::setNotification(
                                        'item_small_stock',
                                        '{"id_product":"'. (int) $id_product .'",
                                        "id_meli":"'. pSQL($meli_data->id) .'"}'
                                    );
                                }
                            }
                            
                            if ($general_stock_reduced <= 0) {
                                
                                if ($module_settings->product->pause_on_out_of_stock) {
                                    $meli->pauseProduct($meli_data->id);
                                }
                                
                                KcPsDb::setNotification(
                                    'item_out_stock',
                                    '{"id_product":"'. (int) $id_product .'",
                                    "id_meli":"'. pSQL($meli_data->id) .'"}'
                                );
                            } else {
                                $data_upd = new stdClass();
                                if (isset($meli_item->variations)) {
                                    $data_upd->variations = array();
                                }
                                
                                $i=0;
                                foreach ($meli_item->variations as $variation) {
                                    
                                    $variat = new stdClass();
                                    $variat->id = $variation->id;
                                    
                                    if ($stock_reduced <= 0) {
                                        if ($i == $mvi) {
                                            $variat->available_quantity = 0;
                                        } else {
                                            $variat->available_quantity = $variation->available_quantity;
                                        }
                                    } else {
                                        if ($i == $mvi) {
                                            $variat->available_quantity = $stock_reduced;
                                        } else {
                                            $variat->available_quantity = $variation->available_quantity;
                                        }
                                    }
                                    
                                    array_push($data_upd->variations, $variat);
                                    $i++;
                                }
                                $res = $meli->updateProduct($meli_data->id, Tools::jsonEncode($data_upd));
                                $res = Tools::jsonDecode($res);
                                
                                if (isset($res->error)) {
                                    if ($module_settings->product->pause_on_out_of_stock) {
                                        $res = $meli->pauseProduct($meli_data->id);
                                    }
                                    KcPsDb::setNotification(
                                        'item_out_stock',
                                        '{"id_product":"'. (int) $id_product .'",
                                        "id_meli":"'. pSQL($meli_data->id) .'"}'
                                    );
                                }
                                
                            }
                        }
                        
                        if ($new_order) {
                            $reduce_stock_sql = "UPDATE `"._DB_PREFIX_."stock_available` SET 
                            `quantity` = '". (int) $stock_reduced ."' 
                            WHERE id_product = ". (int) $id_product ."
                            AND id_product_attribute = ". (int) $ps_variation_id;
                            Db::getInstance()->Execute($reduce_stock_sql);
                            if ($variation_index != 0) {
                                $reduce_stock_sql = "UPDATE `"._DB_PREFIX_."stock_available` SET 
                                `quantity` = '". (int) $general_stock_reduced ."' 
                                WHERE id_product = ". (int) $id_product ."
                                AND id_product_attribute = 0";
                                Db::getInstance()->Execute($reduce_stock_sql);
                            }
                        } else {
                            /**
                             * Restoring stock
                             * */
                            if ($is_cancelled) {
                                $history = new OrderHistory();
                                $history->id_order = (int) $id_order;
                                $history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $id_order);
                                $history->update();
                            }
                        }
                    }
                    /*Update Stock on Prestashop ******************/
                    
                }
                $ps_items_computed = true;
            }
        }
        /*Verify Item *****************************/
    }
}
