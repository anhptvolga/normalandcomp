

<?php

require_once '..\classes\BaseNode.php';

class buildTreeTest extends PHPUnit_Framework_TestCase {
	
	public $typeOfVars;
	
	public static function setUpBeforeClass() {
		$this->typeOfVars["5"] = VarType::CONSTINT;
		$this->typeOfVars["2"] = VarType::CONSTINT;
		$this->typeOfVars["0"] = VarType::CONSTINT;
		$this->typeOfVars["-2"] = VarType::CONSTINT;
		$this->typeOfVars["1"] = VarType::CONSTINT;
		$this->typeOfVars["2.4"] = VarType::CONSTFLOAT;
		$this->typeOfVars["0.5"] = VarType::CONSTFLOAT;
		$this->typeOfVars["a"] = VarType::INT;
		$this->typeOfVars["b"] = VarType::FLOAT;
		$this->typeOfVars["d"] = VarType::INT;
		$this->typeOfVars["e"] = VarType::INT;
		$this->typeOfVars["f"] = VarType::INT;
		$this->typeOfVars["i"] = VarType::INT;
		$this->typeOfVars["x"] = VarType::INT;
		$this->typeOfVars["y"] = VarType::INT;
		$this->typeOfVars["arr"] = VarType::INT;
	}
	
	public function test_oneOperand() {
		
	}
	
	public function test_oneOperator() {
		
	}
	
	public function test_notEnoughOperand() {
		
	}
	
	public function test_notEnoughOperator() {
		
	}
	
	public function test_notHaveType() {
		
	}
	
	public function test_onlyUnary() {
		
	}
	
	public function test_onlyBinary() {
		
	}
	
	public function test_onlyKdim() {
		
	}
	
	public function test_mixedType() {
		
	}
	
	public function test_defOperator() {
		
	}
	
	public function test_referOper() {
		
	}
	
	public function test_unaryMinus() {
		
	}
	
	public function test_minus() {
		
	}
	
	public function test_modoperator() {
		
	}
	
	public function test_equanOper() {
		
	}
	
	public function test_notEqualOper() {
		
	}
	
	public function test_greaterOper() {
		
	}
	
	public function test_lessOper() {
		
	}
	
	public function test_greaterEqualOp() {
		
	}
	
	public function test_lessEqualOp() {
		
	}
	
	public function test_plusAssOp() {
		
	}
	
	public function test_minusAssOp() {
		
	}
	
	public function test_multiAssOp() {
		
	}
	
	public function test_divAssOp() {
		
	}
	
	public function test_shlAssOp() {
		
	}
	
	public function test_shrAssOp() {
		
	}
	
	public function test_shlOp() {
		
	}
	
	public function test_shrOp() {
		
	}
	
	public function test_subscOp() {
		
	}
	
	public function test_ptMemOp() {
		
	}
	
	public function test_memOp() {
		
	}
	
	public function test_powFunc() {
		
	}
	
	public static function tearDownAfterClass() {
		
	}
}

?>