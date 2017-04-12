<?php
class Payfort_Pay_Block_Form_Cc_Notsaved extends Payfort_Pay_Block_Form_Gatewaycc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payfort/form/merchant-page2.phtml');
    }
        
    /**
     * Retrieve availables credit card types
     *
     * @return array
     */
    public function getCcAvailableTypes()
    {
        $types = $this->_getConfig()->getCcTypes();
        if ($method = $this->getMethod()) {
            //$availableTypes = $method->getConfigData('cctypes');
            $availableTypes = Mage::helper('payfort/data')->getCcTypes();
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;
    }
    
}