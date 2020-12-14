<?php

class Payfort_Pay_Adminhtml_Model_System_Config_Source_madaoptions {
    /*     * */

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'Enabled', 'label' => Mage::helper('payfort')->__("Enabled")),
            array('value' => 'Disabled', 'label' => Mage::helper('payfort')->__("Disabled")),
        );
    }

}
