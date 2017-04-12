<?php

class Payfort_Fort_Helper
{

    private static $instance;
    private $pfConfig;

    public function __construct()
    {
        $this->pfConfig = Payfort_Fort_Config::getInstance();
    }

    /**
     * @return Payfort_Fort_Config
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Payfort_Fort_Helper();
        }
        return self::$instance;
    }

    public function getBaseCurrency()
    {
        return Mage::app()->getStore()->getBaseCurrencyCode();
    }

    public function getFrontCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    public function getFortCurrency($baseCurrencyCode, $currentCurrencyCode)
    {
        $gateway_currency = $this->pfConfig->getGatewayCurrency();
        $currencyCode     = $baseCurrencyCode;
        if ($gateway_currency == 'front') {
            $currencyCode = $currentCurrencyCode;
        }
        return $currencyCode;
    }

    public function getReturnUrl($path)
    {
        return $this->getUrl('payfort/payment/' . $path);
    }

    public function getUrl($path)
    {
        if (Mage::app()->getStore()->isFrontUrlSecure() && Mage::app()->getRequest()->isSecure()
        ) {
            // current page is https
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true) . $path;
        }
        else {
            // current page is http
            return Mage::getBaseUrl() . $path;
        }
    }

    /**
     * Convert Amount with dicemal points
     * @param decimal $amount
     * @param decimal $currency_value
     * @param string  $currency_code
     * @return decimal
     */
    public function convertFortAmount($amount, $currency_value, $currency_code)
    {
        $gateway_currency = $this->pfConfig->getGatewayCurrency();
        $new_amount       = 0;
        //$decimal_points = $this->currency->getDecimalPlace();
        $decimal_points   = $this->getCurrencyDecimalPoints($currency_code);
        if ($gateway_currency == 'front') {
            //$new_amount = round($amount * $currency_value, $decimal_points);
            $baseCurrencyCode    = $this->getBaseCurrency();
            $currentCurrencyCode = $this->getFrontCurrency();
            $new_amount          = round(Mage::helper('directory')->currencyConvert($amount, $baseCurrencyCode, $currentCurrencyCode), 2);
        }
        else {
            $new_amount = round($amount, $decimal_points);
        }
        $new_amount = $new_amount * (pow(10, $decimal_points));
        return $new_amount;
    }

    /**
     * 
     * @param string $currency
     * @param integer 
     */
    public function getCurrencyDecimalPoints($currency)
    {
        $decimalPoint  = 2;
        $arrCurrencies = array(
            'JOD' => 3,
            'KWD' => 3,
            'OMR' => 3,
            'TND' => 3,
            'BHD' => 3,
            'LYD' => 3,
            'IQD' => 3,
        );
        if (isset($arrCurrencies[$currency])) {
            $decimalPoint = $arrCurrencies[$currency];
        }
        return $decimalPoint;
    }

    /**
     * calculate fort signature
     * @param array $arrData
     * @param sting $signType request or response
     * @return string fort signature
     */
    public function calculateSignature($arrData, $signType = 'request')
    {
        $shaString = '';

        ksort($arrData);
        foreach ($arrData as $k => $v) {
            $shaString .= "$k=$v";
        }

        if ($signType == 'request') {
            $shaString = $this->pfConfig->getRequestShaPhrase() . $shaString . $this->pfConfig->getRequestShaPhrase();
        }
        else {
            $shaString = $this->pfConfig->getResponseShaPhrase() . $shaString . $this->pfConfig->getResponseShaPhrase();
        }
        $signature = hash($this->pfConfig->getHashAlgorithm(), $shaString);

        return $signature;
    }

    /**
     * Log the error on the disk
     */
    public function log($messages, $forceDebug = false)
    {
        $debugMode = $this->pfConfig->isDebugMode();
        if (!$debugMode && !$forceDebug) {
            return;
        }
        Mage::log($messages, null, $this->pfConfig->getLogFileDir(), true);
    }

    public function getCustomerIp()
    {
        return Mage::helper('core/http')->getRemoteAddr();
    }

    public function getGatewayHost()
    {
        if ($this->pfConfig->isSandboxMode()) {
            return $this->getGatewaySandboxHost();
        }
        return $this->getGatewayProdHost();
    }

    public function getGatewayUrl($type = 'redirection')
    {
        $testMode = $this->pfConfig->isSandboxMode();
        if ($type == 'notificationApi') {
            $gatewayUrl = $testMode ?  'https://sbpaymentservices.payfort.com/FortAPI/paymentApi' :  'https://paymentservices.payfort.com/FortAPI/paymentApi';
        }
        else {
            $gatewayUrl = $testMode ? $this->pfConfig->getGatewaySandboxHost() . 'FortAPI/paymentPage' : $this->pfConfig->getGatewayProdHost() . 'FortAPI/paymentPage';
        }

        return $gatewayUrl;
    }

    public function setFlashMsg($message, $status = PAYFORT_FORT_FLASH_MSG_ERROR, $title = '')
    {
        $session = Mage::getSingleton('checkout/session');
        $session->addError($message);
    }

    public static function loadJsMessages($messages, $isReturn = true, $category = 'payfort_fort')
    {
        $result = '';
        foreach ($messages as $label => $translation) {
            $result .= "arr_messages['{$category}.{$label}']='" . $translation . "';\n";
        }
        if ($isReturn) {
            return $result;
        }
        else {
            echo $result;
        }
    }

    public function refillCart($order)
    {
        if (empty($order)) {
            return;
        }
        $session  = Mage::getSingleton('checkout/session');
        $cart     = Mage::getSingleton('checkout/cart');
        $cart_qty = (int) $cart->getQuote()->getItemsQty();
        if ($cart_qty) {
            return;
        }
        if ($order->getId()) {
            $items = $order->getItemsCollection();
            foreach ($items as $item) {
                try {
                    $cart->addOrderItem($item);
                } catch (Mage_Core_Exception $e) {
                    $session->addError($e->getMessage());
                    Mage::logException($e);
                    continue;
                }
            }
            $cart->save();
            $cart_qty = (int) $cart->getQuote()->getItemsQty();
        }
    }

}

?>