<?php
class Payfort_Fort_Order
{

    private $registry;
    private $order = array();
    private $pfConfig;

    public function __construct()
    {
        $this->pfConfig = Payfort_Fort_Config::getInstance();
    }

    public function loadOrder($orderId)
    {
        $_order = Mage::getModel('sales/order');
        if(!$_order) {
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
        }
        $_order->loadByIncrementId($orderId);
        $this->order = $_order;
    }

    public function getSessionOrderId()
    {
        return Mage::getSingleton('checkout/session')->getLastRealOrderId();
    }
    
    public function getOrderId()
    {
        return $this->order->getId();
    }

    public function getOrderById($orderId)
    {
        $this->registry->get('load')->model('checkout/order');
        return $this->registry->get('model_checkout_order')->getOrder($orderId);
    }

    public function getLoadedOrder()
    {
        return $this->order;
    }

    public function getEmail()
    {
        return $this->order->getCustomerEmail();
    }

    public function getCustomerName()
    {
        $fullName  = '';
        $firstName = $this->order->getData('customer_firstname');
        $lastName  = $this->order->getData('customer_lastname');

        $fullName = trim($firstName . ' ' . $lastName);
        return $fullName;
    }

    public function getCurrencyCode()
    {
        return $this->order->getOrderCurrency()->getCode();
    }

    public function getCurrencyValue()
    {
        return 1;//$this->order->getOrderCurrency()->getRate();
    }

    public function getTotal()
    {
        return $this->order->getBaseGrandTotal();
    }

    public function getPaymentMethod() 
    {
        return $this->order->getPayment()->getMethodInstance()->getCode();
    }
    
    public function getStatusId()
    {
        return $this->order->getState();
    }
    
    public function declineOrder() {
        $status = Mage_Sales_Model_Order::STATE_CANCELED;
        if($this->getStatusId() == $status) {
            return true;
        }
        
        $session = Mage::getSingleton('checkout/session');
        try {
            if($this->order->getId()){
                //$incrementId = $session->getLastRealOrderId();
                $session->getQuote()->setIsActive(false)->save();
                $session->clear();
                try {
                    $this->order->cancel()->setState($status, true, 'Payfort has declined the payment.')->save();
                } catch (Mage_Core_Exception $e) {
                    Mage::logException($e);
                    return false;
                }
                //$this->refillCart($this->order);
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
        return true;
    }
    
    public function cancelOrder() {
        $status = Mage_Sales_Model_Order::STATE_CANCELED;
        if($this->getStatusId() == $status) {
            return true;
        }
        
        $session = Mage::getSingleton('checkout/session');
        try {
            if($this->order->getId()){
                //$incrementId = $session->getLastRealOrderId();
                $session->getQuote()->setIsActive(false)->save();
                $session->clear();
                try {
                    $this->order->cancel()->setState($status, true, 'Payfort user has cancelled the payment.')->save();
                } catch (Mage_Core_Exception $e) {
                    Mage::logException($e);
                    return false;
                }
                //$this->refillCart($this->order);
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
        return true;
    }

    public function successOrder($response_params, $response_mode) {
        $status = $this->pfConfig->getSuccessOrderStatusId();
        
        $response_message = Payfort_Fort_Language::__('Redirecting, please wait...');
        $success = true;
        if($success) {
            if($response_mode == 'offline') {
                $this->createInvoice($response_params);
                $this->order->setState($status, true, 'Payfort has accepted the payment.');
                
                $this->order->sendNewOrderEmail();
                $this->order->setEmailSent(true);
                $this->order->save();
            }
            $response_status = $response_params['status'];
            if ($response_status == 14) {
                $response_message = Payfort_Fort_Language::__('Your payment is accepted.');
            }
            elseif ($response_status == 02) {
                $response_message = Payfort_Fort_Language::__('Your payment is authorized.');
            }
            else {
                $response_message = Payfort_Fort_Language::__('Unknown response status.');
            }
        }
        
        return array($success, $response_message);
    }
    
    /**
     *Create Order Invoice
     * 
     * @param boolean Returns true if an invoice has been created.
     */
    public function createInvoice($response_params)
    {
        $result               = false;
        try {
            if (!$this->order->hasInvoices()) {
                $invoice = $this->order->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                
                $invoice->register();
                
                $invoice->setTransactionId($response_params['fort_id']);
                //$invoice->setTransactionId(1);
                $transactionSave = Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();
                
                //$invoice->capture();
                $this->order->addRelatedObject($invoice);
                
                $invoice->sendEmail();

                $result = true;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $result;
    }

}

?>