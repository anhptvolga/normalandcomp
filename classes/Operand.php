
<?php

require_once 'BaseNode.php';

class Operand extends BaseNode {
	
	public $name = null;
	public $number = null;
	public $typeOfVar = null;
	
	function __construct($name="", $number=null, $typeOfVar = null)	{
		$this->name = $name;
		$this->number = $number;
		$this->typeOfVar = $typeOfVar;
		$this->treeInString = $name;
		$this->pToNewChild = null;
	}
	
	public function convert($parent) {
			
	}
	
	public function convertEachChildrens() {
		
	}
	
	public function calculateTreeInString() {
		$this->treeInString = $this->name;	
	}
	
}

?>