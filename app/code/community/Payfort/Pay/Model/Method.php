<?php

require_once(Mage::getBaseDir('lib') . '/payfortFort/init.php');

class Payfort_Pay_Model_Method extends Mage_Payment_Model_Method_Abstract
{

    protected $_isInitializeNeeded = true;
    protected $_canUseInternal     = true;
    protected $_canCapture         = true;
    
    /**
     * Order statuses         		      			  	
     */
    const PAYFORT_FORT_STATUS_PENDING  = 'pending_payfort_fort';
    const PAYFORT_FORT_STATUS_CANCELED = 'canceled_payfort_fort';
    const STATE_CANCELLED              = 'canceled';
    const STATE_PENDING                = 'pending_payment';
    const STATE_PROCESSING             = 'processing';

    /**
     * Standard payment method flags
     */
    protected $_isGateway = true;

    /**
     * This method is called during order creation to initialize the payment.
     *
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     * @return 
     */
    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
        return $this;
    }

}

?>