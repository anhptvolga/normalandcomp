

<?php

require_once '..\classes\KDimNode.php';
require_once '..\inout.php';

$typeOfVars = array();

class sortChildrensTest extends PHPUnit_Framework_TestCase {
	
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
			array('c * a * 4 * b', 'c * b * a * 4'),
			array('!a && (x>y) && (c > d)', 'x > y && c > d && !a'),
			array('!c && a && d && !b', 'd &&  a && !c && !b'),
			array('e + d + c + b + a', 'e + d + c + b + a'),
			array('2 * a * b * e * k', 'k * e * b * a * 2'),
			array(' a + 5 + arr[pow(a,b) * d << c] + arr[a/2]', '5 + arr[a/2] + arr[pow(a,b) * d << c] + a')
		);
	}
	
	/**
	 * @dataProvider mydataProvider
	 */
	public function test_sortchild($expr1, $expr2) {
		global $typeOfVars;
		$tree1 = buildTree($expr1, $typeOfVars);
		$tree2 = buildTree($expr2, $typeOfVars);
		
		$tree1->sortChildrens();
		$tree2->sortChildrens();
		
		$this->assertTrue(isTreeEqual($tree1, $tree2, FALSE));
	}
}

?>