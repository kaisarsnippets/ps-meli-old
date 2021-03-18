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

<input type="hidden" id="meli-data" name="meli-data" value="{$m4p->meli->meli_data|escape:'htmlall':'UTF-8'}" />

<!-- SITE -->
{if !$m4p->meli->item->id}
<div id="meli-item-site-container" class="form-group row">
    <label class="control-label col-lg-2" for="name_1">
        <span class="label-tooltip" title="" data-toggle="tooltip" data-original-title="{l s='MercadoLibre sites that are compatible with your currency' mod='mercadolibre'} ({$m4p->ps->product->currency->iso_code|escape:'htmlall':'UTF-8'})">{l s='Site' mod='mercadolibre'}</span>
    </label>
    <div class="col-lg-10">
        <div class="form-group" style="margin-left:0;">
            <select id="meli-item-site">
                {foreach from=$m4p->meli->info->sites->avail item=site}
                    <option style="background-image:url({$site->flag|escape:'htmlall':'UTF-8'});" value="{$site->id|escape:'htmlall':'UTF-8'}">
                        {$site->name|escape:'htmlall':'UTF-8'}
                    </option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
{/if}

<!-- CATEGORY -->
{if !$m4p->meli->item->id}
<div id="meli-item-category-container" class="form-group row">
    <label class="control-label col-lg-2">
        <span>{l s='Category' mod='mercadolibre'}</span>
    </label>
    <div class="col-lg-10">
        <div id="meli-category-container" class="panel"></div>
    </div>
</div>
{/if}


