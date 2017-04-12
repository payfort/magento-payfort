<?php

class Payfort_Pay_Block_Note extends Mage_Adminhtml_Block_System_Config_Form_Field {

	function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$element_id = str_replace('payment_payfort_', '', $element->getId());
		switch($element_id):
			case 'more_info':
				return '<a href="http://www.payfort.com/contact-us/" target="_blank">Contact Us</a>';
                break;
                case 'host_to_host_url':
                    $defaultStoreId = Mage::app()
                            ->getWebsite(true)
                            ->getDefaultGroup()
                            ->getDefaultStoreId();

                return  Mage::app()->getStore($defaultStoreId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK). 'payfort/payment/response/';
                break;
			break;
			// case 'how_to_test':
				// return '<a href="https://secure.payfort.com/Ncol/PayFort_Testacc_EN.pdf?CSRFSP=%2fncol%2ftest%2fbackoffice%2fsupportgetdownloaddocument.asp&CSRFKEY=83E267BD93379EC7A63B9D5BDBE67B83E81240E9&CSRFTS=20130906221442&branding=PAYFORT" target="_blank">How to create a test account</a>';
			// break;
			case 'feedback_urls':
				return '

				Accepturl: http://[example.com]/[store lanuage code]/payfort/payment/response?response_type=accept <br />
				Declineurl: http://[example.com]/[store lanuage code]/payfort/payment/response?response_type=decline <br />
				Exceptionurl: http://[example.com]/[store lanuage code]/payfort/payment/response?response_type=exception <br />
				Cancelurl: http://[example.com]/[store lanuage code]/payfort/payment/response?response_type=cancel <br />


				';
			break;
		endswitch;
	}
}
