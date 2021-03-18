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

$internal_access_token = Configuration::get('MERCADOLIBRE_QUESTIONS_TOKEN');
$internal_access_token_req = Tools::getValue('token');

if ($internal_access_token === $internal_access_token_req) {
    
    $appid = Configuration::get('MERCADOLIBRE_APP_ID');
    $secret = Configuration::get('MERCADOLIBRE_APP_SECRET');
    $meli = new KcMeli($appid, $secret);
    
    $mid = Tools::getValue('mid');
    $offset = Tools::getValue('offset');
    $limit = Tools::getValue('limit');
    
    $questions = $meli->getProductQuestions($mid, $offset, $limit);
    
    foreach ($questions->questions as $question) {
        $user_id = $meli->getQuestion($question->id)->from->id;
        $question->user = $meli->getUserInfo($user_id);
    }
    
    echo Tools::jsonEncode($questions);
    
}
