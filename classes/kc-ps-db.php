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

class KcPsDb
{
    /**
    * General get Table method
    * */
    public static function getTable($table, $cond = null)
    {
        $where = '';
        if ($cond) {
            $where = "WHERE ".pSQL($cond);
        }
        $sql = "SELECT * FROM "._DB_PREFIX_.pSQL($table)." $where;";
        $res = Db::getInstance()->executeS($sql);
        $res = KcPsDb::toObject($res);
        return $res;
    }
    /**
    * Get shops
    * */
    public static function getShops()
    {
        return self::getTable('shop');
    }
    public static function getShop($id)
    {
        return self::getTable('shop', 'id_shop = '.(int) $id);
    }
    /**
    * Products
    * */
    public static function getProducts()
    {
        return self::getTable('product');
    }
    public static function getProduct($pid = null)
    {
        if ($pid) {
            $sql = "SELECT * FROM "._DB_PREFIX_."product WHERE id_product = ". (int) $pid;
            $res = Db::getInstance()->ExecuteS($sql);
            if ($res) {
                $res = self::toObject($res);
                return $res[0];
            }
        }
        return null;
    }
    public static function getProductImages($pid, $id_lang)
    {
        if ($pid) {
            $sql = "SELECT *
            FROM `"._DB_PREFIX_."image` i
            LEFT JOIN `"._DB_PREFIX_."image_lang` il ON (i.`id_image` = il.`id_image`)
            WHERE i.`id_product` = ".(int)$pid." AND il.`id_lang` = ".(int)$id_lang."
            ORDER BY i.`position` ASC";
            $res = Db::getInstance()->ExecuteS($sql);
            if ($res) {
                $res = self::toObject($res);
                return $res;
            }
            
        }
        return null;
    }
    public static function getProductStock($pid = null, $shop_id = null)
    {
        $where = "";
        if ($shop_id != null) {
            $shop_id = (int) $shop_id;
            $where = " AND id_shop = $shop_id";
        }
        if ($pid) {
            $sql = "SELECT * FROM "._DB_PREFIX_."stock_available WHERE id_product = ". (int) $pid . pSQL($where);
            $res = Db::getInstance()->ExecuteS($sql);
            if ($res) {
                $res = self::toObject($res);
                return $res;
            }
        }
        return null;
    }
    public static function getMeliProducts($mid = null)
    {
        $out = array();
        if (!$mid) {
            $mid = '.*';
        }
        
        $sql = "SELECT * FROM "._DB_PREFIX_."product WHERE meli_data REGEXP '\"id\":\"". pSQL($mid) ."\"'";
        $res = Db::getInstance()->ExecuteS($sql);
        $out = self::toObject($res);
        return $out;
    }
    
    public static function updateProductCombinationLinks($pid = null, $comb_links = null)
    {
        $out = null;
        if ($pid && $comb_links) {
            $pid = (int) $pid;
            $comb_links = (array) $comb_links;
            $res = self::getProduct($pid);
            $meli_data = Tools::jsonDecode($res->meli_data);
            $meli_data->combination_links = $comb_links;
            $meli_data = Tools::jsonEncode($meli_data);
            $sql = "UPDATE "._DB_PREFIX_."product SET meli_data = '$meli_data' WHERE id_product = ". (int) $pid;
            Db::getInstance()->Execute($sql);
            $out = self::getProduct($pid);
        }
        return $out;
    }
    
    public static function updateProductMeliData($pid = null, $mid = null)
    {
        $out = null;
        if ($pid && $mid) {
            
            $meli_data = new stdClass();
            $meli_data->id = pSQL($mid);
            
            $meli_data = Tools::jsonEncode($meli_data);
            $sql = "UPDATE "._DB_PREFIX_."product SET meli_data = '$meli_data' WHERE id_product = ". (int) $pid;
            
            Db::getInstance()->Execute($sql);
            
            $out = self::getProduct($pid);
        }
        return $out;
    }
    
