var payfortFort = (function () {
   return {
        isDefined: function(variable) {
            if (typeof (variable) === 'undefined' || typeof (variable) === null) {
                return false;
            }
            return true;
        },
        isTouchDevice: function() {
            return 'ontouchstart' in window        // works on most browsers 
                || navigator.maxTouchPoints;       // works on IE10/11 and Surface
        },
        trimString: function(str){
            return str.trim();
        },
        isPosInteger: function(data) {
            var objRegExp  = /(^\d*$)/;
            return objRegExp.test( data );
        },
   };
})();

var payfortFortMerchantPage2 = (function () {
    return {
        submitMerchantPage: function(paymentMethod, merchantPageDataUrl) {
            var merchantPage2FormId = '#frm_payfort_fort_payment';
            var card_number = jQuery('#'+paymentMethod+'_cc_number').val();
            var card_holder_name = jQuery('#'+paymentMethod+'_cc_owner').val();
            var expiry_year = jQuery('#'+paymentMethod+'_expiration_yr').val();
            var expiry_month = jQuery('#'+paymentMethod+'_expiration').val();
            var card_security_code = jQuery('#'+paymentMethod+'_cc_cid').val();
            if(expiry_month.length == 1) {
                expiry_month = '0'+expiry_month;
            }
            expiry_year = expiry_year.substr(expiry_year.length - 2);
            var expiry_date = expiry_year+''+expiry_month;
            //get iframe data
            jQuery.ajax({
                url: merchantPageDataUrl,
                type: 'post',
                //data: postData,
                success: function (data) {

                    var respnse = jQuery.parseJSON(data);
                    respnse.params.card_number = card_number;
                    respnse.params.card_holder_name = card_holder_name;
                    respnse.params.card_security_code = card_security_code;
                    respnse.params.expiry_date = expiry_date;
                    
                    if(jQuery(merchantPage2FormId).size()) {
                        jQuery( merchantPage2FormId ).remove();
                    }
                    jQuery('<form id="frm_payfort_fort_payment" action="'+respnse.url+'" method="POST"><input type="submit"/></form>').appendTo('body');
                    jQuery.each(respnse.params, function(k, v){
                        jQuery('<input>').attr({
                            type: 'hidden',
                            id: k,
                            name: k,
                            value: v
                        }).appendTo(merchantPage2FormId); 
                    });
                    jQuery(merchantPage2FormId).attr('action', respnse.url);
                    jQuery(merchantPage2FormId).submit();
                },
                error: function () {
                    alert("Can't load payment page!");
                }
            });
        },
    };
})();

var payfortFortMerchantPage = (function () {
    return {
        submitMerchantPage: function(merchantPageDataUrl) {
    
            //get iframe data
            jQuery.ajax({
                url: merchantPageDataUrl,
                type: 'post',
                //data: postData,
                success: function (data) {

                    var respnse = jQuery.parseJSON(data);
                    jQuery('#payfort_payment_form').html('');
                    jQuery.each(respnse.params, function(k, v){
                        jQuery('<input>').attr({
                            type: 'hidden',
                            id: k,
                            name: k,
                            value: v
                        }).appendTo('#payfort_payment_form'); 
                    });
                    payfortFortMerchantPage.showMerchantPage(respnse.url);
                },
                error: function () {
                    alert("Can't load payment page!");
                }
            });
        },
        showMerchantPage: function(gatewayUrl) {
            if(jQuery("#payfort_merchant_page").size()) {
                jQuery( "#payfort_merchant_page" ).remove();
            }
            jQuery('<iframe  name="payfort_merchant_page" id="payfort_merchant_page"height="550px" frameborder="0" scrolling="no" onload="payfortFortMerchantPage.iframeLoaded(this)" style="display:none"></iframe>').appendTo('#pf_iframe_content');
            jQuery('.pf-iframe-spin').show();
            jQuery('.pf-iframe-close').hide();
            jQuery( "#payfort_merchant_page" ).attr("src", gatewayUrl);
            jQuery( "#payfort_payment_form" ).attr("action",gatewayUrl);
            jQuery( "#payfort_payment_form" ).attr("target","payfort_merchant_page");
            jQuery( "#payfort_payment_form" ).submit();
            //fix for touch devices
            if (payfortFort.isTouchDevice()) {
                setTimeout(function() {
                    jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                }, 1);
            }
            jQuery( "#div-pf-iframe" ).show();
        },
        closePopup: function() {
            jQuery( "#div-pf-iframe" ).hide();
            jQuery( "#payfort_merchant_page" ).remove();
            window.location = jQuery( "#payfort_cancel_url" ).val();
        },
        iframeLoaded: function(){
            jQuery('.pf-iframe-spin').hide();
            jQuery('.pf-iframe-close').show();
            jQuery('#payfort_merchant_page').show();
        },
    };
})();

