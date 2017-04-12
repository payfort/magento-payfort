<?php

define('PAYFORT_FORT_INTEGRATION_TYPE_REDIRECTION', 'redirection');
define('PAYFORT_FORT_INTEGRATION_TYPE_MERCAHNT_PAGE', 'merchantPage');
define('PAYFORT_FORT_INTEGRATION_TYPE_MERCAHNT_PAGE2', 'merchantPage2');
define('PAYFORT_FORT_PAYMENT_METHOD_CC', 'payfortcc');
define('PAYFORT_FORT_PAYMENT_METHOD_NAPS', 'payfortnaps');
define('PAYFORT_FORT_PAYMENT_METHOD_SADAD', 'payfortsadad');
define('PAYFORT_FORT_FLASH_MSG_ERROR', 'E');
define('PAYFORT_FORT_FLASH_MSG_SUCCESS', 'S');
define('PAYFORT_FORT_FLASH_MSG_INFO', 'I');
define('PAYFORT_FORT_FLASH_MSG_WARNING', 'W');

class Payfort_Fort_Config
{

    private static $instance;
    private $language;
    private $merchantIdentifier;
    private $accessCode;
    private $command;
    private $hashAlgorithm;
    private $requestShaPhrase;
    private $responseShaPhrase;
    private $sandboxMode;
    private $gatewayCurrency;
    private $debugMode;
    private $hostUrl;
    private $successOrderStatusId;
    private $status;
    private $ccStatus;
    private $ccIntegrationType;
    private $ccTitle;
    private $ccSortOrder;
    private $sadadStatus;
    private $sadadTitle;
    private $sadadSortOrder;
    private $napsStatus;
    private $napsTitle;
    private $napsSortOrder;
    private $gatewayProdHost;
    private $gatewaySandboxHost;
    private $logFileDir;

    public function __construct()
    {
        $this->gatewayProdHost    = 'https://checkout.payfort.com/';
        $this->gatewaySandboxHost = 'https://sbcheckout.payfort.com/';
        $this->logFileDir         = 'payfortfort.log';

        $this->language             = $this->_getShoppingCartConfig('payfort/language');
        $this->merchantIdentifier   = $this->_getShoppingCartConfig('payfort/merchant_identifier');
        $this->accessCode           = $this->_getShoppingCartConfig('payfort/access_code');
        $this->command              = $this->_getShoppingCartConfig('payfort/command');
        $this->hashAlgorithm        = $this->_getShoppingCartConfig('payfort/sha_type');
        $this->requestShaPhrase     = $this->_getShoppingCartConfig('payfort/sha_in_pass_phrase');
        $this->responseShaPhrase    = $this->_getShoppingCartConfig('payfort/sha_out_pass_phrase');
        $this->sandboxMode          = $this->_getShoppingCartConfig('payfort/sandbox_mode');
        $this->gatewayCurrency      = $this->_getShoppingCartConfig('payfort/gateway_currency');
        $this->debugMode            = $this->_getShoppingCartConfig('payfort/debug_mode');
        //$this->hostUrl = $this->_getShoppingCartConfig('hostUrl');
        $this->successOrderStatusId = Mage_Sales_Model_Order::STATE_PROCESSING;
        $this->status               = 1;
        $this->ccStatus             = $this->_getShoppingCartConfig('payfortcc/active');
        $this->ccSortOrder          = $this->_getShoppingCartConfig('payfortcc/sort_order');
        $this->ccTitle              = $this->_getShoppingCartConfig('payfortcc/tilte');
        $this->ccIntegrationType    = $this->_getShoppingCartConfig('payfortcc/integration_type');
        $this->sadadStatus          = $this->_getShoppingCartConfig('payfortsadad/active');
        $this->sadadTitle           = $this->_getShoppingCartConfig('payfortsadad/title');
        $this->sadadSortOrder       = $this->_getShoppingCartConfig('payfortsadad/sort_order');
        $this->napsStatus           = $this->_getShoppingCartConfig('payfortnaps/active');
        $this->napsTitle            = $this->_getShoppingCartConfig('payfortnaps/title');
        $this->napsSortOrder        = $this->_getShoppingCartConfig('payfortnaps/sort_order');
    }

