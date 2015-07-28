<?php
global $CFG;
define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$CFG->libdir = $CFG->dirroot . '/lib'; 

require_once($CFG->dirroot .'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');
require_once($CFG->dirroot .'/question/type/correctwriting/normalandcomp/classes/OneDimNode.php');
require_once($CFG->dirroot .'/question/type/correctwriting/normalandcomp/classes/KDimNode.php');
require_once($CFG->dirroot .'/question/type/correctwriting/normalandcomp/classes/BinaryNode.php');
require_once($CFG->dirroot .'/question/type/correctwriting/normalandcomp/classes/Operand.php');

/**
 * Печать дерево в формат DOT
 *
 * \param [in] file указатель на выходный файл
 * \param [in] curNode указатель на текущий узел
 * 
 */
function print_tree_to_dot($file, $curNode) {
	static $globalid = 0;			// следующий идентификатор для узлов
	$id = $globalid;				// идентификатор для данный узла
	// инкремент общий индетификатор
	++$globalid;
	// печать определение для данного узла
	if (is_a($curNode, 'qtype_correctwriting_operand')){
		fwrite($file, $id.' [label = '.$curNode->name);
	}
	else {
		fwrite($file, $id.' [label = "'.$curNode->get_label(get_class($curNode)).'"');
	}
	fwrite($file,"]\n");
	// переход на сыновья
	if (is_subclass_of($curNode, 'qtype_correctwriting_one_dim_node')) {			// унарный узел
		$next = $globalid;
		print_tree_to_dot($file, $curNode->children);
		fwrite($file, $id.' -> '.$next."\n");
	}
	elseif (is_subclass_of($curNode, 'qtype_correctwriting_k_dim_node')) {			// двоичной узел
		foreach ($curNode->childrens as $value) {
			$next = $globalid;
			print_tree_to_dot($file, $value);
			fwrite($file, $id.' -> '.$next."\n");
		}
	}
	elseif (is_subclass_of($curNode, 'qtype_correctwriting_binary_node')) {		// k-dim узел
		$next = $globalid;
		print_tree_to_dot($file, $curNode->left);
		fwrite($file, $id.' -> '.$next."\n");
		$next = $globalid;
		print_tree_to_dot($file, $curNode->right);
		fwrite($file, $id.' -> '.$next."\n");
	}
}


/**
 * \brief Функция сравнения двух деревьев
 *
 * \param [in] tree1 указатель на узел первого дерева
 * \param [in] tree2 указатель на узел второго дерева
 * \param [in] isPrintDiff флаг: печать ли разницы двух деревьев
 * \return true если два дерева равны, в противном случае false
 * 
 */
function is_tree_equal($tree1, $tree2, $isPrintDiff = FALSE) {
	
	if (get_class($tree1) != get_class($tree2)) {
		// печать разницы при тестировать	
		if($isPrintDiff){
			echo "____ type of node diff\n";
			echo "__________ result = ".get_class($tree1)."\n";
			echo "__________ expected = ".get_class($tree2)."\n";
		}
		return false;
	}
	if (is_a($tree1, 'qtype_correctwriting_operand')){							// операнд
		if ($tree1->name != $tree2->name){
			// печать разницы при тестировать
			if ($isPrintDiff){
				echo "____ name of operand diff\n";
				echo "__________ result = ".$tree1->name."\n";
				echo "__________ expected = ".$tree2->name."\n";
			}
			return false;
		}
		if ($tree1->number != $tree2->number){
			// печать разницы при тестировать
			if ($isPrintDiff){
				echo "____ number of operand diff\n";
				echo "__________ result = ".$tree1->number."\n";
				echo "__________ expected = ".$tree2->number."\n";
			}
			return false;
		}
	}
	elseif (is_subclass_of($tree1, 'qtype_correctwriting_one_dim_node')) {				// унарный
		return is_tree_equal($tree1->children, $tree2->children, $isPrintDiff);
	}
	elseif (is_subclass_of($tree1, 'qtype_correctwriting_binary_node')) {				// бинарный
		return is_tree_equal($tree1->left, $tree2->left, $isPrintDiff) &&
				is_tree_equal($tree1->right, $tree2->right, $isPrintDiff);
	}
	elseif (is_subclass_of($tree1, 'qtype_correctwriting_k_dim_node')) {				// k-dim
		$res = count($tree1->childrens) == count($tree2->childrens);
		$i = 0;
		while ($res && $i < count($tree1->childrens)) {
			$res = $res && is_tree_equal($tree1->childrens[$i], $tree2->childrens[$i], $isPrintDiff);
			$i ++;
		}
		return $res;
	}
	
	return TRUE;
}

