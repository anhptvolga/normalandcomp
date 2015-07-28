

<?php

require_once '..\classes\KDimNode.php';
require_once '..\inout.php';

class openBracketTest extends PHPUnit_Framework_TestCase {
	
	public static function setUpBeforeClass() {
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
		$tree1 = build_tree($expr1, $typeOfVars);
		$tree2 = build_tree($expr2, $typeOfVars);
		
		$opened = $tree1->open_bracket();
	
		$this->assertTrue(is_tree_equal($opened, $tree2, TRUE));
		
		
		
		}
}

?>