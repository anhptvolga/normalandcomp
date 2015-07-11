

<?php

require_once '..\classes\KDimNode.php';
require_once '..\inout.php';

class openBracketTest extends PHPUnit_Framework_TestCase {
	
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
	
	public function mydataProvider() {
		
		return array(
			array('c * (a + b + d)', 'c*a + c*b + c*d'),
			array('(a + c) * (b + d)', 'a*d + c*b + c*d + a*b'),
			array('(c + d) * (a + c) * b', 'd*a*b + c*c*b + d*c*b + c*a*b'),
			array('(a + b) * pow(x,y) * (c%d)', 'a*pow(x,y)*(c%d) + b*pow(x,y)*(c%d)')
		);
		  
	}
	
	/**
	 * @dataProvider mydataProvider
	 */
	public function test_openbracket($expr1, $expr2) {
		global $typeOfVars;
		$tree1 = buildTree($expr1, $typeOfVars);
		$tree2 = buildTree($expr2, $typeOfVars);
		
		$opened = $tree1->openBracket();
	
		$this->assertTrue(isTreeEqual($opened, $tree2, TRUE));
		
		
		
		}
}

?>