<div id="meli-data-after-category"{if $m4p->meli->item->id} style="display:block;"{/if}>
    
    <!-- TITLE -->
    {if $m4p->meli->item->sold_quantity == 0}
        {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!='payment_required'}
        <div id="meli-item-title-container" class="form-group row">
            <label class="control-label col-lg-2">
                <span>{l s='Title' mod='mercadolibre'}</span>
            </label>
            <div class="col-lg-10">
                <div class="form-group" style="margin-left:0;">
                    <input 
                    class="form-control" 
                    id="meli-item-title"
                    value="{$m4p->functions->uncleanHTMLString($m4p->meli->item->title)|escape:'htmlall':'UTF-8'}" 
                    placeholder="{l s='e.g: My Product' mod='mercadolibre'}"
                    maxlength="60" />
                </div>
            </div>
        </div>
        {/if}
    {/if}

    <!-- DESCRIPTION -->
    {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!='payment_required'}
        <div id="meli-item-description-container" class="form-group row">
            <label class="control-label col-lg-2">
                <span>{l s='Description' mod='mercadolibre'}</span>
            </label>
            <div class="col-lg-10">
                <div class="form-group" style="margin-left:0;">
                    <textarea class="form-control meli_rte" id="meli-item-description" maxlength="50000">{if isset($m4p->meli->item->description)}{$m4p->functions->uncleanHTMLString($m4p->meli->item->description)|escape:'htmlall':'UTF-8'}{/if}</textarea>
                </div>
            </div>
        </div>
    {/if}
    
    <!-- PRICE -->
    {if $m4p->meli->item->status!='payment_required'}
    <div id="meli-item-price-container" class="form-group row">
        <label class="control-label col-lg-2">
            <span>{l s='Price' mod='mercadolibre'} (<span id="m4p-currency-iso">{$m4p->ps->product->currency->iso_code|escape:'htmlall':'UTF-8'}</span>)</span>
        </label>
        <div class="col-lg-10">
            <div class="form-group" style="margin-left:0;">
                <input 
                class="form-control"
                type="number"
                id="meli-item-price" 
                name="meli-item-price" 
                value="{$m4p->meli->item->price|escape:'htmlall':'UTF-8'}" 
                placeholder="{l s='e.g: 999.99' mod='mercadolibre'}"
                step="0.01"
                />
            </div>
        </div>
    </div>
    {/if}

    <!-- QUANTITY -->
    
    {if $m4p->meli->item->status!='payment_required'}
		{if $m4p->meli->item->buying_mode != 'classified'}
		<div id="meli-item-quantity-container" class="form-group row">
			<label class="control-label col-lg-2">
				<span>{l s='Quantity' mod='mercadolibre'}</span>
			</label>
			<div class="col-lg-10">
				<div class="form-group" style="margin-left:0;">
					<input 
					class="form-control" 
					id="meli-item-quantity" 
					name="meli-item-quantity" 
					value="{$m4p->meli->item->available_quantity|escape:'htmlall':'UTF-8'}" 
					placeholder="{l s='e.g: 999' mod='mercadolibre'}"
					data-ps-quantity="{$m4p->meli->item->available_quantity|escape:'htmlall':'UTF-8'}"
					min="0" />
				</div>
			</div>
		</div>
		{/if}
    {/if}
    
    <!-- IMAGES -->
    {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!='payment_required'}
        <div id="meli-item-images-container" id="meli-images-panel" class="form-group row">
            <label class="control-label col-lg-2">
                <span>{l s='Images' mod='mercadolibre'}</span>
            </label>
            <div class="col-lg-10">
                <div id="meli-item-images-dropzone" class="dropzone empty panel"></div>
            </div>
        </div>
    {/if}
    
    <!-- ATTRIBUTES -->
    {if $m4p->meli->item->status!='payment_required'}
        {if
            ($m4p->meli->item->buying_mode=='classified' && $m4p->meli->item->status!='closed') ||
            ($m4p->meli->item->buying_mode!='classified')
        }
        <div id="meli-item-attributes-container" class="form-group row">
            <label class="control-label col-lg-2">
                <span>{l s='Attributes' mod='mercadolibre'}</span>
            </label>
            <div class="col-lg-10">
                <div id="meli-attributes-content-panel" class="panel">
                    <div id="meli-attributes-content"></div>
                </div>
            </div>
        </div>
        {/if}
    {/if}
    
    <!-- SELLER CONTACT -->
    {if !$m4p->meli->item->id}
    <div id="meli-item-seller-contact-container" class="form-group row">
        <label class="control-label col-lg-2">
            <span>{l s='Seller Contact' mod='mercadolibre'}</span>
        </label>
        <div class="col-lg-10">
            <div  class="panel">
                <div class="form-group row">
                    <label class="control-label col-lg-3">
                        <span>{l s='Phone' mod='mercadolibre'}</span>
                    </label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-5">
                                <input class="form-control" type="text" id="meli-seller-contact-area-code" value="" placeholder="011" />
                            </div>
                            <div class="col-lg-7">
                                <input class="form-control" type="text" id="meli-seller-contact-phone" value="" placeholder="555-5555" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-lg-3">
                        <span>{l s='Email' mod='mercadolibre'}</span>
                    </label>
                    <div class="col-lg-9">
                        <input class="form-control" type="email" id="meli-seller-contact-email" value="" placeholder="my@mail.com" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}
    
    <!-- LOCATION -->
    {if $m4p->meli->item->status!='payment_required' && $m4p->meli->item->status!='closed'}
    <div id="meli-item-location-container" class="form-group row">
        <label class="control-label col-lg-2">
            <span>{l s='Real Estate Location' mod='mercadolibre'}</span>
        </label>
        <div class="col-lg-10">
            <div  class="panel">
                {if !$m4p->meli->item->id}
                <div class="form-group row">
                    <label class="control-label col-lg-3">
                        <span>{l s='Address' mod='mercadolibre'}</span>
                    </label>
                    <div class="col-lg-9">
                        {if isset($m4p->meli->item->location)}
                            <input type="text" id="meli-location-address" value="{$m4p->meli->item->location->address_line|escape:'htmlall':'UTF-8'}" placeholder="{l s='My Address 123' mod='mercadolibre'}" />
                        {else}
                            <input type="text" id="meli-location-address" value="" placeholder="{l s='My Address 123' mod='mercadolibre'}" />
                        {/if}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-lg-3">
                        <span>{l s='Zip / Postal Code' mod='mercadolibre'}</span>
                    </label>
                    <div class="col-lg-9">
                        {if isset($m4p->meli->item->location)}
                            <input type="text" id="meli-location-zip" value="{$m4p->meli->item->location->zip_code|escape:'htmlall':'UTF-8'}" placeholder="4567" />
                        {else}
                            <input type="text" id="meli-location-zip" value="" placeholder="4567" />
                        {/if}
                    </div>
                </div>
                {/if}
                <div class="form-group row">
                    <label class="control-label col-lg-3">
                        <span>{l s='Country' mod='mercadolibre'}</span>
                    </label>
                    <div class="col-lg-9" id="meli-location-country-container">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}
    
    <!-- COVERAGE AREAS -->
    {if $m4p->meli->item->status!='payment_required' && $m4p->meli->item->status!='closed'}
		{if $m4p->meli->item->buying_mode != 'classified'}
		<div id="meli-item-coverage-areas-container" class="form-group row">
			<label class="control-label col-lg-2">
				<span>{l s='Coverage Areas' mod='mercadolibre'}</span>
			</label>
			<div class="col-lg-10">
				<div  class="panel">
					<div class="form-group row">
						<div>
							<button id="meli-coverage-areas-checkall" type="button" class="btn btn-default">{l s='check all' mod='mercadolibre'}</button>
						</div>
						<hr />
						<div class="col-lg-9" id="meli-coverage-areas-select-container">
							
						</div>
					</div>
				</div>
			</div>
		</div>
		{/if}
    {/if}
    
    <!-- STATUS -->
    {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!= 'payment_required' && $m4p->meli->item->status!= ''}
        <div id="meli-item-status-container" class="form-group row">
            <label class="control-label col-lg-2">
                <span>{l s='Status' mod='mercadolibre'}</span>
            </label>
            <div class="col-lg-10">
                <div class="form-group panel" style="margin-left:0;">
                    <p><input{if $m4p->meli->item->status=='active'} checked="checked"{/if} type="radio" id="meli-item-status-active" name="meli-item-status" class="meli-item-status" value="active" /> <label for="meli-item-status-active">{l s='Active' mod='mercadolibre'}</label> </p>
                    <p><input{if $m4p->meli->item->status=='paused'} checked="checked"{/if} type="radio" id="meli-item-status-paused" name="meli-item-status" class="meli-item-status" value="paused" /> <label for="meli-item-status-paused">{l s='Paused' mod='mercadolibre'}</label> </p>
                    <p><input{if $m4p->meli->item->status=='closed'} checked="checked"{/if} type="radio" id="meli-item-status-closed" name="meli-item-status" class="meli-item-status" value="closed" /> <label for="meli-item-status-closed">{l s='Closed' mod='mercadolibre'}</label> </p>
                </div>
            </div>
        </div>
    {/if}

    <!-- LISTING TYPE -->
    {if (!$m4p->meli->item->id || $m4p->meli->item->status == 'closed') && $m4p->meli->item->status!= 'payment_required'}
        <div id="meli-item-listing-type-container" class="form-group row">
            <label class="control-label col-lg-2">
                <span>{l s='Listing Type' mod='mercadolibre'}</span>
            </label>
            <div class="col-lg-10">
                <div class="form-group" style="margin-left:0;">
                    <select id="meli-item-listing-type"></select>
                </div>
            </div>
        </div>
    {/if}

    <!-- BUYING MODE -->
    {if !$m4p->meli->item->id}
        {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!= 'payment_required'}
            <div id="meli-item-buying-mode-container" class="form-group row">
                <label class="control-label col-lg-2">
                    <span>{l s='Buying Mode' mod='mercadolibre'}</span>
                </label>
                <div class="col-lg-10">
                    <div class="form-group" style="margin-left:0;">
                        <select id="meli-item-buying-mode"></select>
                    </div>
                </div>
            </div>
        {/if}
    {/if}
    
    <!-- CONDITION -->
    {if $m4p->meli->item->sold_quantity == 0}
        {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!= 'payment_required'}
            <div id="meli-item-condition-container" class="form-group row">
                <label class="control-label col-lg-2">
                    <span>{l s='Condition' mod='mercadolibre'}</span>
                </label>
                <div class="col-lg-10">
                    <div class="form-group" style="margin-left:0;">
                        {if $m4p->meli->item->id}
                            <p><b id="meli-item-condition-name" style="position:relative; top:7px">{$m4p->meli->item->condition|escape:'htmlall':'UTF-8'}</b></p>
                        {else}
                            <select id="meli-item-condition"></select>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
    {/if}

    <!-- WARRANTY -->
    {if $m4p->meli->item->sold_quantity == 0}
        {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!= 'payment_required'}
            <div id="meli-item-warranty-container" class="form-group row">
                <label class="control-label col-lg-2">
                    <span>{l s='Warranty' mod='mercadolibre'}</span>
                </label>
                <div class="col-lg-10">
                    <div class="form-group" style="margin-left:0;">
                        <input type="text"
                        class="form-control" 
                        id="meli-item-warranty" 
                        name="meli-item-warranty" 
                        placeholder="{l s='e.g: 12 months by Company' mod='mercadolibre'}"
                        value="{$m4p->functions->uncleanHTMLString($m4p->meli->item->warranty)|escape:'htmlall':'UTF-8'}" />
                    </div>
                </div>
            </div>
        {/if}
    {/if}

    <!-- YOUTUBE VIDEO -->
    {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!= 'payment_required'}
        <div id="meli-item-youtube-container" class="form-group row">
            <label class="control-label col-lg-2" for="name_1">
                <span class="label-tooltip" title="" data-toggle="tooltip" data-original-title="{l s='Paste here the full YouTube video URL' mod='mercadolibre'}">{l s='Youtube Video' mod='mercadolibre'}</span>
            </label>
            <div class="col-lg-10">
                <div class="form-group" style="margin-left:0;">
                    <div>
                        <input 
                        class="form-control" 
                        id="meli-item-youtube" 
                        name="meli-item-youtube" 
                        value="{if $m4p->meli->item->video_id}https://www.youtube.com/watch?v={/if}{$m4p->meli->item->video_id|escape:'htmlall':'UTF-8'}" 
                        placeholder="{l s='e.g: https://www.youtube.com/watch?v=0vrdgDdPApQ' mod='mercadolibre'}" />
                    </div>
                    <div id="meli-item-youtube-show">
                        {if $m4p->meli->item->video_id}
                            <br />
                            <iframe width="280" height="280" src="https://www.youtube.com/embed/{$m4p->meli->item->video_id|escape:'htmlall':'UTF-8'}" frameborder="0" allowfullscreen></iframe>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/if}
    
    <!-- AUTOMATIC RELIST -->
    {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!= 'payment_required'}
        
        {if !isset($m4p->meli->item->variations)}
			<div id="meli-item-automatic-relist-container" class="form-group row">
				<label class="control-label col-lg-2">
					<span>{l s='Automatic Relist' mod='mercadolibre'}</span>
				</label>
				<div class="col-lg-10">
					<div class="form-group panel" style="margin-left:0;">
						<p style="margin:0;"><input{if $m4p->meli->item->automatic_relist} checked="checked"{/if} type="checkbox" id="meli-item-automatic-relist" value="1" style="vertical-align: top" /> <label for="meli-item-automatic-relist">{l s='Relist automatically after the product finishes' mod='mercadolibre'}</label> </p>
					</div>
				</div>
			</div>
		{/if}
    
    {/if}
    
</div>

<!-- FOOTER -->
{if $m4p->ps->product->meli_data->id && $m4p->meli->item->status!= 'payment_required'}
    <div class="panel-footer">
        {if $m4p->meli->item->status == 'closed'}
            <button class="btn btn-default pull-right" name="submitAddproductAndStay" value="relist" type="submit">
                <i class="process-icon-save"></i> {l s='Relist' mod='mercadolibre'}
            </button>
        {else}
            <button class="btn btn-default pull-right" name="submitAddproductAndStay" value="update" type="submit">
                <i class="process-icon-save"></i> {l s='Update' mod='mercadolibre'}
            </button>
        {/if}
    </div>
{else}
    {if $m4p->meli->item->status!= 'payment_required'}
        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="submitAddproductAndStay" value="create" type="submit">
                <i class="process-icon-save"></i> {l s='Create' mod='mercadolibre'}
            </button>
        </div>
    {/if}
{/if}
