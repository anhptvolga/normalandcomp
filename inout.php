<?php


global $CFG;

define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(__FILE__)));
$CFG->libdir = $CFG->dirroot . '/lib';

require_once($CFG->dirroot .'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');
require_once($CFG->dirroot .'/blocks/normalandcomp/classes/OneDimNode.php');
require_once($CFG->dirroot .'/blocks/normalandcomp/classes/KDimNode.php');
require_once($CFG->dirroot .'/blocks/normalandcomp/classes/BinaryNode.php');
require_once($CFG->dirroot .'/blocks/normalandcomp/classes/Operand.php');

$typeOfVars = array();

function printTreeToDOT($file, $curNode) {
	static $globalid = 0;
	$id = $globalid;
	++$globalid;
	if (is_a($curNode, 'Operand')){
		fwrite($file, $id.' [label = '.$curNode->name);
	}
	else {
		fwrite($file, $id.' [label = "'.$curNode->getLabel(get_class($curNode)).'"');
	}
	fwrite($file,"]\n");
	if (is_subclass_of($curNode, 'OneDimNode')) {
		$next = $globalid;
		printTreeToDOT($file, $curNode->children);
		fwrite($file, $id.' -> '.$next."\n");
	}
	elseif (is_subclass_of($curNode, 'KDimNode')) {
		foreach ($curNode->childrens as $value) {
			$next = $globalid;
			printTreeToDOT($file, $value);
			fwrite($file, $id.' -> '.$next."\n");
		}
	}
	elseif (is_subclass_of($curNode, 'BinaryNode')) {
		$next = $globalid;
		printTreeToDOT($file, $curNode->left);
		fwrite($file, $id.' -> '.$next."\n");
		$next = $globalid;
		printTreeToDOT($file, $curNode->right);
		fwrite($file, $id.' -> '.$next."\n");
	}
}


function isTreeEqual($tree1, $tree2, $isPrintDiff = FALSE) {
	
	if (get_class($tree1) != get_class($tree2)) {
		if($isPrintDiff){
			echo "____ type of node diff\n";
			echo "__________ result = ".get_class($tree1)."\n";
			echo "__________ expected = ".get_class($tree2)."\n";
		}
		return false;
	}
	if (is_a($tree1, 'Operand')){
		if ($tree1->name != $tree2->name){
			if ($isPrintDiff){
				echo "____ name of operand diff\n";
				echo "__________ result = ".$tree1->name."\n";
				echo "__________ expected = ".$tree2->name."\n";
			}
			return false;
		}
		if ($tree1->number != $tree2->number){
			if ($isPrintDiff){
				echo "____ number of operand diff\n";
				echo "__________ result = ".$tree1->number."\n";
				echo "__________ expected = ".$tree2->number."\n";
			}
			return false;
		}
	}
	elseif (is_subclass_of($tree1, 'OneDimNode')) {
		return isTreeEqual($tree1->children, $tree2->children, $isPrintDiff);
	}
	elseif (is_subclass_of($tree1, 'BinaryNode')) {
		return isTreeEqual($tree1->left, $tree2->left, $isPrintDiff) &&
				isTreeEqual($tree1->right, $tree2->right, $isPrintDiff);
	}
	elseif (is_subclass_of($tree1, 'KDimNode')) {
		$res = count($tree1->childrens) == count($tree2->childrens);
		$i = 0;
		while ($res && $i < count($tree1->childrens)) {
			$res = $res && isTreeEqual($tree1->childrens[$i], $tree2->childrens[$i], $isPrintDiff);
			$i ++;
		}
		return $res;
	}
	
	return TRUE;
}

function readExp($filename, &$exp1, &$exp2) {
	$filename = '..\testinput\expession.txt';
	$file = fopen($filename, "r");
	if ($file !== FALSE) {
		$exp1 = fgets($file);
		$exp2 = fgets($file);
		fclose($file);
	}
	else {
		throw new Exception("file input not found");
	}
	
	if (strlen($exp1) == 0) {
		throw new Exception("No expession in file");
	}
	if (strlen($exp2) == 0) {
		throw new Exception("Only one expression in file");
		
	}
}

/**
 * Функция для создания экземплю узла по именю класса
*/
function getInstane($classname) {
	switch ($classname) {
		case 'identifier':
			return new Operand();
		case 'expr_logical_or':
			return new OrLogicOperator();
		case 'expr_logical_and':
			return new AndLogicOperator();
		case 'expr_plus':
			return new PlusOperator();
		case 'expr_multiply':
			return new MultiOperator();
			
		case 'expr_logical_not':
			return new NotLogicOperator();
		case 'expr_unary_minus':
			return new UnaryMinusOperator();
		case 'expr_dereference':
			return new DereferenceOperator();
		case 'expr_take_adress':
			return new ReferenceOperator();
			
		case 'expr_function_call':
			return new PowFunction();
		case 'expr_assign':
			return new AssignOperator();
		case 'expr_minus':
			return new MinusOperator();
		case 'expr_modulosign':
			return new ModOperator();
		case 'expr_division':
			return new DivOperator();
		case 'expr_equal':
			return new EqualOperator();
		case 'expr_notequal':
			return new NotEqualOperator();
		case 'try_value_access':
			return new MemAccOperator();
		case 'try_pointer_access':
			return new PtMemAccOperator();		
		case 'expr_array_access':
			return new SubscriptOperator();
		case 'expr_rightshift':
			return new ShiftRightOperator();
		case 'expr_leftshift':
			return new ShiftLeftOperator();
		case 'expr_lesser_or_equal':
			return new LessEqualOperator();
		case 'expr_greater_or_equal':
			return new GreaterEqualOperator();
		case 'expr_lesser':
			return new LessOperator();
		case 'expr_greater':
			return new GreaterOperator();
		case 'expr_plus_assign':
			return new PlusAssignOperator();
		case 'expr_minus_assign':
			return new MinusAssignOperator();
		case 'expr_multiply_assign':
			return new MultiAssignOperator();
		case 'expr_division_assign':
			return new DivAssignOperator();
		case 'expr_leftshift_assign':
			return new ShlAssignOperator();
		case 'expr_rightshift_assign':
			return new ShrAssignOperator();
	}
	return null;
}

