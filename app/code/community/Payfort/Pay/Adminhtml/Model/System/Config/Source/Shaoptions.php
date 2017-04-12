<?php

class Payfort_Pay_Adminhtml_Model_System_Config_Source_Shaoptions {
    /*     * */

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'SHA-1', 'label' => Mage::helper('payfort')->__('SHA-1')),
            array('value' => 'SHA-256', 'label' => Mage::helper('payfort')->__('SHA-256')),
            array('value' => 'SHA-512', 'label' => Mage::helper('payfort')->__('SHA-512')),
        );
    }

}
