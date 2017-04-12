<?php

class Payfort_Pay_Model_Pay extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'payfort';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = true;
    //protected $_canUseForMultishipping = false;
    protected $_active = false;
    //protected $_formBlockType = 'payfort/form_options';
    //protected $_infoBlockType = 'payfort/info_details';
    
    public function getOrderPlaceRedirectUrl() {
        //return Mage::getUrl('payfort/payment/redirect', array('_secure' => true));
    }
    
    public function isAvailable($quote = null)
    {
        return false;
    }

}
