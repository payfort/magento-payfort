<?php

class Payfort_Fort_Language
{
    
    public static function __($input, $args = array(), $domain = 'payfort/data')
    {
        return Mage::helper($domain)->__($input);
    }

    public static function getCurrentLanguageCode() 
    {
        $language = Mage::app()->getLocale()->getLocaleCode();
        return substr($language, 0, 2);
    }
}

?>