<?php

require_once( Mage::getBaseDir('lib') . '/payfortFort/init.php');

class Payfort_Pay_Model_Observer extends Mage_CatalogInventory_Model_Observer
{

    public $pfConfig;
    public $pfHelper;

    function __construct()
    {
        $this->pfConfig = Payfort_Fort_Config::getInstance();
        $this->pfHelper = Payfort_Fort_Helper::getInstance();
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('payfort_fort_order', $order, true);

        return $this;
    }

    public function paymentMethodIsActive($observer)
    {
        $event            = $observer->getEvent();
        $method           = $event->getMethodInstance();
        $result           = $event->getResult();
        $napsPaymentCode  = Mage::getModel('payfort/payment_naps')->getCode();
        $sadadPaymentCode = Mage::getModel('payfort/payment_sadad')->getCode();
        
        if (($method->getCode() == $napsPaymentCode) || ($method->getCode() == $sadadPaymentCode)) {
            $quote = $event->getQuote();
            if ($quote) {
                $frontCurrency = $this->pfHelper->getFrontCurrency();
                $baseCurrency  = $this->pfHelper->getBaseCurrency();
                $currency      = $this->pfHelper->getFortCurrency($baseCurrency, $frontCurrency);
                if ($method->getCode() == $napsPaymentCode) {
                    if ($currency != 'QAR') {
                        $result->isAvailable = false;
                    }
                }
                if ($method->getCode() == $sadadPaymentCode) {
                    if ($currency != 'SAR') {
                        $result->isAvailable = false;
                    }
                }
            }
        }
    }

    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
    public function setResponseAfterSaveOrder(Varien_Event_Observer $observer)
    {
        $order = Mage::registry('payfort_fort_order');
        if($order && $order->getId()) {
            /* @var $controller Mage_Core_Controller_Varien_Action */
            $controller = $observer->getEvent()->getData('controller_action');
            $result = Mage::helper('core')->jsonDecode(
                    $controller->getResponse()->getBody('default'),
                    Zend_Json::TYPE_ARRAY
                );
            if(empty($result['error'])) {
                $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
                if($paymentMethod == PAYFORT_FORT_PAYMENT_METHOD_CC) {
                    if($this->pfConfig->isCcMerchantPage2()) {
                        $controller = $observer->getEvent()->getData('controller_action');
                        $result['redirect'] = false;
                        $controller->getResponse()->clearHeader('Location');
                        $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    }
                }
            }
        }
        return $this;
    }
    
    /**
     * Dispatch: checkout_type_onepage_save_order_after
     * @param $observer
     */
    public function afterSaveOrder($observer) {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
        if(in_array($paymentMethod, array(PAYFORT_FORT_PAYMENT_METHOD_CC, PAYFORT_FORT_PAYMENT_METHOD_NAPS, PAYFORT_FORT_PAYMENT_METHOD_SADAD))) {
            $order->setCanSendNewEmailFlag(false);
            /*$order->setState(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, (bool) Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $this->__('Payfort pending payment.')
            )->save();*/
            $order->setIsNotified(false);
        }
    }
    /**
     * Set data for response of frontend saveOrder action
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Paypal_Model_Observer
     */
//    public function setResponseAfterSaveOrder(Varien_Event_Observer $observer)
//    {
//        /* @var $order Mage_Sales_Model_Order */
//        //$order = Mage::registry('pf_order');
//        if(Mage::helper('payfort/data')->isMerchantPageMethod()){
//            $controller = $observer->getEvent()->getData('controller_action');
//            $result = Mage::helper('core')->jsonDecode(
//                $controller->getResponse()->getBody('default'),
//                Zend_Json::TYPE_ARRAY
//            );
//            $result['redirect'] = false;
//            $result['success'] = false;
//            $result['results']['save_order']['success'] = false;
//            $result['results']['save_order']['redirect'] = false;
//            $controller->getResponse()->clearHeader('Location');
//            $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//        }
//        return $this;
//    }
}

?>
