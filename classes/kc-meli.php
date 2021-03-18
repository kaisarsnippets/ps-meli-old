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

class KcMeli
{
    public function __construct($appId, $secretKey)
    {
        $this->appId = $appId;
        $this->secretKey = $secretKey;
        $accessData = $this->getAccessData();
        $this->accessToken = $accessData->accessToken;
        $this->userId = $accessData->userId;
    }
    public function curlGet($url, $return_object = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($return_object) {
            return Tools::jsonDecode($data);
        } else {
            return $data;
        }
    }
    public function curlPost($url, $data, $isjson = false, $return_object = true)
    {
        $ch = curl_init($url);
        if ($isjson) {
            $dsl = mb_strlen($data, "UTF-8");
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Content-Length: ' . $dsl
                )
            );
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($return_object) {
            return Tools::jsonDecode($output);
        } else {
            return $output;
        }
    }
    public function curlPut($url, $data, $isjson = false, $return_object = true)
    {
        $ch = curl_init($url);
        if ($isjson) {
            $dsl = mb_strlen($data, "UTF-8");
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Content-Length: '. $dsl
                )
            );
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        $output = curl_exec($ch);
        curl_close($ch);
        if ($return_object) {
            return $output;
        } else {
            return Tools::jsonDecode($output);
        }
    }
    private function curlDelete($url, $return_object = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $data = curl_exec($ch);
        curl_close($ch);
        if ($return_object) {
            return $data;
        } else {
            return Tools::jsonDecode($data);
        }
    }
    public function getAccessData($return_object = true)
    {
        $output = new stdClass();
        $DATA_URL = 'https://api.mercadolibre.com/oauth/token';
        $fields = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->appId,
            'client_secret' =>  $this->secretKey
        );
        $output->accessToken = '';
        $output->tokentType = '';
        $output->expiresIn = '';
        $output->scope = '';
        $output->refreshToken = '';
        $output->userId = '';
        
        $authData = $this->curlPost($DATA_URL, $fields);
        if (isset($authData->access_token)) {
            $output->accessToken = $authData->access_token;
        }
        if (isset($authData->token_type)) {
            $output->tokentType = $authData->token_type;
        }
        if (isset($authData->expires_in)) {
            $output->expiresIn = $authData->expires_in;
        }
        if (isset($authData->scope)) {
            $output->scope = $authData->scope;
        }
        if (isset($authData->refresh_token)) {
            $output->refreshToken = $authData->refresh_token;
        }
        if (isset($authData->x_ml_user_id)) {
            $output->userId = $authData->x_ml_user_id;
        } elseif (isset($authData->user_id)) {
            $output->userId = $authData->user_id;
        }
        if ($return_object) {
            return $output;
        } else {
            return Tools::jsonEncode($output);
        }
    }
    
    /*Users ans Apps*/
    public function getUserInfo($user_id, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/users/$user_id";
        return $this->curlGet($url, $return_object);
    }
    public function getMyInfo($return_object = true)
    {
        $user_id = $this->userId;
        $url = "https://api.mercadolibre.com/users/$user_id";
        return $this->curlGet($url, $return_object);
    }
    public function getMyAddress($return_object = true)
    {
        $user_id = $this->userId;
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/users/$user_id/addresses?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function createTestUser($site_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/users/test_user?access_token=$access_token";
        $data = '{"site_id":"'.$site_id.'"}';
        return $this->curlPost($url, $data, true, $return_object);
    }
    
    /*General MELI Info*/
    public function getSites($return_object = true)
    {
        $url = "https://api.mercadolibre.com/sites/";
        return $this->curlGet($url, $return_object);
    }
    public function getSite($code, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/sites/$code";
        return $this->curlGet($url, $return_object);
    }
    public function getCurrencies($return_object = true)
    {
        $url = "https://api.mercadolibre.com/currencies";
        return $this->curlGet($url, $return_object);
    }
    public function getCurrency($code, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/currencies/$code";
        return $this->curlGet($url, $return_object);
    }
    public function getCategories($code, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/sites/$code/categories";
        return $this->curlGet($url, $return_object);
    }
    public function getCategory($cat_id, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/categories/$cat_id";
        return $this->curlGet($url, $return_object);
    }
    public function getAttributes($cat_id, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/categories/$cat_id/attributes";
        return $this->curlGet($url, $return_object);
    }
    public function getSubCategories($code, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/categories/$code";
        $cat = $this->curlGet($url);
        $children = null;
        if ($cat->children_categories) {
            $children = $cat->children_categories;
        }
        if ($return_object) {
            return $children;
        } else {
            return Tools::jsonEncode($children);
        }
    }
    public function getCategoryAttr($code, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/categories/$code/attributes";
        return $this->curlGet($url, $return_object);
    }
    
    /*Listings*/
    public function getProductIDs($limit = 50, $offset = 0, $filter = null, $return_object = true)
    {
        $user_id = $this->userId;
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/users/$user_id/items/search?";
        $url .= "access_token=$access_token&limit=$limit&offset=$offset";
        if ($filter) {
            $url .= '&'.$filter;
        }
        return $this->curlGet($url, $return_object);
    }
    
    public function getProduct($item_id, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $url = "https://api.mercadolibre.com/items/$item_id";
        return $this->curlGet($url, $return_object);
    }
    //Get always the last version of the item's ID
    //using any ID that it could had in the past
    //If a present ID is used, it'll return the same one
    public function getProductEdgeID($item_id)
    {
        $item_id = str_replace('-', '', $item_id);
        $item = $this->getProduct($item_id);
        
        if (isset($item->error)) {
            return false;
        } else {
            $url = $item->permalink;
            $sellerid = $item->seller_id;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $header = "Location: ";
            $pos = strpos($response, $header);
            $pos += Tools::strlen($header);
            $redirect_url = Tools::substr($response, $pos, strpos($response, "\r\n", $pos) - $pos);
            $url = $redirect_url;
            $sliced_id = array();
            preg_match("/\/ML.*?-\d*-/", $url, $sliced_id);
            $nwid = '';
            foreach ($sliced_id as $sid) {
                $nwid = ltrim($sid, '/');
                $nwid = str_replace('-', '', $nwid);
            }
            if ($nwid) {
                $newitem = $this->getProduct($nwid);
                if ($sellerid == $newitem->seller_id) {
                    return $nwid;
                } else {
                    return $item_id;
                }
            } else {
                return $item_id;
            }
        }
    }
    
    public function getProductDescription($item_id, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $url = "https://api.mercadolibre.com/items/$item_id/description";
        return $this->curlGet($url, $return_object);
    }
    public function validateProduct($data, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/validate?access_token=$access_token";
        return $this->curlPost($url, $data, true, $return_object);
    }
    public function listProduct($data, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items?access_token=$access_token";
        return $this->curlPost($url, $data, true, $return_object);
    }
    public function updateProduct($item_id, $data, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id?access_token=$access_token";
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function updateProductDescription($item_id, $text, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id/description?access_token=$access_token";
        $data = '{"text":"'.$text.'"}';
        
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function changeStatusProduct($item_id, $status, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id?access_token=$access_token";
        $data = '{"status":"'.$status.'"}';
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function activateProduct($item_id, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id?access_token=$access_token";
        $data = '{"status":"active"}';
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function pauseProduct($item_id, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id?access_token=$access_token";
        $data = '{"status":"paused"}';
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function closeProduct($item_id, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id?access_token=$access_token";
        $data = '{"status":"closed"}';
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function deleteProduct($item_id, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id?access_token=$access_token";
        $data = '{"deleted":"true"}';
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function relistProduct($item_id, $data, $return_object = true)
    {
        $item_id = str_replace('-', '', $item_id);
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/items/$item_id/relist?access_token=$access_token";
        return $this->curlPost($url, $data, true, $return_object);
    }
    
    /*Orders*/
    public function getSellerOrders($limit = 50, $offset = 0, $sort = "&sort=date_desc", $return_object = true)
    {
        $user_id = $this->userId;
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/search?";
        $url .= "seller=$user_id&access_token=$access_token&limit=$limit&offset=$offset".$sort;
        return $this->curlGet($url, $return_object);
    }
    public function getSellerOrder($order_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/$order_id?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function getSellerOrdersArchived($limit = 50, $offset = 0, $sort = "&sort=date_desc", $return_object = true)
    {
        $user_id = $this->userId;
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/search/archived?";
        $url .= "seller=$user_id&access_token=$access_token&limit=$limit&offset=$offset".$sort;
        return $this->curlGet($url, $return_object);
    }
    public function getSellerOrdersRecent($limit = 50, $offset = 0, $sort = "&sort=date_desc", $return_object = true)
    {
        $user_id = $this->userId;
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/search/recent?";
        $url .= "seller=$user_id&access_token=$access_token&limit=$limit&offset=$offset".$sort;
        return $this->curlGet($url, $return_object);
    }
    public function getSellerOrdersPending($limit = 50, $offset = 0, $sort = "&sort=date_desc", $return_object = true)
    {
        $user_id = $this->userId;
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/search/pending?";
        $url .= "seller=$user_id&access_token=$access_token&limit=$limit&offset=$offset".$sort;
        return $this->curlGet($url, $return_object);
    }
    public function getBuyerOrders($return_object = true)
    {
        $user_id = $this->userId;
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/search?buyer=$user_id&access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function getOrderNotes($order_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/$order_id/notes?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function setOrderNote($order_id, $text_note, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/$order_id/notes?access_token=$access_token";
        $data = '{"note":"'.$text_note.'"}';
        return $this->curlPost($url, $data, true, $return_object);
    }
    public function updateOrderNote($order_id, $note_id, $text_note, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/$order_id/notes/$note_id?access_token=$access_token";
        $data = '{"note":"'.$text_note.'"}';
        return $this->curlPut($url, $data, true, $return_object);
    }
    public function deleteOrderNote($order_id, $note_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/$order_id/notes/$note_id?access_token=$access_token";
        return $this->curlDelete($url, $return_object);
    }
    
    /*Payments*/
    public function getPaymentInfo($payment_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/collections/$payment_id?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function getOperationInfo($payment_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/collections/$payment_id?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    
    /*Shipping*/
    public function getShippingMethods($site_id, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/sites/$site_id/shipping_methods";
        return $this->curlGet($url, $return_object);
    }
    public function getOrderShipment($order_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/orders/$order_id/shipments?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function getShipmentInfo($shipment_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/shipments/$shipment_id?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    
    /*Questions*/
    public function getProductQuestions(
        $item_id,
        $offset = 0,
        $limit = 50,
        $sort = "&sort=date_desc",
        $return_object = true
    ) {
        $access_token = $this->accessToken;
        $item_id = str_replace('-', '', $item_id);
        $url = "https://api.mercadolibre.com/questions/search?";
        $url .= "item_id=$item_id&offset=$offset&limit=$limit$sort&access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function getQuestion($question_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url = "https://api.mercadolibre.com/questions/$question_id?access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function searchItemQuestions($item_id, $user_id = '', $return_object = true)
    {
        $access_token = $this->accessToken;
        $my_id = self::getMyInfo();
        $my_id = $my_id->id;
        if ($user_id) {
            $user_id = "&from_id=".$user_id;
        }
        $url = "https://api.mercadolibre.com/questions/search?
        seller=$my_id&item_id=$item_id".$user_id."&access_token=$access_token";
        return $this->curlGet($url, $return_object);
    }
    public function answerQuestion($question_id, $text, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url="https://api.mercadolibre.com/answers?access_token=$access_token";
        $data = '{"question_id":"'.$question_id.'","text":"'.$text.'"}';
        return $this->curlPost($url, $data, true, $return_object);
    }
    public function deleteQuestion($question_id, $return_object = true)
    {
        $access_token = $this->accessToken;
        $url="https://api.mercadolibre.com/questions/$question_id?access_token=$access_token";
        return $this->curlDelete($url, $return_object);
    }
    
    /*Calculators*/
    public function calcPublicationFee($site_id, $price, $return_object = true)
    {
        $url = "https://api.mercadolibre.com/sites/$site_id/listing_prices?price=$price";
        return $this->curlGet($url, $return_object);
    }
}
