

<?php
require_once '..\classes\k_dim_node.php';
require_once '..\inout.php';

$typeOfVars = array();

class testConvertQuineMcCluskeyTest extends PHPUnit_Framework_TestCase {
	
	public static function setUpBeforeClass() {
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
		$tree1 = build_tree($expr1, $typeOfVars);
		$tree2 = build_tree($expr2, $typeOfVars);
		
		$tree1->convert_quine_mc_cluskey();
		
		$tmp;
		if (get_class($tree2) != 'qtype_correctwriting_or_logic_operator') {
			$tmp = new qtype_correctwriting_or_logic_operator();
			array_push($tmp->childrens, $tree2);
			$tree2 = $tmp;	
		}
		
		$this->assertTrue(is_tree_equal($tree1, $tree2, TRUE));
	}
	
}

?>