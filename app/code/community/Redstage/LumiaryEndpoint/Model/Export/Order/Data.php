<?php

class Redstage_LumiaryEndpoint_Model_Export_Order_Data
    extends Redstage_LumiaryEndpoint_Model_Data_Abstract
{
    public function generateXml()
    {
        $xml = new SimpleXMLElement("<order></order>");

        $this->_arrayToXml($this->getOrder(), $xml);
        
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
    }
    
    protected function _arrayToXml($data, &$xml, $parentKey = "")
    {
        foreach ($data as $key => $value) {
            if (is_array($value) && array_keys($value) === range(0, count($value) - 1)) {
                $this->_arrayToXml($value, $xml, $key);
            } elseif (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild($key);
                    $this->_arrayToXml($value, $subnode, $key);
                } else {                    
                    $subnode = $xml->addChild($parentKey);
                    $this->_arrayToXml($value, $subnode, $key);
                }
            } else {
                $xml->addChild($key,$value);
            }
        }
    }
}
