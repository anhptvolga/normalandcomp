

<?php

require_once '..\classes\BaseNode.php';
require_once '..\inout.php';

$typeOfVars = array();

class buildTreeTest extends PHPUnit_Framework_TestCase {

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
	
	public function test_oneOperand() {
		global $typeOfVars;
		$expression = "a";
		$result = buildTree($expression, $typeOfVars);
		$this->assertTrue(is_a($result, 'Operand'), "type of root not correct");
		$this->assertEquals($result->name === "a", "name of operand not correct");
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Expression "*" invalid
	 */
	public function test_oneOperator() {
		global $typeOfVars;
		$expression = "*";
		$result = buildTree($expression, $typeOfVars);
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Expression "a +" invalid
	 */
	public function test_notEnoughOperand() {
		global $typeOfVars;
		$expression = "a +";
		$result = buildTree($expression, $typeOfVars);
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Expression "a + b c" invlid
	 */
	public function test_notEnoughOperator() {
		global $typeOfVars;
		$expression = "a + b c";
		$result = buildTree($expression, $typeOfVars);
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Not found type of: woie
	 */
	public function test_notHaveType() {
		global $typeOfVars;
		$expression = "a + b + woie";
		$result = buildTree($expression, $typeOfVars);
	}
	
	public function test_onlyUnary() {
		global $typeOfVars;
		$expression = "!a";
		$result = buildTree($expression, $typeOfVars);
		$this->assertTrue(is_a($result, 'NotLogicOperator'), "type of root error");
		$this->assertTure(is_a($result->children, 'Operand'), "type of children error");
		$this->assertEquals("a", $result->childrens->name, "name of operand error");
	}
	
	public function test_onlyBinary() {
		global $typeOfVars;
		$expression = "c = a / b";
		$result = buildTree($expression, $typeOfVars);
		$this->assertTrue(is_a($result, 'AssignOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("c", $result->left->name, "name of left operand error");
		
		$this->assertTrue(is_a($result->right, 'DivOperator'), "type of right children error");
		
		$this->assertTrue(is_a($result->right->left, 'Operand'), "type of left children div operator error");
		$this->assertEquals("a", $result->right->left->name, "name of left operand of div opertor error");
		
		$this->assertTrue(is_a($result->right->right, 'Operand'), "type of right children div operator error");
		$this->assertEquals("b", $result->right->right->name, "name of right operand of div opertor error");
	}
	
	public function test_onlyKdim() {
		global $typeOfVars;
		$expression = "a * b * c";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'MultiOperator'), "type of root error");
		$this->assertEquals(3, count($result->childrens), "number of childrens error");
		
		$this->assertTrue(is_a($result->childrens[0], 'Operand'), "type of 1-st children error");
		$this->assertTrue(is_a($result->childrens[1], 'Operand'), "type of 2-sd children error");
		$this->assertTrue(is_a($result->childrens[2], 'Operand'), "type of 3-rd children error");
		
		$this->assertEquals("a", $result->childrens[0]->name, "name of 1-st children error");
		$this->assertEquals("b", $result->childrens[1]->name, "name of 2-sd children error");
		$this->assertEquals("c", $result->childrens[2]->name, "name of 3-rd children error");
		
	}
	
	public function test_mixedType() {
		global $typeOfVars;
		$expression = "a = !a && b && c || a && !b && !c";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'AssignOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left operand error");
		
		$this->assertTrue(is_a($result->right, 'OrLogicOperator'), "type of right children error");
		$this->assertEquals(2, count($result->right->childrens), "number of or logic operator error");
		
		$this->assertTrue(is_a($result->right->childrens[0], 'AndLogicOperator'), "type of 1-st children of or logic error");
		$this->assertTrue(is_a($result->right->childrens[1], 'AndLogicOperator'), "type of 2-sd children of or logic error");
		$this->assertEquals(3, count($result->right->childrens[0]->childrens), "number of 1-st children or logic operator error");
		$this->assertEquals(3, count($result->right->childrens[1]->childrens), "number of 2-sd children or logic operator error");
		
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[0], 'NotLogicOperator'), "type of 1-st children of 1-st and logic error");
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[0]->children, 'Operand'), "type of 1-st children of 1-st and logic error");
		$this->assertEquals("a", $result->right->childrens[0]->childrens[0]->children->name, "name of 1-st children of 1-st and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[1], 'Operand'), "type of 2-sd children of 1-st and logic error");
		$this->assertEquals("a", $result->right->childrens[0]->childrens[1]->name, "name of 2-sd children of 1-st and logic error");
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[2], 'Operand'), "type of 3-rd children of 1-st and logic error");
		$this->assertEquals("a", $result->right->childrens[0]->childrens[2]->name, "name of 3-rd children of 1-st and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[0], 'Operand'), "type of 1-st children of 2-sd and logic error");
		$this->assertEquals("a", $result->right->childrens[1]->childrens[0]->name, "name of 1-st children of 2-sd and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[1], 'NotLogicOperator'), "type of 2-sd children of 2-sd and logic error");
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[1]->children, 'Operand'), "type of 2-sd children of 2-sd and logic error");
		$this->assertEquals("a", $result->right->childrens[1]->childrens[1]->children->name, "name of 2-sd children of 2-sd and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[2], 'NotLogicOperator'), "type of 3-rd children of 3-rd and logic error");
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[2]->children, 'Operand'), "type of 2-sd children of 3-rd and logic error");
		$this->assertEquals("a", $result->right->childrens[1]->childrens[2]->children->name, "name of 2-sd children of 3-rd and logic error");
		
	}
	
	public function test_defOperator() {
		global $typeOfVars;
		$expression = "*(a+i)";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'DereferenceOperator'), "type of root error");
		$this->assertTrue(is_a($result->children, 'PlusOperator'), "type of children error");
		
		$this->assertEquals(2, count($result->children->childrens), "number of plus operator error");
		
		$this->assertTrue(is_a($result->children->childrens[0], 'Operand'), "type of 1-st children error");
		$this->assertTrue(is_a($result->children->childrens[1], 'Operand'), "type of 2-nd children error");
		
		$this->asserEquals("a", $this->childrens->childrens[0]->name, "name of 1-st children error");
		$this->asserEquals("i", $this->childrens->childrens[1]->name, "name of 2-nd children error");
		
	}
	
	public function test_referOper() {
		global $typeOfVars;
		$expression = "&a";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'ReferenceOperator'), "type of root error");
		$this->assertTrue(is_a($result->children, 'Operand'), "type of children error");
		$this->assertEquals("a", $result->children->name, "name of children error");		
	}
	
	public function test_unaryMinus() {
		global $typeOfVars;
		$expression = "a + -b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'PlusOperator'), "type of root error");
		$this->assertEquals(2, count($result->childrens), "number of plus operator error");
		$this->assertEquals("a", $result->childrens[0]->name, "name of 1-st children error");
		$this->assertTrue(is_a($result->childrens[1], 'UnaryMinusOperator'), "type of 2-rd error");
		$this->assertEquals("b", $result->childrens[1]->children->name, "name of 1-st children error");

	}
	
	public function test_minus() {
		global $typeOfVars;
		$expression = "a - b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'MinusOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_modoperator() {
		global $typeOfVars;
		$expression = "a % b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'ModOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_equanOper() {
		global $typeOfVars;
		$expression = "a == b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'EqualOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_notEqualOper() {
		global $typeOfVars;
		$expression = "a != b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'NotEqualOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_greaterOper() {
		global $typeOfVars;
		$expression = "a > b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'GreaterOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_lessOper() {
		global $typeOfVars;
		$expression = "a < b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'LessOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_greaterEqualOp() {
		global $typeOfVars;
		$expression = "a >= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'GreaterEqualOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_lessEqualOp() {
		global $typeOfVars;
		$expression = "a <= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'LessEqualOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_plusAssOp() {
		global $typeOfVars;
		$expression = "a += b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'CompoAssignOperator'), "type of root error");
		$this->assertEquals("+=", $result->compoOperator, "type of compo assign error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_minusAssOp() {
		global $typeOfVars;
		$expression = "a -= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'CompoAssignOperator'), "type of root error");
		$this->assertEquals("-=", $result->compoOperator, "type of compo assign error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_multiAssOp() {
		global $typeOfVars;
		$expression = "a *= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'CompoAssignOperator'), "type of root error");
		$this->assertEquals("*=", $result->compoOperator, "type of compo assign error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_divAssOp() {
		global $typeOfVars;
		$expression = "a /= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'CompoAssignOperator'), "type of root error");
		$this->assertEquals("/=", $result->compoOperator, "type of compo assign error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shlAssOp() {
		global $typeOfVars;
		$expression = "a <<= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'CompoAssignOperator'), "type of root error");
		$this->assertEquals("<<=", $result->compoOperator, "type of compo assign error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shrAssOp() {
		global $typeOfVars;
		$expression = "a >>= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'CompoAssignOperator'), "type of root error");
		$this->assertEquals(">>=", $result->compoOperator, "type of compo assign error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shlOp() {
		global $typeOfVars;
		$expression = "a << b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'ShiftLeftOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shrOp() {
		global $typeOfVars;
		$expression = "a >> b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'ShiftRightOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_subscOp() {
		global $typeOfVars;
		$expression = "a[b]";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'SubscriptOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_ptMemOp() {
		global $typeOfVars;
		$expression = "a.b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'PtMemAccOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_memOp() {
		global $typeOfVars;
		$expression = "a->b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'MemAccOperator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_powFunc() {
		global $typeOfVars;
		$expression = "pow(a,b)";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'PowFunction'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'Operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'Operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public static function tearDownAfterClass() {
		
	}
}

?>