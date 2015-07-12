

<?php
require_once '..\classes\KDimNode.php';
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
		
	public function mydataProvider() {
		return array(
			array('a || b','a || b'),
			array('a && b && c || c','c'),
			array('a && b || !a && b','b'),
			array('a && b || b && c || c && d','a && b || b && c || c && d'),
			array('!a&&!b&&!c&&d || !a&&!b&&c&&d || !a&&b&&!c&&d || !a&&b&&c&&d || a&&b&&c&&!d || a&&b&&c&&d','!a&&d || c&&b&&a'),
			array('a > b && b > c && a > d || b > c','b > c'),
			array('!(a>b) && (c<x-y) && !(b==2) || (a>b) && !(c<x-y) && !(b==2) || (a>b) && !(c<x-y) && (b==2)','!(c<x-y)&&(a>b) || (c<x-y)&&!(a>b)&&!(b==2)')
			
		);
	}
	
	/**
	 * @dataProvider mydataProvider
	 */
	public function test_sortchild($expr1, $expr2) {
		global $typeOfVars;
		$tree1 = buildTree($expr1, $typeOfVars);
		$tree2 = buildTree($expr2, $typeOfVars);
		
		$tree1->convertQuineMcCluskey();
		
		$tmp;
		if (get_class($tree2) != 'OrLogicOperator') {
			$tmp = new OrLogicOperator();
			array_push($tmp->childrens, $tree2);
			$tree2 = $tmp;	
		}
		
		$this->assertTrue(isTreeEqual($tree1, $tree2, TRUE));
	}
	
}

?>