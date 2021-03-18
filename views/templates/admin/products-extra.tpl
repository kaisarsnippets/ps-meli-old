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

{if !$m4p->meli->user->nick}
    <div class="alert alert-warning">
        <button class="close" data-dismiss="alert" type="button">×</button>
        {l s='The MercadoLibre module is not configured.' mod='mercadolibre'}
    </div>
{else}
    {if !$m4p->ps->product->meli_data->id}
        <div class="alert alert-info">
            <button class="close" data-dismiss="alert" type="button">×</button>
            {l s='This product is not in MercadoLibre... yet.' mod='mercadolibre'}
        </div>
    {else}
        {if $m4p->meli->item->status == 'closed'}
            <div class="alert alert-warning">
                <button class="close" data-dismiss="alert" type="button">×</button>
                {l s='This publication has finalized. You can relist it if you like.' mod='mercadolibre'}
            </div>
        {/if}
        {if $m4p->meli->item->status == 'paused'}
            <div class="alert alert-info">
                <button class="close" data-dismiss="alert" type="button">×</button>
                {l s='Publication paused.' mod='mercadolibre'}
            </div>
        {/if}
        {if $m4p->meli->item->status=='payment_required'}
            <div class="alert alert-warning">
                <button class="close" data-dismiss="alert" type="button">×</button>
                {l s='This item is waiting for your payment. Pay it first so it can be published.' mod='mercadolibre'}
            </div>
        {/if}
        {if $m4p->meli->item->status=='not_yet_active'}
            <div class="alert alert-warning">
                <button class="close" data-dismiss="alert" type="button">×</button>
                {l s='This product is being activated in MercadoLibre. Please wait a while and reload the page.' mod='mercadolibre'}
            </div>
        {/if}
    {/if}
    
    {if isset($m4p->err_msg)}
        {if isset($m4p->err_msg->error) || isset($m4p->err_msg->message) || isset($m4p->err_msg->cause)}
            {if $m4p->err_msg->error || $m4p->err_msg->message || $m4p->err_msg->cause}
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert" type="button">×</button>
                {if isset($m4p->err_msg->error)}
                    {if $m4p->err_msg->error}
                        <p><b>{$m4p->err_msg->error|escape:'htmlall':'UTF-8'}</b></p>
                    {/if}
                {/if}
                {if isset($m4p->err_msg->message)}
                    {if $m4p->err_msg->message}
                        <p><b>{$m4p->err_msg->message|escape:'htmlall':'UTF-8'}</b></p>
                    {/if}
                {/if}
                {if isset($m4p->err_msg->cause)}
                    {if $m4p->err_msg->cause}
                        <ul>
                            {foreach from = $m4p->err_msg->cause item = cause}
                                <li>{$cause->message|escape:'htmlall':'UTF-8'}</li>
                            {/foreach}
                        </ul>
                    {/if}
                {/if}
            </div>
            {/if}
        {/if}
    {/if}
    
    <!-- Tabs -->
    <ul class="nav nav-tabs">
        <li{if
            $m4p->meli_tab != 'questions' &&
            $m4p->meli_tab != 'info' &&
            $m4p->meli_tab != 'settings'
            } class="active"{/if}><a href="#tab-edit" data-toggle="tab">
            {if $m4p->ps->product->meli_data->id}   
                {l s='Edit' mod='mercadolibre'}
            {else}
                {l s='Create' mod='mercadolibre'}
            {/if}
        </a></li>
        {if $m4p->ps->product->meli_data->id}
            {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!='payment_required'}
                <li{if $m4p->meli_tab == 'questions'} class="active"{/if}><a href="#tab-questions" data-toggle="tab">{l s='Questions' mod='mercadolibre'}</a></li>
            {/if}
        {/if}
        {if $m4p->ps->product->meli_data->id}
            <li{if $m4p->meli_tab == 'info'} class="active"{/if}><a href="#tab-info" data-toggle="tab">{l s='Info' mod='mercadolibre'}</a></li>
        {/if}
        <li{if $m4p->meli_tab == 'settings'} class="active"{/if}><a href="#tab-import" data-toggle="tab">{l s='Settings' mod='mercadolibre'}</a></li>
    </ul>
    
    
    <!-- Edit Product -->
    <div class="panel tabbed-panel">
        <br />
        
        <!-- Edit -->
        <div class="tab-pane{if
        $m4p->meli_tab != 'questions' &&
        $m4p->meli_tab != 'info' &&
        $m4p->meli_tab != 'settings'
        } active{/if}" id="tab-edit">
            
            <div>
                {if $m4p->ps->product->meli_data->id}
                    {if $m4p->meli->item->status == 'closed'}
                        <h3>{l s='Relist MercadoLibre Product' mod='mercadolibre'}</h3>
                    {else}
                        {if $m4p->meli->item->status=='payment_required'}
                        <h3>{l s='Pay the Product so it can be Published' mod='mercadolibre'}</h3>
                        {else}
                        <h3>{l s='Edit MercadoLibre Product' mod='mercadolibre'}</h3>
                        {/if}
                    {/if}
                {else}
                    <h3>{l s='Create MercadoLibre Product' mod='mercadolibre'}</h3>
                {/if}
            </div>
            <div>
                {if $m4p->meli->info->sites->avail}
                    {include './includes/products-extra/tab-edit.tpl'}
                {else}
                    <div class="alert alert-warning">
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        {l s='Sorry. Your currency is not compatible with MercadoLibre. Try selling in USD.' mod='mercadolibre'}
                    </div>
                {/if}
            </div>
            {if $m4p->meli->item->status == 'closed' || $m4p->meli->item->status=='payment_required'}
                {if $m4p->meli->item->status!='payment_required'}
                <hr />
                <br />
                <br />
                <br />
                {/if}
                <div class="panel">
                    <h3>{l s='...Or Delete It' mod='mercadolibre'}</h3>
                    <button class="btn btn-default" type="submit" name="submitAddproductAndStay" value="delete">{l s='Delete' mod='mercadolibre'}</button>
                </div>
            {/if}
        </div>
        
        {if $m4p->ps->product->meli_data->id}
            {if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!='payment_required'}
                <!-- Questions -->
                <div class="tab-pane{if $m4p->meli_tab == 'questions'} active{/if}" id="tab-questions">
                    <div>
                        <h3>{l s='Questions' mod='mercadolibre'}</h3>
                    </div>
                    {include './includes/products-extra/tab-questions.tpl'}
                </div>
            {/if}
        {/if}
        
        <!-- Info -->
        {if $m4p->ps->product->meli_data->id}
            <div class="tab-pane{if $m4p->meli_tab == 'info'} active{/if}" id="tab-info">
                <div>
                    <h3>{$m4p->meli->item->title|escape:'htmlall':'UTF-8'}</h3>
                </div>
                {include './includes/products-extra/tab-info.tpl'}
            </div>
        {/if}
        
        
        <!-- Settings -->
        <div class="tab-pane{if $m4p->meli_tab == 'settings'} active{/if}" id="tab-import">
            {if $m4p->ps->product->meli_data->id}
                <div class="alert alert-info">
                    <button class="close" data-dismiss="alert" type="button">×</button>
                    {l s='Item linked with' mod='mercadolibre'} <b>{$m4p->ps->product->meli_data->id|escape:'htmlall':'UTF-8'}</b>
                </div>
            {/if}
            <div class="panel">
            {include './includes/products-extra/tab-settings.tpl'}
        </div>
        
    </div>
{/if}

