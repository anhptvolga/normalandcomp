
<?php

require_once '..\inout.php';

$typeOfVars = array();

ini_set('memory_limit', '-1');

class convertTest extends PHPUnit_Framework_TestCase {
		
	public static function setUpBeforeClass() {
	}
	
	
	public function mydataProvider() {
		return array(  
			array('a - 3 * 4 + 4','a + -8'),
			array('b + a + d + e','e + d + b + a'),
			array('pow(a, 3)','a * a * a'),
			array(' pow(a, 2.4)',' pow(a, 2.4)'),
			array(' pow(a,c)','pow(a,c)'),
			array('(a+b)*2','a + a + b + b'),
			array('(a+b) * -2','- a - a - b - b'),
			array('(a+b) * 2.4','a*2.4 + b*2.4'),
			array('(a + b - c)/d','a/d + b/d - c/d'),
			array('(a * b * c)/d',' (a*b*c)*(1/d)'),
			array('a[i]','*(a+i)'),
			array('a[0]','*a'),
			array('(*a).b','a->b'),
			array('a[i].c','(a+i)->c'),
			array('a[0].b','a->b'),
			array(' (*(&(a[2*k]))).b[i+2-3]','*((a+k+k)->b+i-1)'),
			array('a += b','a = a + b'),
			array('a >>= b','a = a >> b'),
			array('a - 5 > 3 + 2*(b-3)','a-b-b-2>0'),
			array('(b << 1) - 4 >= b / c','!(b/c - b - b + 4 > 0)'),
			array('b * c + 5 - d < c + a + 3','a + c + d - 2 - b * c > 0'),
			array('a + 3 <= b - c','!(3 + a + c - b > 0)'),
			array('a != b','!(a - b == 0)'),
			array('!!!a','!a'),
			array('!!!!a','a'),
			array('a && a','a'),
			array('a << 2','a + a + a + a'),
			array('a >> 4','a / 16'),
			array('*(&a)','a'),
			array('(a + b - c)*(-2)', 'c + c - b - b - a - a'),  
			array('!(a>b) && a != b', 'b-a > 0'), 
			array('(a/b)*(c/d)*e*f', 'a*c*e*f/(b*d)')
		);
	}
	
	/**
	 * @dataProvider mydataProvider
	 */
	public function test_convert($expr1, $expr2) {
		global $typeOfVars;
		$tree1 = buildTree($expr1, $typeOfVars);
		$tree2 = buildTree($expr2, $typeOfVars);
		
		$tree1->ptonewchild = null;
		$tree1->convert($tree1);
		while ($tree1->ptonewchild != null){
			$tree1 = $tree1->ptonewchild;
			$tree1->ptonewchild = null;
			$tree1->convert($tree1);
		}
		
		$tree2->ptonewchild = null;
		$tree2->convert($tree2);
		while ($tree2->ptonewchild != null){
			$tree2 = $tree2->ptonewchild;
			$tree2->ptonewchild = null;
			$tree2->convert($tree2);
		}
		
		$this->assertTrue(isTreeEqual($tree1, $tree2, TRUE));
		
		
		$tree1->deleteChildrens();
		$tree2->deleteChildrens();
		unset($tree1);
		unset($tree2);
	}	
	

}

?>