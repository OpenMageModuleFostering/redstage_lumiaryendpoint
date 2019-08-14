<?php

class Redstage_LumiaryEndpoint_Model_Operation_Status
    extends Varien_Object
{
    public function successful($data)
    {
        $this->setSuccess(true);
        $this->setError(false);
        
        foreach ($data as $k=>$v) {
            $this->setData($k, $v);
        }
        
        return $this;
    }
    
    public function failed($message = "")
    {
        $this->setSuccess(false);
        $this->setError(true);
        $this->setMessage($message);
        
        return $this;
    }
}
