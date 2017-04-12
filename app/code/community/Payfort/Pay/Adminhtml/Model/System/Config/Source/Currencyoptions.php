<?php

class Payfort_Pay_Adminhtml_Model_System_Config_Source_Currencyoptions {
    /*     * */

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'USD', 'label' => Mage::helper('adminhtml')->__('USD')),
            array('value' => 'AED', 'label' => Mage::helper('adminhtml')->__('AED')),
            array('value' => 'EUR', 'label' => Mage::helper('adminhtml')->__('EUR')),
            array('value' => 'EGP', 'label' => Mage::helper('adminhtml')->__('EGP')),
            array('value' => 'SAR', 'label' => Mage::helper('adminhtml')->__('SAR')),
            array('value' => 'KWD', 'label' => Mage::helper('adminhtml')->__('KWD')),
            array('value' => 'SYP', 'label' => Mage::helper('adminhtml')->__('SYP')),
            array('value' => 'QAR', 'label' => Mage::helper('adminhtml')->__('QAR')),
            array('value' => 'no_currency', 'label' => Mage::helper('adminhtml')->__('Use Default')),
        );
    }

}
