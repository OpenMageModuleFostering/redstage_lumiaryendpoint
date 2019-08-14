<?php

class Redstage_LumiaryEndpoint_Helper_Data
    extends Mage_Core_Helper_Abstract
{
	public function isEnabled()
	{
		return (Mage::getConfig()->getModuleConfig('Redstage_LumiaryEndpoint')->is('active', 'true')
			&& Mage::getStoreConfig('lumiaryendpoint/core/enabled'));
	}
    
    public function checkToken($token)
    {
        return ($this->getToken()) && $token === $this->getToken();
    }
    
    public function getToken()
    {
        return Mage::getStoreConfig('lumiaryendpoint/core/token');
    }
    
    public function generateToken($len = 8, $chars = null)
    {
        return strtolower(implode('-', array(
            Mage::helper('core')->getRandomString($len, $chars),
            Mage::helper('core')->getRandomString($len/2, $chars),
            Mage::helper('core')->getRandomString($len/2, $chars),
            Mage::helper('core')->getRandomString($len/2, $chars),
            Mage::helper('core')->getRandomString($len + 2, $chars)
        )));

    }
    
    public function log($message, $severity = null)
    {
        Mage::helper('lumiaryendpoint/logger')->log($message, $severity);
    }
}
