<?php

class Payfort_Pay_Adminhtml_Model_System_Config_Source_Integrationtypeoptions {
    /*     * */

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'redirection', 'label' => Mage::helper('payfort')->__("Redirection")),
            array('value' => 'merchantPage', 'label' => Mage::helper('payfort')->__("Merchant Page")),
            array('value' => 'merchantPage2', 'label' => Mage::helper('payfort')->__("Merchant Page 2.0")),
        );
    }

}
