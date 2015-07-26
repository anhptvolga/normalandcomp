

<?php

require_once '..\inout.php';

$typeOfVars = array();

class buildTreeTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
	}
	
	public function test_oneOperand() {
		global $typeOfVars;
		$expression = "a";
		$result = buildTree($expression, $typeOfVars);

		$this->assertTrue(is_a($result, 'qtype_correctwriting_operand'), "type of root not correct");
		$this->assertEquals("a",$result->name, "name of operand not correct");
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Expression invalid
	 */
	public function test_oneOperator() {
		global $typeOfVars;
		$expression = "*";
		$result = buildTree($expression, $typeOfVars);
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Expression invalid
	 */
	public function test_notEnoughOperand() {
		global $typeOfVars;
		$expression = "a +";
		$result = buildTree($expression, $typeOfVars);
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Expression invalid
	 */
	public function test_notEnoughOperator() {
		global $typeOfVars;
		$expression = "a + b c";
		$result = buildTree($expression, $typeOfVars);
	}
	
	public function test_onlyUnary() {
		global $typeOfVars;
		$expression = "!a";
		$result = buildTree($expression, $typeOfVars);
		$this->assertTrue(is_a($result, 'qtype_correctwriting_not_logic_operator'), "type of root error");
		$this->assertTrue(is_a($result->children, 'qtype_correctwriting_operand'), "type of children error");
		$this->assertEquals("a", $result->children->name, "name of operand error");
	}
	
	public function test_onlyBinary() {
		global $typeOfVars;
		$expression = "c = a / b";
		$result = buildTree($expression, $typeOfVars);
		$this->assertTrue(is_a($result, 'qtype_correctwriting_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("c", $result->left->name, "name of left operand error");
		
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_div_operator'), "type of right children error");
		
		$this->assertTrue(is_a($result->right->left, 'qtype_correctwriting_operand'), "type of left children div operator error");
		$this->assertEquals("a", $result->right->left->name, "name of left operand of div opertor error");
		
		$this->assertTrue(is_a($result->right->right, 'qtype_correctwriting_operand'), "type of right children div operator error");
		$this->assertEquals("b", $result->right->right->name, "name of right operand of div opertor error");
	}
	
	public function test_onlyKdim() {
		global $typeOfVars;
		$expression = "a * b * c";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_multi_operator'), "type of root error");
		$this->assertEquals(3, count($result->childrens), "number of childrens error");
		
		$this->assertTrue(is_a($result->childrens[0], 'qtype_correctwriting_operand'), "type of 1-st children error");
		$this->assertTrue(is_a($result->childrens[1], 'qtype_correctwriting_operand'), "type of 2-sd children error");
		$this->assertTrue(is_a($result->childrens[2], 'qtype_correctwriting_operand'), "type of 3-rd children error");
		
		$this->assertEquals("c", $result->childrens[0]->name, "name of 1-st children error");
		$this->assertEquals("b", $result->childrens[1]->name, "name of 2-sd children error");
		$this->assertEquals("a", $result->childrens[2]->name, "name of 3-rd children error");
		
	}
	
	public function test_mixedType() {
		global $typeOfVars;
		$expression = "a = !a && b && c || a && !b && !c";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left operand error");
		
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_or_logic_operator'), "type of right children error");
		$this->assertEquals(2, count($result->right->childrens), "number of or logic operator error");
		
		$this->assertTrue(is_a($result->right->childrens[0], 'qtype_correctwriting_and_logic_operator'), "type of 1-st children of or logic error");
		$this->assertTrue(is_a($result->right->childrens[1], 'qtype_correctwriting_and_logic_operator'), "type of 2-sd children of or logic error");
		$this->assertEquals(3, count($result->right->childrens[0]->childrens), "number of 1-st children or logic operator error");
		$this->assertEquals(3, count($result->right->childrens[1]->childrens), "number of 2-sd children or logic operator error");
		
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[2], 'qtype_correctwriting_not_logic_operator'), "type of 1-st children of 1-st and logic error");
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[2]->children, 'qtype_correctwriting_operand'), "type of 1-st children of 1-st and logic error");
		$this->assertEquals("a", $result->right->childrens[0]->childrens[2]->children->name, "name of 1-st children of 1-st and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[1], 'qtype_correctwriting_operand'), "type of 2-sd children of 1-st and logic error");
		$this->assertEquals("b", $result->right->childrens[0]->childrens[1]->name, "name of 2-sd children of 1-st and logic error");
		$this->assertTrue(is_a($result->right->childrens[0]->childrens[0], 'qtype_correctwriting_operand'), "type of 3-rd children of 1-st and logic error");
		$this->assertEquals("c", $result->right->childrens[0]->childrens[0]->name, "name of 3-rd children of 1-st and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[2], 'qtype_correctwriting_operand'), "type of 1-st children of 2-sd and logic error");
		$this->assertEquals("a", $result->right->childrens[1]->childrens[2]->name, "name of 1-st children of 2-sd and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[1], 'qtype_correctwriting_not_logic_operator'), "type of 2-sd children of 2-sd and logic error");
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[1]->children, 'qtype_correctwriting_operand'), "type of 2-sd children of 2-sd and logic error");
		$this->assertEquals("b", $result->right->childrens[1]->childrens[1]->children->name, "name of 2-sd children of 2-sd and logic error");
		
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[0], 'qtype_correctwriting_not_logic_operator'), "type of 3-rd children of 3-rd and logic error");
		$this->assertTrue(is_a($result->right->childrens[1]->childrens[0]->children, 'qtype_correctwriting_operand'), "type of 2-sd children of 3-rd and logic error");
		$this->assertEquals("c", $result->right->childrens[1]->childrens[0]->children->name, "name of 2-sd children of 3-rd and logic error");
		
	}
	
	public function test_defOperator() {
		global $typeOfVars;
		$expression = "*(a+i)";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_dereference_operator'), "type of root error");
		$this->assertTrue(is_a($result->children, 'qtype_correctwriting_plus_operator'), "type of children error");
		
		$this->assertEquals(2, count($result->children->childrens), "number of plus operator error");
		
		$this->assertTrue(is_a($result->children->childrens[0], 'qtype_correctwriting_operand'), "type of 1-st children error");
		$this->assertTrue(is_a($result->children->childrens[1], 'qtype_correctwriting_operand'), "type of 2-nd children error");
		
		$this->assertEquals("a", $result->children->childrens[0]->name, "name of 1-st children error");
		$this->assertEquals("i", $result->children->childrens[1]->name, "name of 2-nd children error");
		
	}
	
	public function test_referOper() {
		global $typeOfVars;
		$expression = "&a";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_reference_operator'), "type of root error");
		$this->assertTrue(is_a($result->children, 'qtype_correctwriting_operand'), "type of children error");
		$this->assertEquals("a", $result->children->name, "name of children error");		
	}
	
	public function test_unaryMinus() {
		global $typeOfVars;
		$expression = "a + -b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_plus_operator'), "type of root error");
		$this->assertEquals(2, count($result->childrens), "number of plus operator error");
		$this->assertEquals("a", $result->childrens[0]->name, "name of 1-st children error");
		$this->assertTrue(is_a($result->childrens[1], 'qtype_correctwriting_unary_minus_operator'), "type of 2-rd error");
		$this->assertEquals("b", $result->childrens[1]->children->name, "name of 1-st children error");

	}
	
	public function test_minus() {
		global $typeOfVars;
		$expression = "a - b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_minus_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_modoperator() {
		global $typeOfVars;
		$expression = "a % b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_mod_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_equanOper() {
		global $typeOfVars;
		$expression = "a == b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_equal_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_notEqualOper() {
		global $typeOfVars;
		$expression = "a != b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_not_equal_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_greaterOper() {
		global $typeOfVars;
		$expression = "a > b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_greater_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_lessOper() {
		global $typeOfVars;
		$expression = "a < b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_less_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_greaterEqualOp() {
		global $typeOfVars;
		$expression = "a >= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_greater_equal_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_lessEqualOp() {
		global $typeOfVars;
		$expression = "a <= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_less_equal_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_plusAssOp() {
		global $typeOfVars;
		$expression = "a += b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_plus_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_minusAssOp() {
		global $typeOfVars;
		$expression = "a -= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_minus_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_multiAssOp() {
		global $typeOfVars;
		$expression = "a *= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_multi_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_divAssOp() {
		global $typeOfVars;
		$expression = "a /= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_div_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shlAssOp() {
		global $typeOfVars;
		$expression = "a <<= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_shl_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shrAssOp() {
		global $typeOfVars;
		$expression = "a >>= b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_shr_assign_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shlOp() {
		global $typeOfVars;
		$expression = "a << b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_shift_left_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_shrOp() {
		global $typeOfVars;
		$expression = "a >> b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_shift_right_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_subscOp() {
		global $typeOfVars;
		$expression = "a[b]";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_subscript_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_ptMemOp() {
		global $typeOfVars;
		$expression = "a.b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_mem_acc_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
		
	}
	
	public function test_memOp() {
		global $typeOfVars;
		$expression = "a->b";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_pt_mem_acc_operator'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public function test_powFunc() {
		global $typeOfVars;
		$expression = "pow(a,b)";
		$result = buildTree($expression, $typeOfVars);
		
		$this->assertTrue(is_a($result, 'qtype_correctwriting_pow_function'), "type of root error");
		
		$this->assertTrue(is_a($result->left, 'qtype_correctwriting_operand'), "type of left children error");
		$this->assertEquals("a", $result->left->name, "name of left children error");
		$this->assertTrue(is_a($result->right, 'qtype_correctwriting_operand'), "type of right children error");
		$this->assertEquals("b", $result->right->name, "name of right children error");
	}
	
	public static function tearDownAfterClass() {
		
	}
}

?>