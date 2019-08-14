<?php

abstract class Redstage_LumiaryEndpoint_Model_Data_Table_Abstract
    implements Iterator, ArrayAccess
{    
    protected $_rows = array();
    protected $_headings = array();
    protected $_position = 0;
    
    public function __construct()
    {
        $this->_position = 0;
    }

    public function setHeadings(array $headings)
    {                
        $this->_headings = $headings;
    }

    public function getHeadings()
    {
        foreach ($this->_headings as $i=>$heading) {
            if (!$heading instanceof Varien_Object) {
                $this->_headings[$i] = new Varien_Object(array(
                    'name' => $heading,
                    'index' => $i
                ));
            }
        }

        return $this->_headings;
    }
    
    public function setRows(array $rows)
    {
        $this->_rows = $rows;
    }
    
    public function addRow($row)
    {
        $this->_rows[] = $row;
    }
    
    public function current()
    {
        return $this->_convertRow($this->_position);
    }
    
    public function key()
    {
        return $this->_position;
    }
    
    public function next()
    {
        ++$this->_position;
    }
    
    public function rewind()
    {
        $this->_position = 0;
    }
    
    public function valid()
    {
        return isset($this->_rows[$this->_position]);
    }
    
    public function slice($offset, $length = null)
    {
        $this->_rows = array_slice($this->_rows, $offset, $length);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_rows[] = $value;
        } else {
            $this->_rows[$offset] = $value;
        }
    }
    
    public function offsetExists($offset)
    {
        return isset($this->_rows[$offset]);
    }
    
    public function offsetUnset($offset)
    {
        unset($this->_rows[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return isset($this->_rows[$offset]) ? $this->_convertRow($offset) : null;
    }
    
    public function count()
    {
        return count($this->_rows);
    }

    protected function _underscore($name)
    {
        return str_ireplace(' ', '_', strtolower(trim($name)));
    }
    
    abstract protected function _convertRow($position);
}
