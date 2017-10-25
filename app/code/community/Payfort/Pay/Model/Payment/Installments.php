<?php

class Payfort_Pay_Model_Payment_Installments extends Payfort_Pay_Model_Method {

    protected $_code = PAYFORT_FORT_PAYMENT_METHOD_INSTALLMENTS;
    public $pfConfig;

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('payfort/payment/redirect', array('_secure' => true));
    }

}
