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

function loadQuestions(offset,limit) {
    var mid = meli_data.id;
    var url = m4p.paths.base+'/services/internal/admin/questions/load.php?mid='+mid+'&offset='+offset+'&limit='+limit+'&token='+m4p.tokens.questions;
    jQuery('#m4p-questions-load').remove();
    jQuery('#question-loading').show();
    
    jQuery.getJSON(url,function(data){
		
		if (data == null) {
			var data = {
				total : 0,
				questions : []
			}
		}
		
        jQuery('#question-loading').hide();
        q_offset += q_limit;
        q_total = data.total;
        
        if (q_total > 0) {
            jQuery('.meli-no-questions-yet-msg').remove();
        }
        
        var questions = data.questions;
        jQuery(questions).each(function(b,a){
            var question = a;
            var reply_box = '';
            if (question.status == 'UNANSWERED') {
                reply_box = '\
                <hr />\
                <p><textarea class="form-control" placeholder="'+m4p.messages.enterReply+'"></textarea></p>\
                <p><button class="m4p-btn-reply btn btn-default" type="button" data-qid="'+question.id+'">'+m4p.messages.sendReply+'</button></p>\
                ';
            } else {
                if (typeof(question.answer) != 'undefined') {
                    reply_box = '\
                    <hr />\
                    <p><b>'+m4p.messages.youReplied+'</b></p>\
                    <p>'+question.answer.text+'</p>\
                    ';
                }
            }
            jQuery('#meli-questions-container').append('\
            <div class="panel m4p-question-panel">\
                <h3>\
                    <i class="icon-comments"></i> '+question.user.nickname+': \
                    <button data-qid="'+question.id+'" type="button" style="padding:1px 5px;position:relative;top:5px" class="m4p-delete-question btn btn-default pull-right"><i style="position:relative;" class="icon-trash"></i></button>\
                </h3>\
                <p>'+question.text+'</p>\
                <div class="meli-reply-box">\
                    '+reply_box+'\
                </div>\
            </div>\
            ');
            
            jQuery('.m4p-delete-question').off('click');
            jQuery('.m4p-delete-question').on('click',function(){
                var this_delete = jQuery(this);
                if (confirm(m4p.messages.deleteQuestionConfirm)) {
                    this_q = this_delete.parent().parent();
                    this_q.fadeOut(function(){
                        this_q.remove();
                    });
                    var qid = this_delete.attr('data-qid');
                    var url = m4p.paths.base+'/services/internal/admin/questions/delete.php?del=1&qid='+qid+'&token='+m4p.tokens.questions;
                    jQuery.get(url);
                }
            });
            
            jQuery('.m4p-btn-reply').off('click');
            jQuery('.m4p-btn-reply').on('click',function(){
                var url = m4p.paths.base+'/services/internal/admin/questions/answer.php?token='+m4p.tokens.questions;
                var elem = jQuery(this);
                var box = elem.parent().parent();
                var id = elem.attr('data-qid');
                var txt = elem.parent().prev().find('textarea').val();
                if (txt) {
                    box.html('<p><img src="'+m4p.paths.base+'/views/img/loading-sm.gif" /><p>');
                    reply_box = '\
                    <hr />\
                    <p><b>'+m4p.messages.youReplied+'</b></p>\
                    <p>'+txt+'</p>\
                    ';
                    var post_data = {
                        question_id: id,
                        text: txt
                    }
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: JSON.stringify(post_data),
                        contentType: 'application/json; charset=utf-8',
                        dataType: 'json',
                        success: function(data) {
                            box.html(reply_box);
                        }
                    });
                }
            });
        });
        if (q_total) {
            if (q_total > q_offset) {
                jQuery('#meli-questions-container').append('\
                <p id="m4p-questions-load" class="text-center">\
                    <button type="button" class="btn btn-default"><i class="icon-plus"></i> '+m4p.messages.loadMoreQuestions+'</button>\
                </p>\
                ');
                jQuery('#m4p-questions-load').on('click', function(){
                    loadQuestions(q_offset,q_limit);
                });
            }
        }
    });
}

if (meli_data.id) {
    var q_total;
    var q_offset = 0; 
    var q_limit = 3; 
    loadQuestions(q_offset, q_limit);
}