function readExp($filename) {
	//$filename = '..\testinput\expession.txt';
	$file = fopen($filename, "r");
	if ($file !== FALSE) {
		$res = fgets($file);
		fclose($file);
		$res = rtrim($res);
	}
	else {
		throw new Exception("file not found");
	}
	if (strlen($res) == 0) {
		throw new Exception("no expession in file");
	}
	return $res;
}

/**
 * Функция для создания экземплю узла по именю класса
 */
function getInstane($classname) {
	switch ($classname) {
		case 'identifier':
			return new qtype_correctwriting_operand();
		case 'expr_logical_or':
			return new qtype_correctwriting_or_logic_operator();
		case 'expr_logical_and':
			return new qtype_correctwriting_and_logic_operator();
		case 'expr_plus':
			return new qtype_correctwriting_plus_operator();
		case 'expr_multiply':
			return new qtype_correctwriting_multi_operator();
			
		case 'expr_logical_not':
			return new qtype_correctwriting_not_logic_operator();
		case 'expr_unary_minus':
			return new qtype_correctwriting_unary_minus_operator();
		case 'expr_dereference':
			return new qtype_correctwriting_dereference_operator();
		case 'expr_take_adress':
			return new qtype_correctwriting_reference_operator();
			
		case 'expr_function_call':
			return new qtype_correctwriting_pow_function();
		case 'expr_assign':
			return new qtype_correctwriting_assign_operator();
		case 'expr_minus':
			return new qtype_correctwriting_minus_operator();
		case 'expr_modulosign':
			return new qtype_correctwriting_mod_operator();
		case 'expr_division':
			return new qtype_correctwriting_div_operator();
		case 'expr_equal':
			return new qtype_correctwriting_equal_operator();
		case 'expr_notequal':
			return new qtype_correctwriting_not_equal_operator();
		case 'try_value_access':
			return new qtype_correctwriting_mem_acc_operator();
		case 'try_pointer_access':
			return new qtype_correctwriting_pt_mem_acc_operator();		
		case 'expr_array_access':
			return new qtype_correctwriting_subscript_operator();
		case 'expr_rightshift':
			return new qtype_correctwriting_shift_right_operator();
		case 'expr_leftshift':
			return new qtype_correctwriting_shift_left_operator();
		case 'expr_lesser_or_equal':
			return new qtype_correctwriting_less_equal_operator();
		case 'expr_greater_or_equal':
			return new qtype_correctwriting_greater_equal_operator();
		case 'expr_lesser':
			return new qtype_correctwriting_less_operator();
		case 'expr_greater':
			return new qtype_correctwriting_greater_operator();
		case 'expr_plus_assign':
			return new qtype_correctwriting_plus_assign_operator();
		case 'expr_minus_assign':
			return new qtype_correctwriting_minus_assign_operator();
		case 'expr_multiply_assign':
			return new qtype_correctwriting_multi_assign_operator();
		case 'expr_division_assign':
			return new qtype_correctwriting_div_assign_operator();
		case 'expr_leftshift_assign':
			return new qtype_correctwriting_shl_assign_operator();
		case 'expr_rightshift_assign':
			return new qtype_correctwriting_shr_assign_operator();
	}
	return null;
}

/**
 * Функция для создания дерева от лексического дерева
 */
