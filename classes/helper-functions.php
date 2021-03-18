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

class HelperFunctions
{
    public static function cleanStringNewLines($str)
    {
        return str_replace(array("\r\n", "\n", "\r"), '\n', $str);
    }
    
    public static function cleanStringHTML($str, $noquotes = false)
    {
        if ($noquotes) {
            return htmlentities($str, ENT_NOQUOTES, 'UTF-8');
        } else {
            return htmlentities($str, ENT_QUOTES, 'UTF-8');
        }
    }
    
    public static function cleanString($str)
    {
        $str = self::cleanStringNewLines($str);
        $str = self::cleanStringHTML($str);
        return $str;
    }
    public static function parseEscapedToJSON($str)
    {
        $str = str_replace('&lt;', '<', $str);
        $str = str_replace('&gt;', '>', $str);
        $str = str_replace('&quot;', '\"', $str);
        $str = str_replace('&amp;', '&', $str);
        return $str;
    }
    public static function uncleanHTMLString($str)
    {
        $str = html_entity_decode($str);
        $str = str_replace('\"', '"', $str);
        return $str;
    }
    public static function parseDefaultMeliDataValue($str)
    {
        $str = self::cleanStringHTML($str);
        $str = self::cleanStringNewLines($str);
        $str = str_replace("\n", '', $str);
        $str = str_replace('\n', '', $str);
        $str = self::parseEscapedToJSON($str);
        return $str;
    }
    
    public static function isHttp()
    {
        $out = false;
        if (isset($_SERVER["HTTPS"])) {
            if ($_SERVER["HTTPS"] == "on") {
                $out = true;
            }
        }
        return $out;
    }
    
    public static function getURL()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    public static function redirect($url = null)
    {
        if (!$url) {
            $url = self::getURL();
        }
        Tools::redirect("Location: $url");
        echo "<script>location.href=$url</script>";
    }
    
    public static function getExternalFileSize($url)
    {
        $out = null;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        curl_close($ch);
        if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
            $out = (int)$matches[1];
        }
        return $out;
    }
}
