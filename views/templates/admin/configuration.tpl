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

{if $m4p->form->sbmt && !$m4p->meli->user->nick}
    <div class="alert alert-danger">
        <button class="close" data-dismiss="alert" type="button">×</button>
        {l s='The Mercadolibre App credentials are incorrect. Please check your data and try again.' mod='mercadolibre'}
    </div>
{/if}
{if $m4p->msg->type && $m4p->msg->text}
    <div class="alert alert-{$m4p->msg->type|escape:'htmlall':'UTF-8'}">
        <button class="close" data-dismiss="alert" type="button">×</button>
        {$m4p->msg->text|escape:'htmlall':'UTF-8'}
    </div>
{/if}

<div>
    <div>
        
        {if !$m4p->meli->user->nick}
            <div class="panel">
                <h3>
                    <img src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/logo.png" width="16" />
                    <span>{$m4p->this->displayName|escape:'htmlall':'UTF-8'}</span>
                </h3>
                <div id="m4p-panel-config-welcome" class="collapse in">
                    <div class="text-center">
                        <p>{$m4p->this->description_full|escape:'htmlall':'UTF-8'}</p>
                        <p>{l s='Said that, we want to give you a warm welcome to the MercadoLibre Integration Module. We hope this tool can make your business\' administration easier, and boost your productivity to the maximum!' mod='mercadolibre'}</p>
                        <p>{l s='We recommend you to read the documentation before start. Don\'t worry, It\'s really easy and straight forward.' mod='mercadolibre'}</p>
                    </div>
                </div>
            </div>
        {/if}
        
        <form action="{$m4p->paths->config_sbmt|escape:'htmlall':'UTF-8'}" method="post" class="defaultForm form-horizontal">
            
            {if $m4p->meli->user->nick}
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-app" data-toggle="tab">{l s='Application' mod='mercadolibre'}</a></li>
                <li><a href="#tab-settings" data-toggle="tab">{l s='Synchronization' mod='mercadolibre'}</a></li>
                <li><a href="#tab-export" data-toggle="tab">{l s='Item Export' mod='mercadolibre'}</a></li>
            </ul>
            {else}
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-connect" data-toggle="tab">{l s='Connect to your MercadoLibre Application' mod='mercadolibre'}</a></li>
            </ul>
            {/if}
            
            {if $m4p->meli->user->nick}
            <div class="panel tabbed-panel">
                <br />
                
                <!-- APP CONFIG -->
                <div class="tab-pane active" id="tab-app">
                    <div class="panel">
                        <div>{l s='Welcome' mod='mercadolibre'} <b>{$m4p->meli->user->nick|escape:'htmlall':'UTF-8'}</b></div>
                    </div>
                    <div class="panel">
                        <div>
                            <h3><span class="icon-unlock-alt color-link"></span> {l s='Grant Application Access' mod='mercadolibre'}</h3>
                            <p>
                                <a class="btn btn-default"
                                href="http://auth.mercadolibre.com/authorization?redirect_uri=http%3A%2F%2Fstatic.mlstatic.com%2Forg-img%2Fsdk%2Fxd-1.0.4.html&response_type=token&client_id={$m4p->meli->app->id|escape:'htmlall':'UTF-8'}&state=iframe&display=popup&interactive=1" 
                                onclick="javascript:open('http://auth.mercadolibre.com/authorization?redirect_uri=http%3A%2F%2Fstatic.mlstatic.com%2Forg-img%2Fsdk%2Fxd-1.0.4.html&response_type=token&client_id={$m4p->meli->app->id|escape:'htmlall':'UTF-8'}&state=iframe&display=popup&interactive=1','mlallow','width=830,height=249,top=50,left=50');return false;">
                                {l s='Allow Access to the MercadoLibre Application' mod='mercadolibre'}
                                </a>
                            </p>
                            <p><small>{l s='Allows the communication with the MercadoLibre Application.' mod='mercadolibre'}</small></p>
                        </div>
                    </div>
                    
                    <div class="panel">
                        <div>
                            <h3><span class="icon-exchange color-link"></span> {l s='Notification Callback URL' mod='mercadolibre'}</h3>
                            <pre class="highlight"><i id="notif-url">{$m4p->paths->services_abs|escape:'htmlall':'UTF-8'}/external/notifications/notifications.php?token={$m4p->tokens->meli_token|escape:'htmlall':'UTF-8'}</i></pre>
                            <p><button type="button" id="btn-copy-notif-url" class="copy-clip btn btn-default" data-clipboard-target="notif-url">{l s='copy url' mod='mercadolibre'}</button></p>
                            <p><small>{l s='Use this URL as "Notification Callback", in your MercadoLibre App.' mod='mercadolibre'}</small></p>
                        </div>
                    </div>
                    
                    <br />
                    <hr />
                    
                    <div class="panel">
                        <h3><span class="icon-close color-link"></span> {l s='Disconnect from Application' mod='mercadolibre'}</h3>
                        <div class="clearfix">
                            <button class="btn btn-danger pull-right" id="m4p-config-submit" name="m4p-cfg-sbmt" value="reset" type="submit">{l s='Disconnect Module from MercadoLibre' mod='mercadolibre'}</button>
                        </div>
                    </div>
                
                </div>
                
                <!-- SETTINGS -->
                <div class="tab-pane" id="tab-settings">
                    <div class="panel clearfix">
                        
                        <div class="col-sm-2"> <!-- required for floating -->
                            <!-- Nav tabs -->
                            <ul class="nav nav-pills nav-stacked">
                                <li class="active">
                                    <a href="#synch-shop" data-toggle="tab">{l s='Shop' mod='mercadolibre'}</a>
                                </li>
                                <li>
                                    <a href="#synch-price" data-toggle="tab">{l s='Price' mod='mercadolibre'}</a>
                                </li>
                                <li>
                                    <a href="#synch-stock" data-toggle="tab">{l s='Stock' mod='mercadolibre'}</a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-sm-10">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                
                                <div class="tab-pane active" id="synch-shop">
                                    <div class="panel">
                                        <h3>{l s='Shop' mod='mercadolibre'}</h3>
                                        
                                        <p style="margin:0;">
                                            <label for="m4p-target-shop">{l s='Synchronize with Shop:' mod='mercadolibre'}</label>
                                            <select class="form-control" name="m4p-target-shop">
                                                {foreach from = $m4p->shop_list item = shop}
                                                    <option value="{$shop->id_shop|escape:'htmlall':'UTF-8'}"
                                                    {if $shop->id_shop == $m4p->settings->shop->target_id}
                                                    selected = "selected"
                                                    {/if}>
                                                        {$shop->name|escape:'htmlall':'UTF-8'}
                                                    </option>
                                                {/foreach}
                                            </select>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="tab-pane" id="synch-price">
                                    
                                    <div class="panel">
                                        
                                        <h3>{l s='Price' mod='mercadolibre'}</h3>
                                        
                                        <p style="margin:0;">
                                            <input{if $m4p->settings->product->auto_upd_price} checked="checked"{/if} type="checkbox" id="m4p-product-auto-upd-price" name="m4p-product-auto-upd-price" value="1" style="vertical-align: top" />
                                            <label for="m4p-product-auto-upd-price">{l s='Keep price synchronized with PrestaShop product.' mod='mercadolibre'}</label>
                                        </p>
                                        <p style="margin:0;">
                                            <input{if $m4p->settings->product->auto_upd_price_tax} checked="checked"{/if} type="checkbox" id="m4p-product-auto-upd-price-tax" name="m4p-product-auto-upd-price-tax" value="1" style="vertical-align: top" />
                                            <label for="m4p-product-auto-upd-price-tax" style="font-weight:normal;">{l s='Tax Included' mod='mercadolibre'}</label>
                                        </p>
                                        
                                        <hr />
                                        <p>{l s='Add a percentage over total price' mod='mercadolibre'}</p>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <p class="input-group">
                                                    <input 
                                                    class="form-control"
                                                    type="number"
                                                    id="m4p-product-auto-upd-price-add-perc"
                                                    name="m4p-product-auto-upd-price-add-perc"
                                                    value="{$m4p->settings->product->auto_upd_price_add_perc|escape:'htmlall':'UTF-8'}" 
                                                    placeholder="{l s='e.g: 6' mod='mercadolibre'}"
                                                    step="0.01"
                                                    />
                                                    <span class="input-group-addon">%</span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="panel">
                                        <div>
                                            <h3><span class="icon-exchange color-link"></span> {l s='External Synchronization Service' mod='mercadolibre'}</h3>
                                            <pre class="highlight"><i id="synch-url-stock">{$m4p->paths->services_abs|escape:'htmlall':'UTF-8'}/external/updater/update-queue.php?a=10&token={$m4p->tokens->meli_token|escape:'htmlall':'UTF-8'}</i></pre>
                                            <p><button type="button" id="btn-copy-synch-url-stock" class="copy-clip btn btn-default" data-clipboard-target="synch-url-stock">{l s='copy url' mod='mercadolibre'}</button></p>
                                            <p><small>{l s='* You can call this URL externally, to keep your products updated.' mod='mercadolibre'}</small></p>
                                            <p><small>{l s='* This service updates both price and stock.' mod='mercadolibre'}</small></p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <p><strong>{l s='Notice' mod='mercadolibre'}</strong></p>
                                        <p>* {l s='If currencies are different in PrestaShop than in MercadoLibre, prices (in ML) will be converted according to the current exchange rate between them.' mod='mercadolibre'}</p>
                                        <p>* {l s='PrestaShop discounts are not included (Since their conditions are not compatible with MercadoLibre settings).' mod='mercadolibre'}</p>
                                    </div>
                                    
                                </div>
                                
                                <div class="tab-pane" id="synch-stock">
                                    
                                    <div class="panel">
                                        
                                        <h3>{l s='Stock' mod='mercadolibre'}</h3>
                                        
                                        <p style="margin:0;">
                                            <input type="checkbox"
                                            id="m4p-product-auto-upd-stock"
                                            name="m4p-product-auto-upd-stock"
                                            value="1"
                                            {if $m4p->settings->product->auto_upd_stock}checked="checked"{/if}
                                            />
                                            <label for="m4p-product-pause-on-out-of-stock">{l s='Keep stock synchronized with PrestaShop product.' mod='mercadolibre'}</label>
                                        </p>
                                        
                                        <hr />
                                        
                                        <p style="margin:0;">
                                            <input type="checkbox"
                                            id="m4p-product-pause-on-out-of-stock"
                                            name="m4p-product-pause-on-out-of-stock"
                                            value="1"
                                            {if $m4p->settings->product->pause_on_out_of_stock}checked="checked"{/if}
                                            />
                                            <label for="m4p-product-pause-on-out-of-stock">{l s='Pause MercadoLibre publication when PrestaShop product is Out of Stock' mod='mercadolibre'}</label>
                                        </p>
                                        
                                        <hr />
                                        
                                        <p style="margin:0;">
                                            <input type="checkbox"
                                            id="m4p-product-getting-out-of-stock"
                                            name="m4p-product-getting-out-of-stock"
                                            value="1"
                                            {if $m4p->settings->product->notif_getting_out_of_stock}checked="checked"{/if}
                                            /> <label for="m4p-product-getting-out-of-stock">{l s='Notify me when a product in MercadoLibre is getting Out of Stock' mod='mercadolibre'}</label>
                                        </p>
                                        
                                        <p>{l s='Minimum stock amount for notification' mod='mercadolibre'}</p>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <p class="input-group">
                                                    <input type="number"
                                                    class="form-control"
                                                    id="m4p-product-minimum-out-of-stock"
                                                    name="m4p-product-minimum-out-of-stock"
                                                    value="{$m4p->settings->product->minimum_out_of_stock|escape:'htmlall':'UTF-8'}"
                                                    min="1"
                                                    step="0.01"/>
                                                </p>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="panel">
                                        <div>
                                            <h3><span class="icon-exchange color-link"></span> {l s='External Synchronization Service' mod='mercadolibre'}</h3>
                                            <pre class="highlight"><i id="synch-url-price">{$m4p->paths->services_abs|escape:'htmlall':'UTF-8'}/external/updater/update-queue.php?a=10&token={$m4p->tokens->meli_token|escape:'htmlall':'UTF-8'}</i></pre>
                                            <p><button type="button" id="btn-copy-synch-url-price" class="copy-clip btn btn-default" data-clipboard-target="synch-url-price">{l s='copy url' mod='mercadolibre'}</button></p>
                                            <p><small>{l s='* You can call this URL externally, to keep your products updated.' mod='mercadolibre'}</small></p>
                                            <p><small>{l s='* This service updates both price and stock.' mod='mercadolibre'}</small></p>
                                        </div>
                                    </div>
                                    
                                </div>
                            
                            </div>
                        </div>  
                        
                    </div>
                    
                    <div class="panel-footer">
                        <button class="btn btn-default pull-right" name="m4p-cfg-sbmt" value="settings" type="submit">
                            <i class="process-icon-save"></i> {l s='Save' mod='mercadolibre'}
                        </button>
                    </div>
                    
                </div>
                
                <!-- EXPORT ITEMS -->
                <div class="tab-pane" id="tab-export">
                    <div>
                        <h3><span class="icon-cloud-download color-link"></span> {l s='Export MercadoLibre Items (CSV)' mod='mercadolibre'}</h3>
                    </div>
                    <div>
                        <div class="panel">
                            <h4><b>{l s='Filter by status' mod='mercadolibre'}</b></h4>
                            <hr />
                            <div>
                                <p><input checked="checked" type="checkbox" id="meli-export-inactive" value="1" /> <label for="meli-export-inactive" style="float:none; cursor:pointer;">{l s='Export inactive items' mod='mercadolibre'}</label></p>
                                <p><input checked="checked" type="checkbox" id="meli-export-active" value="1" /> <label for="meli-export-active" style="float:none; cursor:pointer;">{l s='Export active items' mod='mercadolibre'}</label></p>
                                <p><input checked="checked" type="checkbox" id="meli-export-paused" value="1" /> <label for="meli-export-paused" style="float:none; cursor:pointer;">{l s='Export paused items' mod='mercadolibre'}</label></p>
                                <p><input checked="checked" type="checkbox" id="meli-export-closed" value="1" /> <label for="meli-export-closed" style="float:none; cursor:pointer;">{l s='Export closed items' mod='mercadolibre'}</label></p>
                                <p><input checked="checked" type="checkbox" id="meli-export-missing" value="1" /> <label for="meli-export-missing" style="float:none; cursor:pointer;">{l s='Just items which are not already in PrestaShop' mod='mercadolibre'}</label></p>
                            </div>
                        </div>
                        <div class="panel">
                            <h4><b>{l s='Select a set of rows' mod='mercadolibre'}</b> ({l s='*required' mod='mercadolibre'})</h4>
                            <hr />
                            <div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <p><b>{l s='From row #:' mod='mercadolibre'}</b></p>
                                    </div>
                                    <div class="col-lg-8">
                                        <p><input class="form-control" type="number" id="meli-show-export-pages-start" value="0" autocomplete="off" placeholder="{l s='example: 0' mod='mercadolibre'}" min="0" max="50" /> <input type="checkbox" checked="checked" id="meli-amnt-autoincrement" /><label for="meli-amnt-autoincrement"> {l s='auto increment:' mod='mercadolibre'}</label></p>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-lg-4">
                                        <p><b>{l s='How many?' mod='mercadolibre'}:</b></p>
                                    </div>
                                    <div class="col-lg-8">
                                        <p><input class="form-control" type="number" id="meli-show-export-pages-amnt" value="10" autocomplete="off" placeholder="{l s='example: 20' mod='mercadolibre'}" min="0" max="50" /></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div>
                                <p><input type="checkbox" id="meli-import-reverse" value="1" /> <label for="meli-import-reverse" style="float:none; cursor:pointer;">{l s='Export in reverse order' mod='mercadolibre'}</label></p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a class="btn btn-default pull-right" id="export-sbmt" href="">
                            <i class="process-icon-save"></i> {l s='Export' mod='mercadolibre'}
                        </a>
                    </div>
                </div>
                
            </div>
            
            {else}
            
            <div class="panel tabbed-panel">
                <div id="tab-content">
                    <br />
                    
                    <div class="tab-pane active" id="tab-connect">
                        
                        <div role="alert" class="alert alert-warning">
                            <strong>{l s='Psst!' mod='mercadolibre'}</strong> {l s='Remember that the following credentials are NOT your mercadolibre user and password, but your application\'s APP ID and Secret Key.' mod='mercadolibre'}
                        </div>
                        
                        <div class="panel">
                            <div class="form-group row">
                                <label class="control-label col-lg-3 required">
                                    <span>{l s='App ID' mod='mercadolibre'}</span>
                                </label>
                                <div class="col-lg-4 ">
                                    <div class="input-group fixed-width-lg">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <input type="text" value="" class="" name="m4p-app-id" required="required" />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">                                    
                                <label class="control-label col-lg-3 required">
                                    <span>{l s='Secret Key' mod='mercadolibre'}</span>
                                </label>
                                <div class="col-lg-4 ">
                                    <div class="input-group fixed-width-lg">
                                        <span class="input-group-addon"><i class="icon-key"></i></span>
                                        <input type="password" value="" class="" name="m4p-app-key" required="required" />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel-footer">
                                <button class="btn btn-default pull-right" name="m4p-cfg-sbmt" value="submit" type="submit">
                                    <i class="process-icon-save"></i> {l s='Save' mod='mercadolibre'}
                                </button>
                            </div>
                            
                        </div>
                        
                        
                    </div>
                </div>
            </div>
            {/if}
            
        </form>
    </div>
</div>


<!--FOOTER-->
<hr />
<footer>
    <div class="text-right">
        <p><small>{l s='powered by' mod='mercadolibre'} <b>KaisarCode</b></small></p>
    </div>
</footer>

<script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/lib/ZeroClipboard.min.js"></script>
<script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/admin/configuration.js"></script>
