<?php

abstract class Redstage_LumiaryEndpoint_Model_Data_Abstract
    extends Varien_Object
{
    protected $_ignoreKeys;
    
    public function diff(Varien_Object $b,
        array $ignoreAttributes = array())
    {        
        $diff = $this->_diff($this->getData(), $b->getData());

        foreach ($ignoreAttributes as $ignoreKey) {
            unset($diff[$ignoreKey]);
        }
        
        return $diff;
    }
    
    protected function _diff(array $a, array $b)
    {
        $diff = array();
        foreach ($a as $k => $v) {
            if (array_key_exists($k, $b)) {
                if (is_array($v)) {
                    $rad = $this->_diff($v, $b[$k]);

                    if (count($rad)) {
                        $diff[$k] = $rad;
                    }
                } else {
                    if ($v != $b[$k]) {
                        $diff[$k] = $v;
                    }
                }
            } else {
                $diff[$k] = $v;
            }
        }

        return $diff; 
    }
}
