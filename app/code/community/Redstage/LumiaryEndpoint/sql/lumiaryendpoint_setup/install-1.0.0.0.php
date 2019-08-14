<?php
$installer = $this;
$installer->startSetup();

$config = new Mage_Core_Model_Config();
$config ->saveConfig('lumiaryendpoint/core/token', Mage::helper('lumiaryendpoint')->generateToken(), 'default', 0);

$installer->endSetup();