    /**
     * @return Payfort_Fort_Config
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Payfort_Fort_Config();
        }
        return self::$instance;
    }

    private function _getShoppingCartConfig($key)
    {
        return Mage::getStoreConfig('payment/' . $key);
    }

    public function getLanguage()
    {
        $langCode = $this->language;
        if ($this->language == 'no_language') {
            $langCode = Payfort_Fort_Language::getCurrentLanguageCode();
        }
        if ($langCode != 'ar') {
            $langCode = 'en';
        }
        return $langCode;
    }

    public function getMerchantIdentifier()
    {
        return $this->merchantIdentifier;
    }

    public function getAccessCode()
    {
        return $this->accessCode;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getHashAlgorithm()
    {
        $this->hashAlgorithm = str_replace('-', '', $this->hashAlgorithm);
        return $this->hashAlgorithm;
    }

    public function getRequestShaPhrase()
    {
        return $this->requestShaPhrase;
    }

    public function getResponseShaPhrase()
    {
        return $this->responseShaPhrase;
    }

    public function getSandboxMode()
    {
        return $this->sandboxMode;
    }

    public function isSandboxMode()
    {
        if ($this->sandboxMode) {
            return true;
        }
        return false;
    }

    public function getGatewayCurrency()
    {
        return $this->gatewayCurrency;
    }

    public function getDebugMode()
    {
        return $this->debugMode;
    }

    public function isDebugMode()
    {
        if ($this->debugMode) {
            return true;
        }
        return false;
    }

    public function getHostUrl()
    {
        return $this->hostUrl;
    }

    public function getSuccessOrderStatusId()
    {
        return $this->successOrderStatusId;
    }

    public function getStatus()
    {
        return $this->Status;
    }

    public function isActive()
    {
        if ($this->active) {
            return true;
        }
        return false;
    }

    public function getCcStatus()
    {
        return $this->ccStatus;
    }

    public function isCcActive()
    {
        if ($this->ccStatus) {
            return true;
        }
        return false;
    }

    public function getCcTitle()
    {
        return $this->ccTitle;
    }

    public function getCcSortOrder()
    {
        return $this->ccSortOrder;
    }

    public function getCcIntegrationType()
    {
        return $this->ccIntegrationType;
    }

    public function isCcMerchantPage()
    {
        if ($this->ccIntegrationType == PAYFORT_FORT_INTEGRATION_TYPE_MERCAHNT_PAGE) {
            return true;
        }
        return false;
    }

    public function isCcMerchantPage2()
    {
        if ($this->ccIntegrationType == PAYFORT_FORT_INTEGRATION_TYPE_MERCAHNT_PAGE2) {
            return true;
        }
        return false;
    }

    public function getSadadStatus()
    {
        return $this->sadadStatus;
    }

    public function isSadadActive()
    {
        if ($this->sadadStatus) {
            return true;
        }
        return false;
    }

    public function getSadadTitle()
    {
        return $this->sadadTitle;
    }

    public function getSadadSortOrder()
    {
        return $this->sadadSortOrder;
    }

    public function getNapsStatus()
    {
        return $this->napsStatus;
    }

    public function isNapsActive()
    {
        if ($this->napsStatus) {
            return true;
        }
        return false;
    }

    public function getNapsTitle()
    {
        return $this->napsTitle;
    }

    public function getNapsSortOrder()
    {
        return $this->napsSortOrder;
    }

    public function getGatewayProdHost()
    {
        return $this->gatewayProdHost;
    }

    public function getGatewaySandboxHost()
    {
        return $this->gatewaySandboxHost;
    }

    public function getLogFileDir()
    {
        return $this->logFileDir;
    }

}

?>