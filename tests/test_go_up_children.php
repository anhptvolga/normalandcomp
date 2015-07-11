

<?php

require_once '..\inout.php';

$typeOfVars = array();

class goUpChildrenTest extends PHPUnit_Framework_TestCase {
		
	public static function setUpBeforeClass() {
		global $typeOfVars;
		$typeOfVars["5"] = VarType::CONSTINT;
		$typeOfVars["2"] = VarType::CONSTINT;
		$typeOfVars["0"] = VarType::CONSTINT;
		$typeOfVars["-2"] = VarType::CONSTINT;
		$typeOfVars["1"] = VarType::CONSTINT;
		$typeOfVars["2.4"] = VarType::CONSTFLOAT;
		$typeOfVars["0.5"] = VarType::CONSTFLOAT;
		$typeOfVars["a"] = VarType::INT;
		$typeOfVars["b"] = VarType::FLOAT;
		$typeOfVars["d"] = VarType::INT;
		$typeOfVars["e"] = VarType::INT;
		$typeOfVars["f"] = VarType::INT;
		$typeOfVars["i"] = VarType::INT;
		$typeOfVars["x"] = VarType::INT;
		$typeOfVars["y"] = VarType::INT;
		$typeOfVars["arr"] = VarType::INT;
	}
	
	public function test_multiOp() {
		
	}
	
	public function test_plusOp() {
		
	}
	
	public function test_andLogicOp() {
		
	}
	
	public function test_orLogicOp() {
		
	}
	
	public function test_notThingToUp() {
		
	}
	
	
	public static function tearDownAfterClass() {
		
	}
}

?>