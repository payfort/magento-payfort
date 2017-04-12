<?php

class Payfort_Pay_Adminhtml_Model_System_Config_Source_Gatewaycurrencyoptions {
    /*     * */

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'base', 'label' => Mage::helper('adminhtml')->__('Base Currency')),
            array('value' => 'front', 'label' => Mage::helper('adminhtml')->__('Front Currency')),
        );
    }

}