/**
	 * Функция для создания дерева от лексического дерева
	 */
function filter_node($node)
{
	$curNode = null;						// указатель на текущий узел
	$nodetype = $node->type();				// тип узла
	
	$leftchild = null;						// указатель на левый сын
	$rightchild = null;						// указатель на правый сын

	if ($nodetype === 'identifier' || $nodetype === 'numeric') { 						// операнд
		$curNode = new Operand();
		$curNode->name = $node->value();
		$curNode->treeInString = $curNode->name;
		if ($nodetype === 'numeric') {
			$tmp = $curNode->name->string();
			$curNode->number = ($tmp == intval($tmp)) ? intval($tmp) : doubleval($tmp);
		}
		
	}
	elseif ($nodetype === 'expr_function_call') {			// pow() функция
		if ($node->childs[0]->value() != 'pow') {
			$fname = $node->childs[0]->value();
			throw new Exception("Funtion $fname not suported");
		}
		$leftchild = filter_node($node->childs[2]->childs[0]);
		$rightchild = filter_node($node->childs[2]->childs[2]);
		$curNode = new PowFunction();
		$curNode->left = $leftchild;
		$curNode->right = $rightchild;
		$curNode->treeInString = "pow() ".$leftchild->treeInString." ".$rightchild->treeInString;
	}
	elseif ($nodetype === 'expr_property_access'){				// операции . и ->
		$leftchild = filter_node($node->childs[0]->childs[0]);
		$rightchild = filter_node($node->childs[1]);
		$curNode = getInstane($node->childs[0]->type());
		$curNode->left = $leftchild;
		$curNode->right = $rightchild;
		$curNode->treeInString = $node->childs[0]->childs[1]->value()." ".$leftchild->treeInString." ".$rightchild->treeInString;
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
		$curNode->goUpChildrens();
		$curNode->treeInString = $node->childs[1]->value();
		
		foreach ($curNode->childrens as $value) {
			$curNode->treeInString .= " ".$value->treeInString;
		}
	}
	elseif (in_array($nodetype, array('expr_logical_not', 'expr_unary_minus', 'expr_dereference', 'expr_take_adress'))) { 
		// one-dim узел
		$curNode = getInstane($nodetype);
		$curNode->children = filter_node($node->childs[1]);
		$curNode->treeInString = $node->childs[0]->value()." ".$curNode->children->treeInString;
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
		$curNode->treeInString = $node->childs[1]->value()." ".$leftchild->treeInString." ".$rightchild->treeInString;
	}
	else {
		// ошибка
		throw new Exception("Not supported operator ".$nodetype." ".$node->value());
	}
	
	return $curNode;
}


function buildTree($expression, $typeOfVars) {

	$res = null;
	// создать лексическое дерево
	$lang = new block_formal_langs_language_cpp_parseable_language();
	if (isset($donotstripcomments)) {
		$lang->parser()->set_strip_comments(false);
	}
	$result = $lang->create_from_string($expression);
	//var_dump($result->syntaxtree);
	
	if (count($result->syntaxtree) > 1 || $result->syntaxtree[0]->type()==='operators') {
		throw new Exception("Expression invalid");
	}
	
	// создать дерево свое
	$root = filter_node($result->syntaxtree[0]);
	
	//echo "----------\n";
	/*
	var_dump($root);
	$file = fopen("tree.gv", "w");
	fwrite($file,'digraph {');
	printTreeToDOT($file, $root);
	fwrite($file,'}');
	fclose($file);
	*/ 
	
	return $root;
	
}
	
function readTypeVar($file) {
	
	ini_set('error_reporting', 30711);
	
	$file = '..\testinput\test.xml';
	
	function startElement($parser, $name, $attrs) {
		global $typeOfVars;
		if ($name === "CONST") {
			if (count($attrs) != 2) {
				throw new Exception("Not enough attributes of counst in line ".xml_get_current_line_number($parser));
			}
			$typeOfVars[$attrs["VALUE"]] = $attrs["TYPE"]; 			
		}
		elseif ($name === "ARRAY" || $name === "VAR") {
			if (count($attrs) != 2) {
				throw new Exception("Not enough attributes of array in line ".xml_get_current_line_number($parser));
			}
			$typeOfVars[$attrs["NAME"]] = $attrs["TYPE"];
		}
		elseif ($name !== "EXPRESSION") {
			$line = xml_get_current_line_number($parser);
			throw new Exception("Unexpected element xlm in line ".$line." : ".$name);
		}
			   
	}
	
	function endElement($parser, $name) {
	}
	
	
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	if ( !file_exists($file) ) {
        throw new Exception("Xml-file not found");
    }
	
	$fp = fopen($file, "r");
	
	while ($data = fread($fp, 4096)) {
	    if (!xml_parse($xml_parser, $data, feof($fp))) {
	        throw new Exception("Error parsing xml in line ".xml_get_current_line_number($xml_parser));
	    }
	}
	
	xml_parser_free($xml_parser);
		
}




?>