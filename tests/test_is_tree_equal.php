


<?php

require_once '..\inout.php';

$typeOfVars = array();


class isTreeEqualTest extends PHPUnit_Framework_TestCase {
		
	public static function setUpBeforeClass() {
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
		$tree1 = build_tree($expr1, $typeOfVars);
		$tree2 = build_tree($expr2, $typeOfVars);
		$this->assertEquals($result, is_tree_equal($tree1, $tree2, FALSE));
	}
	
	
}

?>