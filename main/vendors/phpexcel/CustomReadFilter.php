<?php

/**  Define a Read Filter class implementing PHPExcel_Reader_IReadFilter  */ 
class CustomReadFilter implements PHPExcel_Reader_IReadFilter 
{ 
	protected $rules = array();
	public function __construct($rules) {
		$this->rules = $rules;
	}
	
    public function readCell($column, $row, $worksheetName = '') { 
        if (!in_array($worksheetName, array_keys($this->rules))) return true;

		if ($row >= $this->rules[$worksheetName][1] && $row <= $this->rules[$worksheetName][3]) { 
			if (in_array($column,range($this->rules[$worksheetName][0],$this->rules[$worksheetName][2]))) { 
				return true; 
			} 
		}
		
        return false; 
    } 
}