<script>
    /**
     * Passing Meli Data to JS
     **/
    m4p.item = '{$m4p->meli->item_json|escape:'htmlall':'UTF-8'}'.replace(/&quot;/g,'"');
    m4p.ps_prod_comb_stock = '{$m4p->ps->product->comb_stock_json|escape:'htmlall':'UTF-8'}'.replace(/&quot;/g,'"');
    
    /**
     * Precompiled Data Object Placeholder
     * this object is compiled and attached
     * as a JSON string in the "#meli-data" hidden input
     * this happens on "setMeliJSON" function
     * that value goes to the backend 
     * */
    var meli_data = jQuery.parseJSON(m4p.item);
    
    var ps_prod_comb_stock = jQuery.parseJSON(m4p.ps_prod_comb_stock);
    
    {if $m4p->meli->item->status == 'closed'}
        meli_data.title = '';
        meli_data.description = '';
        meli_data.warranty = '';
    {/if}
</script>

<!-- <link href="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/css/lib/dropzone.css" rel="stylesheet" type="text/css" media="all" /> -->
<link href="//cdnjs.cloudflare.com/ajax/libs/dropzone/3.8.4/css/dropzone.css" rel="stylesheet" type="text/css" media="all" />
<link href="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/css/admin/products-extra.css" rel="stylesheet" type="text/css" media="all" />
<script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/lib/jquery.livequery.min.js"></script>
<script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/lib/orange-tree.js"></script>
<script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/lib/dropzone.js"></script>
<script>
    Dropzone.autoDiscover = false;
</script>
<script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/admin/products-extra.js"></script>
{if $m4p->meli->item->status != 'closed' && $m4p->meli->item->status!='payment_required'}
    <script src="{$m4p->paths->module|escape:'htmlall':'UTF-8'}/views/js/admin/products-extra/questions.js"></script>
{/if}
