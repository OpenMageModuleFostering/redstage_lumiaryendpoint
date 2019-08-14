<?php

class Redstage_LumiaryEndpoint_Helper_Logger
    extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED     = 'lumiaryendpoint/logging/enabled';
    const XML_PATH_FILE        = 'lumiaryendpoint/logging/file';

    public function log($message, $level = null, $file = '')
    {
        if (!Mage::getStoreConfig(self::XML_PATH_ENABLED)) {
            return;
        }

        $file = empty($file) ? Mage::getStoreConfig(self::XML_PATH_FILE) : $file;
        Mage::log($message, $level, $file);
    }
}
