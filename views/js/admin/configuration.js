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

ZeroClipboard.config({
    forceEnhancedClipboard: true,
    swfPath: m4p.paths.base+'/views/js/lib/ZeroClipboard.swf'
});

jQuery(document).ready(function(){
    var client = new ZeroClipboard(jQuery('.copy-clip'));
    client.off('aftercopy');
    client.on('aftercopy',function(){
        alert(m4p.messages.copiedToClipboard);
    });
});

/**
 * Export
 * */
var serial = [
	1,
	1,
	1,
	1,
	1,
	jQuery('#meli-show-export-pages-start').val(),
	jQuery('#meli-show-export-pages-amnt').val(),
	jQuery('#meli-show-export-reverse').val()
	];

function compileSerial() {
    serial_str = serial.join(',');
    var href = m4p.paths.services_abs+'/internal/admin/items/export.php?serial='+serial_str+'&token='+m4p.tokens.meli;
    jQuery('#export-sbmt').attr('href',href);
}
jQuery('#meli-export-inactive').on('change',function(){
    if (jQuery(this).is(':checked')) {
        serial[0] = 1;
    } else {
        serial[0] = 0;
    }
    compileSerial();
});
jQuery('#meli-export-active').on('change',function(){
    if (jQuery(this).is(':checked')) {
        serial[1] = 1;
    } else {
        serial[1] = 0;
    }
    compileSerial();
});
jQuery('#meli-export-paused').on('change',function(){
    if (jQuery(this).is(':checked')) {
        serial[2] = 1;
    } else {
        serial[2] = 0;
    }
    compileSerial();
});
jQuery('#meli-export-closed').on('change',function(){
    if (jQuery(this).is(':checked')) {
        serial[3] = 1;
    } else {
        serial[3] = 0;
    }
    compileSerial();
});
jQuery('#meli-export-missing').on('change',function(){
    if (jQuery(this).is(':checked')) {
        serial[4] = 1;
    } else {
        serial[4] = 0;
    }
    compileSerial();
});

jQuery('#meli-show-export-pages-start').on('change',function(){
    serial[5] = jQuery(this).val();
    compileSerial();
});
jQuery('#meli-show-export-pages-amnt').on('change',function(){
    serial[6] = jQuery(this).val();
    compileSerial();
});

jQuery('#meli-export-reverse').on('change',function(){
    if (jQuery(this).is(':checked')) {
        serial[7] = 1;
    } else {
        serial[7] = 0;
    }
    compileSerial();
});

jQuery('#export-sbmt').on('click',function(){
    if (jQuery(this).attr('href') == '') {
        return false;
    }
});
compileSerial();

jQuery('#export-sbmt').on('click', function(e){
	e.preventDefault();
	var win = popupwindow(jQuery(this).attr('href'), '', 100, 100);
	if (jQuery("#meli-amnt-autoincrement").is(":checked")) {
		var incr = parseInt(jQuery("#meli-show-export-pages-amnt").val());
		var curr_val = parseInt(jQuery("#meli-show-export-pages-start").val());
		var next_val = curr_val + incr;
		jQuery("#meli-show-export-pages-start").val(next_val);
		jQuery('#meli-show-export-pages-start').change();
	}
});
