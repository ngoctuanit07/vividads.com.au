/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Ajaxcontacts
 * @version    1.0
 * @copyright  Copyright (c) 2011 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */


var messageToContent = function(html){
    if (!$('message')){
        $$('.col-main').each(function(el){
            var div = document.createElement('div');
            $(div).addClassName('message');
            $(div).id = 'message';
            Element.insert(el, {'top': div });
        });
    }
    if ($('message')){
        $('message').innerHTML = html;
    }
};

var showContactsForm = function(params){

    var lock = false;
    if (lock) {
        return false;
    }
    lock = true;
    var scripts = '';
    var contactForm;
    Dialog.confirm(
        {
            url: params.url.replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')),
            options: {
                method: 'get',
                onSuccess: (function(transport){
                    scripts = transport.responseText;
                }).bind(scripts)
            }
        },
        {
            className: 'alphacube',
            width: params.width,
            height: params.height,
            id: "mp_ajaxcontacts_window",
            okLabel: params.ok_label,
            cancelLabel: params.cancel_label,
            onOk: (function (){
                if (contactForm.validator.validate()){

                    var post = [];
                    var elements = Form.getElements(contactForm.form);
                    elements.each(function(el){
                        if (el.value){
                            post.push(el.name + '=' + el.value);
                        }
                    });
                    var d = new Date();
                    post.push('antibot-field=antibot-'+d.getMilliseconds());
                    post.push('hideit=');

                    new Ajax.Request(
                        params.post_url.replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')),{
                            method: 'post',
                            parameters: post.join("&"),
                            onComplete: function(transport){
                                if (transport && transport.responseText){
                                    try{
                                        var response = eval('(' + transport.responseText + ')');
                                        if (response.message){
                                            messageToContent(response.message);
                                        }
                                    } catch (e) {
                                        response = {};
                                    }
                                }
                            }
                    });

                    lock = false;
                    return true;
                } else {
                    return false;
                }
            }).bind(scripts).bind(contactForm),
            onCancel: function(){ lock = false; },
            onShow: (function(){
                scripts.evalScripts();
                contactForm = new VarienForm('contactForm', true);
            }).bind(scripts).bind(contactForm)

        }
    );
};


function getScroll() {
  var scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    scrOfY = window.pageYOffset;
  } else if( document.body && document.body.scrollTop ) {
    scrOfY = document.body.scrollTop;
  } else if( document.documentElement && document.documentElement.scrollTop) {
    scrOfY = document.documentElement.scrollTop;
  }
  return scrOfY;
}