function filter_node($node)  {
	$curNode = null;						// указатель на текущий узел
	$nodetype = $node->type();				// тип узла
	
	$leftchild = null;						// указатель на левый сын
	$rightchild = null;						// указатель на правый сын

	if ($nodetype === 'identifier' || $nodetype === 'numeric') { 						// операнд
		$curNode = new qtype_correctwriting_operand();
		$curNode->name = $node->value();
		$curNode->treeinstring = $curNode->name;
		if ($nodetype === 'numeric') {
			$tmp = $curNode->name->string();
			$curNode->number = ($tmp == intval($tmp)) ? intval($tmp) : doubleval($tmp);
		}
		
	}
	elseif ($nodetype === 'expr_function_call') {			// pow() функция
		if ($node->childs[0]->value() != 'pow') {
			$fname = $node->childs[0]->value();
			throw new Exception("funtion $fname not suported");
		}
		$leftchild = filter_node($node->childs[2]->childs[0]);
		$rightchild = filter_node($node->childs[2]->childs[2]);
		$curNode = new qtype_correctwriting_pow_function();
		$curNode->left = $leftchild;
		$curNode->right = $rightchild;
		$curNode->treeinstring = "pow() ".$leftchild->treeinstring." ".$rightchild->treeinstring;
	}
	elseif ($nodetype === 'expr_property_access'){				// операции . и ->
		$leftchild = filter_node($node->childs[0]->childs[0]);
		$rightchild = filter_node($node->childs[1]);
		$curNode = getInstane($node->childs[0]->type());
		$curNode->left = $leftchild;
		$curNode->right = $rightchild;
		$curNode->treeinstring = $node->childs[0]->childs[1]->value()." ".$leftchild->treeinstring." ".$rightchild->treeinstring;
	}
	elseif ($nodetype === 'expr_brackets') {					// скобки
		return filter_node($node->childs[1]);
	}
	elseif (in_array($nodetype, array('expr_logical_or', 'expr_logical_and', 'expr_plus', 'expr_multiply'))) {
		// k-dim узел
		$leftchild = filter_node($node->childs[0]);
		$rightchild = filter_node($node->childs[2]);
		$curNode = getInstane($nodetype);
		array_push($curNode->childrens, $leftchild);
		array_push($curNode->childrens, $rightchild);
		$curNode->go_up_childrens();
		$curNode->treeinstring = $node->childs[1]->value();
		
		foreach ($curNode->childrens as $value) {
			$curNode->treeinstring .= " ".$value->treeinstring;
		}
	}
	elseif (in_array($nodetype, array('expr_logical_not', 'expr_unary_minus', 'expr_dereference', 'expr_take_adress'))) { 
		// one-dim узел
		$curNode = getInstane($nodetype);
		$curNode->children = filter_node($node->childs[1]);
		$curNode->treeinstring = $node->childs[0]->value()." ".$curNode->children->treeinstring;
	}
	elseif (in_array($nodetype, array('expr_assign', 'expr_minus', 'expr_modulosign', 'expr_division',
								'expr_notequal', 'expr_equal', 'expr_array_access', 'expr_rightshift',
								'expr_leftshift', 'expr_lesser_or_equal', 'expr_greater_or_equal',
								'expr_lesser', 'expr_greater', 'expr_plus_assign', 'expr_minus_assign',
								'expr_multiply_assign', 'expr_division_assign', 'expr_leftshift_assign',
								'expr_rightshift_assign'))) {
		// двойчный узел
		$leftchild = filter_node($node->childs[0]);
		$rightchild = filter_node($node->childs[2]);
		$curNode = getInstane($nodetype);
		$curNode->left = $leftchild;
		$curNode->right = $rightchild;
		$curNode->treeinstring = $node->childs[1]->value()." ".$leftchild->treeinstring." ".$rightchild->treeinstring;
	}
	else {
		// ошибка
		throw new Exception("not supported operator ".$nodetype." ".$node->value());
	}
	
	return $curNode;
}


function build_tree($expression) {

	// создать лексическое дерево
	$lang = new block_formal_langs_language_cpp_parseable_language();
	if (isset($donotstripcomments)) {
		$lang->parser()->set_strip_comments(false);
	}

	$result = $lang->create_from_string($expression);
	
	if (count($result->syntaxtree) > 1 || $result->syntaxtree[0]->type()==='operators') {
		throw new Exception("Expression invalid");
	}
	
	// создать дерево свое
	$root = filter_node($result->syntaxtree[0]);
	
	return $root;
	
}

?>