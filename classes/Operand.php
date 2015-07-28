
<?php

require_once 'base_node.php';

class qtype_correctwriting_operand extends qtype_correctwriting_base_node {
	
	public $name = null;
	public $number = null;
	public $typeOfVar = null;
	
	function __construct($name="", $number=null, $typeOfVar = null)	{
		$this->name = $name;
		$this->number = $number;
		$this->treeinstring = $name;
		$this->ptonewchild = null;
	}
	
	public function convert($parent) {
			
	}
	
	public function convert_each_childrens() {
		
	}
	
	public function calculate_tree_in_string() {
		$this->treeinstring = $this->name;	
	}
	
	public function delete_childrens()	{
		unset ($this->name);
		unset ($this->number);
		unset ($this->typeOfVar);
		$this->name = null;
		$this->number = null;
		$this->typeOfVar = null;
	}
	
	public function __clone() {
		
	}
}

?>