<?php 
class Payfort_Pay_Block_Checkout_Cart_Totals extends Mage_Checkout_Block_Cart_Totals{
    public function needDisplayBaseGrandtotal(){
        $gateway_currency = Mage::getStoreConfig('payment/payfort/gateway_currency');
        if($gateway_currency == 'base') {
            $quote  = $this->getQuote();
            if ($quote->getBaseCurrencyCode() != $quote->getQuoteCurrencyCode()) {
                return true;
            }
        }
        return false;
    }
}