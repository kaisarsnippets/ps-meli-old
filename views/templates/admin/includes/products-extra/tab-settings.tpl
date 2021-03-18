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

<div class="panel">
	<h3><span class="icon-link"></span> {l s='Link with Existing Item' mod='mercadolibre'}</h3>
	<div class="form-group row">
		<label class="control-label col-lg-3">
			<span>{l s='ML Item ID' mod='mercadolibre'}</span>
		</label>
		<div class="col-lg-4 ">
			<div class="input-group fixed-width-lg">
				<input type="text" placeholder="{l s='Example:' mod='mercadolibre'} MLA1234" value="{$m4p->ps->product->meli_data->id|escape:'htmlall':'UTF-8'}" class="" name="m4p-item-id" />
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<button class="btn btn-default pull-right" name="submitAddproductAndStay" value="import" type="submit">
			<i class="process-icon-save"></i> {l s='Save' mod='mercadolibre'}
		</button>
	</div>
</div>
<br />
<hr />
<br />
{if $m4p->ps->product->meli_data->id}
<div class="panel">
	<h3><span class="icon-unlink"></span> {l s='Unlink Product' mod='mercadolibre'}</h3>
	<p>
		<button class="btn btn-default" name="submitAddproductAndStay" value="remove" type="submit">{l s='Disconnect Product from MercadoLibre' mod='mercadolibre'}</button>
	</p>
</div>
{/if}
