<?php
/**
 * Additional settings for "Merchant Page" variant of payment method (frontend)
 */

class Payfort_Pay_Block_Checkout_Onepage_Settings extends Mage_Core_Block_Template
{

    public function isMerchantPageMethod(){

        return true;
        $paymentCode = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode();
        /*$xpaymentPaymentCode = Mage::getModel('xpaymentsconnector/payment_cc')->getCode();

        if($paymentCode == $xpaymentPaymentCode){
            return true;
        }
        else{
            return false;
        }*/
    }


}
