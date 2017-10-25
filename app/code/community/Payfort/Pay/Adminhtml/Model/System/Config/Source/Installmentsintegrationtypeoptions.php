<?php

class Payfort_Pay_Adminhtml_Model_System_Config_Source_Installmentsintegrationtypeoptions {
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
        );
    }

}