payfortFort.IWD_OPC_responseSaveOrder = {
    init: function(){
            if(typeof(IWD) != 'undefined' && IWD != undefined && IWD != '' && IWD
                    && typeof(IWD.OPC) != 'undefined' && IWD.OPC != undefined && IWD.OPC != '' && IWD.OPC) {
                IWD.OPC.Plugin.event('responseSaveOrder', payfortFort.IWD_OPC_responseSaveOrder.handleResponse);
            }
    },
    handleResponse: function(response) {
        IWD.OPC.Checkout.showLoader();
        IWD.OPC.Checkout.lockPlaceOrder();
        
        payfortFortMerchantPage2.submitMerchantPage('payfortcc', IWD.OPC.Checkout.config.baseUrl + 'payfort/payment/getMerchantPageData');
        
        IWD.OPC.Checkout.hideLoader();
        IWD.OPC.Checkout.unlockPlaceOrder();
    }
}
Event.observe(window, 'load', function() {
    //check if the user in payment page
    if(typeof(payment) != 'undefined' && payment != undefined && payment != '' && payment) {
        //IWD OPC fix
        if(typeof(IWD) != 'undefined' && IWD != undefined && IWD != '' && IWD
                && typeof(IWD.OPC) != 'undefined' && IWD.OPC != undefined && IWD.OPC != '' && IWD.OPC) {
                payfortFort.IWD_OPC_responseSaveOrder.init();
        }
        
        /* onestepchekout fix
         * @url : https://www.onestepcheckout.com/
         */
        // check if we are dealing with OneStepCheckout
        payment.isOneStepCheckout = $$('.onestepcheckout-place-order');
        if(payment.isOneStepCheckout.length) {
            payment.formOneStepCheckout = $('onestepcheckout-form');
            payment.yourmethod = 'payfortcc';
            payment.submitclicked = false;
            payment.formOneStepCheckout.submit =  payment.formOneStepCheckout.submit.wrap(function(originalSubmitMethod) {
                if(payment.submitclicked){
                    return;
                }
                payment.submitclicked = true;
                if (payment.currentMethod == payment.yourmethod && payfortFort.integrationType == 'merchantPage2') {
                    var checkoutParams = payment.formOneStepCheckout.serialize()+ '&pluginName=OneStepCheckout';
                    new Ajax.Request(
                        payment.formOneStepCheckout.action,
                        {
                            method:'post',
                            parameters:checkoutParams,
                            onComplete: function(transport) {
                                if (transport && transport.responseText) {
                                    try{
                                        response = eval('(' + transport.responseText + ')');
                                    }
                                    catch (e) {
                                        response = {};
                                    }
                                }
                            },
                            onSuccess: function(response){
                                payfortFortMerchantPage2.submitMerchantPage(payment.yourmethod, payfortFort.BASE_URL + 'payfort/payment/getMerchantPageData');
                                return;
                            },
                            onFailure: checkout.ajaxFailure.bind(checkout)
                        }
                    );
                } else {
                    originalSubmitMethod();
                }
            });
        }
    }
});