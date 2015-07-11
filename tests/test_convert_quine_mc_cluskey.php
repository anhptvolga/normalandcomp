

<?php

require_once '..\inout.php';

$typeOfVars = array();

class testConvertQuineMcCluskeyTest extends PHPUnit_Framework_TestCase {
	
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
	
	public function test_miniTest() {
		global $typeOfVars;
		$resExpression = "a || b";
		$expectedExpression = " a || b";
		$resTree = buildTree($resExpression, $typeOfVars);
		$expectedTree = buildTree($expectedExpression, $typeOfVars);
		
		$resTree->convertQuineMcCluskey();
		
		$this->assertEquals(isTreeEqual($resTree, $expectedTree, TRUE), "in mini test tree not equal");
		
	}
	
	public function test_intercRule() {
		global $typeOfVars;
		$resExpression = "a && b && c || c";
		$resTree = buildTree($resExpression, $typeOfVars);
		
		$resTree->convertQuineMcCluskey();
		
		$this->assertEquals(1, count($resTree->childrens), "sizeof childrens not correct");
		$this->assertEquals("c", $resTree->childrens[0]->name, "not correct convert");
		
	}
	
	public function test_glueRule() {
		global $typeOfVars;
		$resExpression = "a && b || !a && b";
		$resTree = buildTree($resExpression, $typeOfVars);
		$expectedTree = buildTree($expectedExpression, $typeOfVars);
		
		$resTree->convertQuineMcCluskey();
		
		$this->assertEquals(1, count($resTree->childrens), "sizeof childrens not correct");
		$this->assertEquals("b", $resTree->childrens[0]->name, "not correct convert");
	}
	
	public function test_reduced() {
		global $typeOfVars;
		$resExpression = "a && b || ( b && c || c && d )";
		$expectedExpression = "a && b || b && c || c && d";
		$resTree = buildTree($resExpression, $typeOfVars);
		$expectedTree = buildTree($expectedExpression, $typeOfVars);
		
		$resTree->convertQuineMcCluskey();
		
		$this->assertEquals(isTreeEqual($resTree, $expectedTree, TRUE), "in reduced test tree not equal");
		
	}
	
	public function test_onlyOperand() {
		global $typeOfVars;
		$resExpression = "";
		$expectedExpression = "!a || c && b && a";
		$resTree = buildTree($resExpression, $typeOfVars);
		$expectedTree = buildTree($expectedExpression, $typeOfVars);
		
		$resTree->convertQuineMcCluskey();
		
		$this->assertEquals(isTreeEqual($resTree, $expectedTree, TRUE), "in mini test tree not equal");
		
	}
	
	public function test_withCompOp() {
		global $typeOfVars;
		$resExpression = "a || b";
		$expectedExpression = " a || b";
		$resTree = buildTree($resExpression, $typeOfVars);
		$expectedTree = buildTree($expectedExpression, $typeOfVars);
		
		$resTree->convertQuineMcCluskey();
		
		$this->assertEquals(isTreeEqual($resTree, $expectedTree, TRUE), "in mini test tree not equal");
		
	}
	
	public function test_mixedType() {
		global $typeOfVars;
		$resExpression = "a || b";
		$expectedExpression = " a || b";
		$resTree = buildTree($resExpression, $typeOfVars);
		$expectedTree = buildTree($expectedExpression, $typeOfVars);
		
		$resTree->convertQuineMcCluskey();
		
		$this->assertEquals(isTreeEqual($resTree, $expectedTree, TRUE), "in mini test tree not equal");
		
	}
	
	public function test_testEqual1() {
		
	}
	
	
	public static function tearDownAfterClass() {
		
	}
}

?>