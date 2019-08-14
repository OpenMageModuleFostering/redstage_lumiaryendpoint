<?php

class Redstage_LumiaryEndpoint_Model_Operation_Timer
{
    protected $_startTime;
    protected $_endTime;
    
    public function start()
    {
        $this->_startTime = microtime(true);
        
        return $this;
    }
    
    public function isStarted()
    {
        return isset($this->_startTime);
    }
    
    public function stop($decimals = 2)
    {
        if(!$this->isStarted()) {
            return 0;
        }
        
        $this->_endTime = microtime(true);
        $time = $this->_endTime - $this->_startTime;
        $this->reset();
        
        return number_format($time, $decimals);
    }
    
    public function isStopped()
    {
        return isset($this->$_endTime);
    }
    
    public function lap($decimals = 2)
    {
        $lapTime = $this->stop($decimals);
        $this->start();
        
        return $this;
    }
    
    public function check($decimals = 2)
    {
        return number_format(microtime(true) - $this->_startTime, $decimals);
    }
    
    public function reset()
    {
        $this->_startTime = null;
        $this->_endTime = null;
        
        return $this;
    }
}
