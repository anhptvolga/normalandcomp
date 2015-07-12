
<?php

require_once '..\inout.php';

$typeOfVars = array();

ini_set('memory_limit', '-1');

class convertTest extends PHPUnit_Framework_TestCase {
		
	public static function setUpBeforeClass() {
		global $typeOfVars;
		$typeOfVars["5"] = VarType::CONSTINT;
		$typeOfVars["2"] = VarType::CONSTINT;
		$typeOfVars["3"] = VarType::CONSTINT;
		$typeOfVars["2.00"] = VarType::CONSTFLOAT;
		$typeOfVars["2.4"] = VarType::CONSTFLOAT;
		$typeOfVars["0"] = VarType::CONSTINT;
		$typeOfVars["-2"] = VarType::CONSTINT;
		$typeOfVars["1"] = VarType::CONSTINT;
		$typeOfVars["16"] = VarType::CONSTINT;
		$typeOfVars["-1"] = VarType::CONSTINT;
		$typeOfVars["-8"] = VarType::CONSTINT;
		$typeOfVars["2.4"] = VarType::CONSTFLOAT;
		$typeOfVars["0.5"] = VarType::CONSTFLOAT;
		$typeOfVars["a"] = VarType::INT;
		$typeOfVars["b"] = VarType::FLOAT;
		$typeOfVars["c"] = VarType::INT;
		$typeOfVars["d"] = VarType::INT;
		$typeOfVars["e"] = VarType::INT;
		$typeOfVars["f"] = VarType::INT;
		$typeOfVars["i"] = VarType::INT;
		$typeOfVars["k"] = VarType::INT;
		$typeOfVars["x"] = VarType::INT;
		$typeOfVars["y"] = VarType::INT;
		$typeOfVars["arr"] = VarType::INT;
	}
	
	
	public function mydataProvider() {
		return array(
			//array('a - 3 * 4 + 4','a + -8'),
			//array('b + a + d + e','e + d + b + a'),
			//array('pow(a, 3)','a * a * a'),
			//array(' pow(a, 2.4)',' pow(a, 2.4)'),
			//array(' pow(a,c)','pow(a,c)'),
			//array('(a+b)*2','a + a + b + b'),
			//array('(a+b) * -2','- a - a - b - b'),
			//array('(a+b) * 2.4','a*2.4 + b*2.4'),
			array('(a + b - c)/d','a/d + b/d - c/d')
			//array('','')
			//array('','')
			//array('','')
			//array('','')
			//array('','')
		);
	}
	
	/**
	 * @dataProvider mydataProvider
	 */
	public function test_convert($expr1, $expr2) {
		global $typeOfVars;
		$tree1 = buildTree($expr1, $typeOfVars);
		$tree2 = buildTree($expr2, $typeOfVars);
		
		$file = fopen("tree1.gv", "w");
		fwrite($file,"digraph {\n");
		printTreeToDOT($file, $tree1);
		fwrite($file,'}');
		fclose($file);
		
		$file = fopen("tree2.gv", "w");
		fwrite($file,"digraph {\n");
		printTreeToDOT($file, $tree2);
		fwrite($file,'}');
		fclose($file);
		
		$tree2->pToNewChild = null;
		$tree2->convert($tree2);
		
		$tree1->pToNewChild = null;
		$tree1->convert($tree1);
		while ($tree1->pToNewChild != null){
			$tree1 = $tree1->pToNewChild;
			$tree1->pToNewChild = null;
			$tree1->convert($tree1);
		}
		
		while ($tree2->pToNewChild != null){
			$tree2 = $tree2->pToNewChild;
			$tree2->pToNewChild = null;
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