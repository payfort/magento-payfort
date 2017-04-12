<?php

class Payfort_Pay_Block_Form_Gateway extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        if (Mage::app()->getLayout()->getBlock('head')) {
            Mage::app()->getLayout()->getBlock('head')->addLinkRel('stylesheet', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
            Mage::app()->getLayout()->getBlock('head')->addCss('css/payfort/merchant-page.css');
            Mage::app()->getLayout()->getBlock('head')->addJs('payfort/payfort_fort.js');
        }

        return parent::_prepareLayout();
    }

}
