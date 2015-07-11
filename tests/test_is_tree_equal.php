


<?php

require_once '..\inout.php';

$typeOfVars = array();


class isTreeEqualTest extends PHPUnit_Framework_TestCase {
		
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
			array('a * b * c','b * c * a', FALSE),
			array('a + b + c + e << f','a + b + c + e << f', TRUE),
			array('d = a[i] >> c','d = a[i] >> c', TRUE),
			array('& a','& a', TRUE),
			array('a > b && c && d ','a > e && c && d ', FALSE),
			array('a + b + *c',' x *  y', FALSE),
			array('a + 2.00', 'a + 2', FALSE)
		);
	}
	
	/**
	 * @dataProvider mydataProvider
	 */
	public function test_compareTree($expr1, $expr2, $result) {
		global $typeOfVars;
		$tree1 = buildTree($expr1, $typeOfVars);
		$tree2 = buildTree($expr2, $typeOfVars);
		$this->assertEquals($result, isTreeEqual($tree1, $tree2, FALSE));
	}
	
	
}

?>