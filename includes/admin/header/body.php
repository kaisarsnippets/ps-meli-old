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
$dummy = false;
if ($dummy) {
    $m4p = new stdClass();
}

$psdb = self::initPSDB();
$m4p->notifications = $psdb->getNotifications();
$m4p->notifications = Tools::jsonEncode($m4p->notifications);

/**
 * Check for recently imported MELI prods
 * */
$url = $m4p->paths->services_abs.
'/internal/admin/items/import-ps.php?token='.$m4p->tokens->meli_token;
$res = Tools::file_get_contents($url);

/**
 * Delete old notifications
 * */
$psdb->deleteOldNotifications(4);
