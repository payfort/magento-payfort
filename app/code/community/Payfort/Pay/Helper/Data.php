<?php

require_once( Mage::getBaseDir('lib') . '/payfortFort/init.php');

class Payfort_Pay_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $pfConfig;
    public function __construct()
    {
        $this->pfConfig = Payfort_Fort_Config::getInstance();
    }
    public function deleteAllCartItems()
    {
        $cartHelper = Mage::helper('checkout/cart');
        $items      = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            $itemId = $item->getItemId();
            $cartHelper->getCart()->removeItem($itemId)->save();
        }
    }

    /**
     * Translates the response code into a more meaningful description.
     * Response code descriptions are taken directly from the Payfort documentation.
     */
    function getResponseCodeDescription($responseCode)
    {
        switch ($responseCode)
        {
            case "0" : $result = "Invalid or incomplete";
                break;
            case "1" : $result = "Cancelled by customer";
                break;
            case "2" : $result = "Authorisation declined";
                break;
            case "5" : $result = "Authorised";
                break;
            case "9" : $result = "Payment requested";
                break;
            default : $result = "Response Unknown";
        }

        return $result;
    }

    public function isMerchantPageMethod($order = '')
    {
        $useMerchantPage = $this->pfConfig->isCcMerchantPage();
        if (!empty($order)) {
            $paymentCode = $order->getPayment()->getMethodInstance()->getCode();
        }
        else {
            $paymentCode = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode();
        }
        if ($useMerchantPage && $paymentCode == Mage::getModel('payfort/payment_cc')->getCode()) {
            return true;
        }
        return false;
    }

    public function isMerchantPageMethod2($order = '')
    {
        $useMerchantPage = $this->pfConfig->isCcMerchantPage2();
        if (!empty($order)) {
            $paymentCode = $order->getPayment()->getMethodInstance()->getCode();
        }
        else {
            $paymentCode = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode();
        }
        if ($useMerchantPage && $paymentCode == Mage::getModel('payfort/payment_cc')->getCode()) {
            return true;
        }
        return false;
    }
    
    /**
     * @param $name
     * @param $block
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        //$quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($this->isMerchantPageMethod() || $this->isMerchantPageMethod2()) {
            return $name;
        }

        if ($blockObject = Mage::getSingleton('core/layout')->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    public function renderResponse($response_message)
    {
        $this->loadLayout();
        //Creating a new block
        $block = $this->getLayout()->createBlock(
                        'Mage_Core_Block_Template', 'payfort_block_response', array('template' => 'payfort/pay/response.phtml')
                )
                ->setData('response_message', $response_message);

        $this->getLayout()->getBlock('content')->append($block);

        //Now showing it with rendering of layout
        $this->renderLayout();
    }
    
    public function getCcTypeName($type)
    {
        if (preg_match('/^paypal/', strtolower($type))) {
            return 'PayPal';
        }

        if(is_null($this->_ccTypeNames)) {
            $this->_ccTypeNames = Mage::getSingleton('payment/config')->getCcTypes();
        }
        return (isset($this->_ccTypeNames[$type]) ? $this->_ccTypeNames[$type] : 'Unknown');
    }
    
    public function getCcTypes() {
        return 'VI,MC';
    }
}
