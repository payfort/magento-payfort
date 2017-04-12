<?php

class Payfort_Pay_Block_Adminhtml_System_Config_Fieldset_Payment
        extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Add custom css class
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button '
            . ($this->_isPaymentEnabled($element) ? ' enabled' : '');
    }

    /**
     * Check whether current payment method is enabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isPaymentEnabled($element)
    {
        $groupConfig = $this->getGroup($element)->asArray();
        $activityPath = isset($groupConfig['activity_path']) ? $groupConfig['activity_path'] : '';

        if (empty($activityPath)) {
            return false;
        }

        $isPaymentEnabled = (string)Mage::getSingleton('adminhtml/config_data')->getConfigDataValue($activityPath);

        return (bool)$isPaymentEnabled;
    }

    /**
     * Return header title part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        return '';
    }

    /**
     * Return header comment part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * Get collapsed state on-load
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _getCollapseState($element)
    {
        return false;
    }
}