    public static function updateFlagMeliData($pid = null, $mid = null, $data = null)
    {
        $out = null;
        if ($pid && $mid && $data) {
            
            $pid = (int) $pid;
            $mid = pSQL($mid);
            $data = (object) $data;
            
            $prod = self::getMeliProducts($mid);
            foreach ($prod as $p) {
                $meli_data_s = Tools::jsonDecode($p->meli_data);
                $meli_data = $data;
                foreach ($meli_data_s as $key => $value) {
                    if (!$meli_data->{$key}) {
                        $meli_data->{$key} = $value;
                    }
                }
                $meli_data->id = $mid;
                
                $meli_data = Tools::jsonEncode($meli_data);
                
                $sql = "UPDATE "._DB_PREFIX_."product SET
                meli_data = '$meli_data' WHERE meli_data REGEXP '\"id\":\"". pSQL($mid) ."\"'";
                
                Db::getInstance()->Execute($sql);
            }
        }
        return $out;
    }
    
    public static function changeProductsMeliId($mid = null, $nwmid = null)
    {
        $out = null;
        if ($mid && $nwmid) {
            
            $prod = self::getMeliProducts(pSQL($mid));
            foreach ($prod as $p) {
                
                $meli_data = Tools::jsonDecode($p->meli_data);
                $meli_data->id = pSQL($nwmid);
                
                $meli_data = Tools::jsonEncode($meli_data);
                $sql = "UPDATE "._DB_PREFIX_."product SET
                meli_data = '$meli_data' WHERE meli_data REGEXP '\"id\":\"". pSQL($mid) ."\"'";
                
                Db::getInstance()->Execute($sql);
            }
            $out = self::getProduct($mid);
        }
        return $out;
    }
    
    public static function removeProductMeliData($pid = null)
    {
        $out = null;
        if ($pid) {
            
            $pid = (int) $pid;
            
            $sql = "UPDATE "._DB_PREFIX_."product SET
            meli_data = NULL WHERE id_product = ". (int) $pid;
            
            Db::getInstance()->Execute($sql);
            
            $out = self::getProduct($pid);
        }
        return $out;
    }
    
    public static function setMeliProductExportRow($config, $prods = null)
    {
        /**
         * $config:
         *  $config->meli //kc-meli instance
         *  $config->filters = //filter options 
         * */
        
        $config = (object) $config;
        
        $meli = $config->meli;
        $current = $config->filters[5];
        $next = $current + 1;
        $status_ok = false;
        $missing_ok = true;
        $meli_prod_ids = $meli->getProductIDs(1, $current)->results;
        if (!$prods) {
            $prods = array();
        } else {
            $prods = (array) $prods;
        }
        if ($meli_prod_ids) {
            foreach ($meli_prod_ids as $id) {
                $prd = $meli->getProduct($id);
                if (!isset($prd->error)) {
                    if ($config->filters[0] && $prd->status == 'pending') {
                        $status_ok = true;
                    }
                    if ($config->filters[0] && $prd->status == 'programmed') {
                        $status_ok = true;
                    }
                    if ($config->filters[0] && $prd->status == 'not_yet_active') {
                        $status_ok = true;
                    }
                    if ($config->filters[1] && $prd->status == 'active') {
                        $status_ok = true;
                    }
                    if ($config->filters[2] && $prd->status == 'paused') {
                        $status_ok = true;
                    }
                    if ($config->filters[3] && $prd->status == 'closed') {
                        $status_ok = true;
                    }
                    if ($config->filters[4]) {
                        if (KcPsDb::getMeliProducts($prd->id)) {
                            $missing_ok = false;
                        }
                    }
                    if ($status_ok && $missing_ok) {
                        array_push($prods, $prd);
                    }
                } else {
                    $config->filters[6]++;
                }
            }
        }
        if ($next <= $config->filters[6]) {
            $config->filters[5] = $next;
            $prods = self::setMeliProductExportRow($config, $prods);
        }
        return $prods;
    }
    
    public static function getRecentlyImportedMeliProds()
    {
        $prodsql = "SELECT * FROM "._DB_PREFIX_."product_lang WHERE meta_keywords REGEXP 'ml-.*-ml'";
        $prods = Db::getInstance()->ExecuteS($prodsql);
        $prods = self::toObject($prods);
        return $prods;
    }
    
