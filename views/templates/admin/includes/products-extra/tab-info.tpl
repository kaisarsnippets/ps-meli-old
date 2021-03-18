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

<!-- TITLE -->
<div class="row">
    <div class="col-lg-1">
        <img src="{$m4p->meli->item->secure_thumbnail|escape:'htmlall':'UTF-8'}" alt="" width="65" />
    </div>
    <div class="col-lg-11">
        <ul>
            <li><b>{l s='Site' mod='mercadolibre'}:</b> {$m4p->meli->item->site_id|escape:'htmlall':'UTF-8'}</li>
            <li><b>{l s='Link' mod='mercadolibre'}:</b> <a href="{$m4p->meli->item->permalink|escape:'htmlall':'UTF-8'}" target="_blank">{$m4p->meli->item->permalink|escape:'htmlall':'UTF-8'}</a></li>
            <li>
                <b>{l s='Status' mod='mercadolibre'}:</b>
                {if $m4p->meli->item->status == 'not_yet_active'}<span>{l s='Not Yet Active' mod='mercadolibre'}</span>{/if}
                {if $m4p->meli->item->status == 'active'}<span>{l s='Active' mod='mercadolibre'}</span>{/if}
                {if $m4p->meli->item->status == 'paused'}<span>{l s='Paused' mod='mercadolibre'}</span>{/if}
                {if $m4p->meli->item->status == 'closed'}<span>{l s='Closed' mod='mercadolibre'}</span>{/if}
                {if $m4p->meli->item->status == 'payment_required'}<span>{l s='Payment Required' mod='mercadolibre'}</span>{/if}
            </li>
            <li><b>{l s='Category ID' mod='mercadolibre'}:</b> {$m4p->meli->item->category_id|escape:'htmlall':'UTF-8'}</li>
            <li><b>{l s='Buying Mode' mod='mercadolibre'}:</b> {$m4p->meli->item->buying_mode|escape:'htmlall':'UTF-8'}</li>
            <li><b>{l s='Listing Type' mod='mercadolibre'}:</b> {$m4p->meli->item->listing_type_id|escape:'htmlall':'UTF-8'}</li>
            <li><b>{l s='Price' mod='mercadolibre'}:</b> {$m4p->meli->item->price|escape:'htmlall':'UTF-8'} {$m4p->meli->item->currency_id|escape:'htmlall':'UTF-8'}</li>
            <li><b>{l s='Sold Quantity' mod='mercadolibre'}:</b> {$m4p->meli->item->sold_quantity|escape:'htmlall':'UTF-8'}</li>
            <li><b>{l s='Finalization Date' mod='mercadolibre'}:</b> {$m4p->meli->item->stop_time|escape:'htmlall':'UTF-8'|date_format:"%B %e, %Y, %H:%M:%S"}</li>
        </ul>
    </div>
</div>
