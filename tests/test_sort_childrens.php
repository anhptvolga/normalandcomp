

<?php

require_once '..\classes\k_dim_node.php';
require_once '..\inout.php';

$typeOfVars = array();

class sortChildrensTest extends PHPUnit_Framework_TestCase {
	
	public static function setUpBeforeClass() {
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
		$tree1 = build_tree($expr1, $typeOfVars);
		$tree2 = build_tree($expr2, $typeOfVars);
		
		$tree1->sort_childrens();
		$tree2->sort_childrens();
		
		$this->assertTrue(is_tree_equal($tree1, $tree2, FALSE));
	}
}

?>