    /**
     * Notifications
     * */
    public static function setNotification($name, $value)
    {

        $sql = "INSERT INTO `"._DB_PREFIX_."m4p_notifications`
        (`id`, `name`, `value`, `viewed`) VALUES
        (NULL, '". pSQL($name) ."', '". pSQL($value) ."', '0');";
        Db::getInstance()->Execute($sql);
    }
    public static function getNotificationsIdChanged($id_product, $nwid)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."m4p_notifications` WHERE
        value REGEXP '{\"id_product\":\"". (int)$id_product ."\",\"id_meli\":\"". pSQL($nwid) ."\"}'";
        $res = Db::getInstance()->ExecuteS($sql);
        $res = self::toObject($res);
        return $res;
    }
    public static function getExistentNotificationQuestion($id)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."m4p_notifications` WHERE 
        value REGEXP '\"id_question\":". (int) $id ."'";
        $res = Db::getInstance()->ExecuteS($sql);
        $res = self::toObject($res);
        return $res;
    }
    public static function getNotifications($viewed = 'ALL')
    {
        if ($viewed == 'ALL') {
            $sql = "SELECT * FROM `"._DB_PREFIX_."m4p_notifications` ORDER BY id DESC;";
        } else {
            $sql = "SELECT * FROM `"._DB_PREFIX_."m4p_notifications` WHERE
            viewed = ". (int) $viewed ." ORDER BY id DESC;";
        }
        $res = Db::getInstance()->ExecuteS($sql);
        $res = self::toObject($res);
        return $res;
    }
    public static function viewNotification($condition)
    {
        $sql = "UPDATE `"._DB_PREFIX_."m4p_notifications` SET `viewed` = 1 WHERE ". pSQL($condition);
        Db::getInstance()->Execute($sql);
    }
    
    public static function deleteOldNotifications($amnt)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."m4p_notifications`
        WHERE `viewed` = '1' ORDER BY id DESC LIMIT ". (int) $amnt .", 9999";
        $res = Db::getInstance()->ExecuteS($sql);
        $res = self::toObject($res);
        $arr = array();
        foreach ($res as $itm) {
            array_push($arr, $itm->id);
        }
        if (count($arr) > 0) {
            $ids = implode(',', $arr);
            $sql = "DELETE FROM `"._DB_PREFIX_."m4p_notifications` WHERE id IN (". pSQL($ids) .")";
            Db::getInstance()->Execute($sql);
        }
    }

    /**
    * Helper Methods
    * */

    public static function installDB()
    {
        $sql = array(
            "ALTER TABLE `"._DB_PREFIX_."product` ADD `meli_data` TEXT NULL",
            "ALTER TABLE `"._DB_PREFIX_."customer` ADD `meli_data` TEXT NULL",
            "ALTER TABLE `"._DB_PREFIX_."orders` ADD `meli_data` TEXT NULL",
            "ALTER TABLE `"._DB_PREFIX_."order_payment` ADD `meli_data` TEXT NULL"
        );
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
        
        $sql = "CREATE TABLE `"._DB_PREFIX_."m4p_notifications`
        ( `id` INT(10) NOT NULL AUTO_INCREMENT ,
        `name` VARCHAR(20) NOT NULL ,
        `value` TEXT NOT NULL ,
        `viewed` INT(1) NOT NULL DEFAULT '0' ,
        PRIMARY KEY (`id`))";
        Db::getInstance()->execute($sql);
        
        return true;
    }

    public static function uninstallDB()
    {
        $sql = array(
            "ALTER TABLE `"._DB_PREFIX_."product` DROP `meli_data`",
            "ALTER TABLE `"._DB_PREFIX_."customer` DROP `meli_data`",
            "ALTER TABLE `"._DB_PREFIX_."orders` DROP `meli_data`",
            "ALTER TABLE `"._DB_PREFIX_."order_payment` DROP `meli_data`",
            "DROP TABLE "._DB_PREFIX_."m4p_notifications"
        );
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
        return true;
    }

    public static function toObject($data)
    {
        $data = Tools::jsonEncode($data);
        $data = Tools::jsonDecode($data);
        return $data;
    }
    
    public static function printPre($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
