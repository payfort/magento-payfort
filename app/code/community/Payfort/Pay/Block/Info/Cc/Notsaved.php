<?php
class Payfort_Pay_Block_Info_Cc_Notsaved extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payfort/info/merchant-page2.phtml');
    }

    /**
     * Render as PDF
     *
     * @return string
     */
//    public function toPdf()
//    {
//        $this->setTemplate('/pdf/direct_notsaved.phtml');
//        return $this->toHtml();
//    }

    public function getCcTypeName($type)
    {
        return Mage::helper('payfort/data')->getCcTypeName($type);
    }

}