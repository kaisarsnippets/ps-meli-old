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

/**
 * Set Notifications
 * */
if (m4p_notifications == 'false') {
    m4p_notifications = [];
} else {
    m4p_notifications = m4p_notifications.replace(/&quot;/g,'\"');
    m4p_notifications = m4p_notifications.replace(/\"{/g,'{');
    m4p_notifications = m4p_notifications.replace(/}\"/g,'}');
    m4p_notifications = jQuery.parseJSON(m4p_notifications);
}

jQuery(window).load(function(){
    
    notif_content = '';
    var notif_badge_hide ='';
    var notif_badge_amnt = 0;
    
    if (m4p_notifications.length == 0) {
        notif_badge_hide = ' hide';
        notif_content = '<span class="no_notifs">'+m4p.messages.noNewNotif+'</span>';
    } else {
		var some_unviewed = false;
		jQuery(m4p_notifications).each(function(b, a){
			if (a.viewed == 0) {
				some_unviewed = true;
			}
		});
		if (!some_unviewed) {
			notif_badge_hide = ' hide';
		}
	}
    
    var item_out_stock_taken_ids = [];
    var item_small_stock_taken_ids = [];
    var variation_out_stock_taken_ids = [];
    var order_taken_ids = [];
    jQuery(m4p_notifications).each(function(b,a){
        var notif = a;
        var active_notif = '';
        if (notif.viewed == '1') {
			active_notif = ' style="color:#ccc;"';
		}
        
        if (notif.name == 'question') {
            notif_content += '<a'+active_notif+' target="_blank" href="'+m4p.paths.shop+'/'+m4p.paths.admin+'/index.php?controller=AdminProducts&id_product='+notif.value.id_product+'&updateproduct&key_tab=ModuleMercadolibre&token='+m4p.tokens.admin+'&meli_tab=questions">\
                <p><i class="icon-comments"></i> '+m4p.messages.youHaveANewQuestion+' <b>'+notif.value.nickname+'</b></p>\
            </a>';
            if (notif.viewed == "0") {
				notif_badge_amnt++;
			}
        }
        if (notif.name == 'item_small_stock') {
            var id_taken = false;
            jQuery(item_out_stock_taken_ids).each(function(d, c){
                if (c == notif.id_meli) {
                    id_taken = true;
                }
            });
            if (!id_taken) {
                item_small_stock_taken_ids.push(notif.id_meli);
                notif_content += '<a'+active_notif+' target="_blank" href="'+m4p.paths.shop+'/'+m4p.paths.admin+'/index.php?controller=AdminProducts&id_product='+notif.value.id_product+'&updateproduct&key_tab=ModuleMercadolibre&token='+m4p.tokens.admin+'&meli_tab=edit">\
                    <p><i class="icon-warning"></i> '+m4p.messages.itemSmallStock+' | '+m4p.messages.goToProduct+' #'+notif.value.id_product+' &raquo;</p>\
                </a>';
                if (notif.viewed == "0") {
					notif_badge_amnt++;
				}
            }
        }
        if (notif.name == 'item_out_stock') {
            var id_taken = false;
            jQuery(item_out_stock_taken_ids).each(function(d, c){
                if (c == notif.id_meli) {
                    id_taken = true;
                }
            });
            if (!id_taken) {
                item_out_stock_taken_ids.push(notif.id_meli);
                notif_content += '<a'+active_notif+' target="_blank" href="'+m4p.paths.shop+'/'+m4p.paths.admin+'/index.php?controller=AdminProducts&id_product='+notif.value.id_product+'&updateproduct&key_tab=ModuleMercadolibre&token='+m4p.tokens.admin+'&meli_tab=edit">\
                    <p><i class="icon-warning"></i> '+m4p.messages.itemOutStock+' | '+m4p.messages.goToProduct+' #'+notif.value.id_product+' &raquo;</p>\
                </a>';
                if (notif.viewed == "0") {
					notif_badge_amnt++;
				}
            }
        }
        if (notif.name == 'variation_out_stock') {
            var id_taken = false;
            jQuery(variation_out_stock_taken_ids).each(function(d, c){
                if (c == notif.id_meli) {
                    id_taken = true;
                }
            });
            if (!id_taken) {
                variation_out_stock_taken_ids.push(notif.id_meli);
                notif_content += '<a'+active_notif+' target="_blank" href="'+m4p.paths.shop+'/'+m4p.paths.admin+'/index.php?controller=AdminProducts&id_product='+notif.value.id_product+'&updateproduct&key_tab=ModuleMercadolibre&token='+m4p.tokens.admin+'&meli_tab=edit">\
                    <p><i class="icon-warning"></i> '+m4p.messages.variationOutStock+' | '+m4p.messages.goToProduct+' #'+notif.value.id_product+' &raquo;</p>\
                </a>';
                if (notif.viewed == "0") {
					notif_badge_amnt++;
				}
            }
        }
        if (notif.name == 'item_closed') {
            notif_content += '<a'+active_notif+' target="_blank" href="'+m4p.paths.shop+'/'+m4p.paths.admin+'/index.php?controller=AdminProducts&id_product='+notif.value.id_product+'&updateproduct&key_tab=ModuleMercadolibre&token='+m4p.tokens.admin+'&meli_tab=edit">\
                <p><i class="icon-warning"></i> '+m4p.messages.itemClosed+' | '+m4p.messages.goToProduct+' #'+notif.value.id_product+' &raquo;</p>\
            </a>';
            if (notif.viewed == "0") {
				notif_badge_amnt++;
			}
        }
        if (notif.name == 'order_new') {
            notif_content += '<a'+active_notif+' target="_blank" href="'+m4p.paths.shop+'/'+m4p.paths.admin+'/index.php?controller=AdminOrders&token='+m4p.tokens.orders+'">\
                <p><i class="icon-warning"></i> '+m4p.messages.newOrder+' | '+m4p.messages.goToOrder+' &raquo;</p>\
            </a>';
            if (notif.viewed == "0") {
				notif_badge_amnt++;
			}
        }
    })
    
    jQuery('#header_notifs_icon_wrapper').prepend('<li data-type="meli-warnings" class="dropdown" id="meli_warning_notif">\
        <a data-toggle="dropdown" class="dropdown-toggle notifs" href="javascript:void(0);">\
            <i class="icon-comments color-meli"></i>\
            <span class="notifs_badge'+notif_badge_hide+'" id="meli_warnings_notif_number_wrapper">\
                <span id="meli_warnings_notif_value">'+notif_badge_amnt+'</span>\
            </span>\
        </a>\
        <div class="dropdown-menu notifs_dropdown">\
            <section class="notifs_panel" id="m4p_notif_wrapper">\
                <div class="notifs_panel_header">\
                    <h3>'+m4p.messages.mercadoLibreAlerts+'</h3>\
                </div>\
                <div class="list_notif" id="list_customers_notif">\
                    '+notif_content+'\
                </div>\
            </section>\
        </div>\
    </li>');
    
    jQuery('.dropdown-toggle.notifs').on('click',function(){
		jQuery.get(m4p.paths.base+'/services/internal/admin/alerts/alerts.php?&view=1&delete=1&token='+m4p.tokens.meli);
		jQuery('#meli_warnings_notif_number_wrapper').hide();
	});
});

/**
 * Set Product List Meli Data
 * */
//Changing meli-stock-status to HTML
jQuery(document).ready(function(){
    jQuery("td.m4p-meli-data").each(function(b, a){
        var txt = jQuery(a).text().trim();
        if (txt != '--') {
            jQuery(a).attr("data-meli",txt);
            var meli_data = jQuery.parseJSON(txt);
            
            //replace td content by HTML
            var quant_html = '<span>'+meli_data.quantities[0]+'</span>';
            
            var tooltip_html = '<b>stock: '+meli_data.quantities[0]+'</b>';
            if(meli_data.quantities.length > 1){
                jQuery(meli_data.quantities).each(function(d, c){
                    if(d > 0){
                        tooltip_html += '<br /><span>variation '+d+': <b>'+c+'</b></span>';
                    }
                });
            }
            tooltip_html += '<br /><span><b>status</b> : '+meli_data.status+'</span>';
            
            jQuery(a).html('<div class="m4p-meli-data-display label-tooltip '+meli_data.status+' text-center" data-html="true" data-toggle="tooltip" data-original-title="'+tooltip_html+'">'+meli_data.id+'</div>');
        }
    });
});
 

