{**
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
*}

<script>
    m4p = {
        tokens: {
            images: '{$m4p->tokens->images_token|escape:'htmlall':'UTF-8'}',
            questions: '{$m4p->tokens->questions_token|escape:'htmlall':'UTF-8'}',
            admin: '{$m4p->tokens->admin_token|escape:'htmlall':'UTF-8'}',
            orders: '{$m4p->tokens->orders_token|escape:'htmlall':'UTF-8'}',
            meli: '{$m4p->tokens->meli_token|escape:'htmlall':'UTF-8'}'
        },
        paths:{
            base:'{$m4p->paths->module|escape:'htmlall':'UTF-8'}',
            shop:'{$m4p->paths->shop_uri|escape:'htmlall':'UTF-8'}',
            services_abs: '{$m4p->paths->services_abs|escape:'htmlall':'UTF-8'}',
            admin: '{$m4p->paths->admin_name|escape:'htmlall':'UTF-8'}'
        },
        messages:{
            copiedToClipboard:'{l s='copied to clipboard!' mod='mercadolibre'}',
            addVariation: '{l s='add variation' mod='mercadolibre'}',
            removeVariation: '{l s='remove variation' mod='mercadolibre'}',
            cantRemoveVariation: '{l s='This item must have at least one variation' mod='mercadolibre'}',
            notSet: '{l s='Not Set' mod='mercadolibre'}',
            availQuantity: '{l s='Quantity' mod='mercadolibre'}',
            placeholderQuantity: '{l s='e.g.: 999' mod='mercadolibre'}',
            price: '{l s='Price' mod='mercadolibre'}',
            placeholderPrice: '{l s='e.g.: 999' mod='mercadolibre'}',
            combinationNum: '{l s='Combination #' mod='mercadolibre'}',
            linkWith: '{l s='Link with' mod='mercadolibre'}',
            images: '{l s='Images' mod='mercadolibre'}',
            fileUploadDefault: '{l s='Drop an Image' mod='mercadolibre'}',
            fileUploadDefaultSub: '{l s='(or click)' mod='mercadolibre'}',
            fileUploadRemove: '{l s='remove image' mod='mercadolibre'}',
            youtubeURLError: '{l s='Bad Youtube URL. Be sure to add http:\/\/ at the begining.' mod='mercadolibre'}',
            buying_modes:{
                buy_it_now: '{l s='Buy It Now' mod='mercadolibre'}',
                auction: '{l s='Auction' mod='mercadolibre'}',
                classified: '{l s='Classified' mod='mercadolibre'}'
            },
            conditions:{
                new: '{l s='New' mod='mercadolibre'}',
                used: '{l s='Used' mod='mercadolibre'}',
                not_specified: '{l s='Not Specified' mod='mercadolibre'}'
            },
            variationsNotModifiable: '{l s='Item has bids. The attributes modification is restricted. (You can do this only from the MercadoLibre Control Panel).' mod='mercadolibre'}',
            loadMoreQuestions: '{l s='LOAD MORE QUESTIONS' mod='mercadolibre'}',
            deleteQuestionConfirm: '{l s='Delete Question?' mod='mercadolibre'}',
            sendReply: '{l s='Reply' mod='mercadolibre'}',
            enterReply: '{l s='Your Reply...' mod='mercadolibre'}',
            youReplied: '{l s='You replied:' mod='mercadolibre'}',
            mercadoLibreAlerts: '{l s='MercadoLibre Alerts' mod='mercadolibre'}',
            noNewNotif: '{l s='No new MercadoLibre notifications.' mod='mercadolibre'}',
            youHaveANewQuestion: '{l s='You have a new question from ' mod='mercadolibre'}',
            itemSmallStock: '{l s='One of your MercadoLibre Items is getting Out of Stock.' mod='mercadolibre'}',
            itemOutStock: '{l s='A MercadoLibre Item has been paused, because Product in PrestaShop is Out of Stock.' mod='mercadolibre'}',
            variationOutStock: '{l s='A MercadoLibre Variation stock has been set to 0, because Combination in PrestaShop is Out of Stock.' mod='mercadolibre'}',
            itemClosed: '{l s='A MercadoLibre Item has finalized. ' mod='mercadolibre'}',
            newOrder: '{l s='You have a new Order from MercadoLibre. ' mod='mercadolibre'}',
            checkAll: '{l s='check all' mod='mercadolibre'}',
            goToProduct: '{l s='go to product' mod='mercadolibre'}',
            goToOrder: '{l s='go to orders list' mod='mercadolibre'}'
        }
    }
</script>

<link href="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/css/admin/global.css" rel="stylesheet" type="text/css" media="all" />
<script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/admin/global.js"></script>
