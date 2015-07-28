<?php

require_once 'BaseNode.php';

/**
 * \class qtype_correctwriting_one_dim_node
 *
 * \brief Базовый класс для унарных операций
 * 
 */
abstract class qtype_correctwriting_one_dim_node extends qtype_correctwriting_base_node {
	
	public $children;				///< указатель на сын
	
	public function convert_each_childrens() {
		// convert children
		$this->children->convert($this);
		while ($this->children->ptonewchild !== null) {
			$tmp = $this->children->ptonewchild;
			$this->children = clone $this->children->ptonewchild;
			$tmp->delete_childrens();
			$this->children->ptonewchild = null;
			$this->children->convert($this);
		}
	}
	
	public function calculate_tree_in_string() {
		$this->treeinstring = $this->get_label(get_class($this))." ".$this->children->treeinstring;	
	}
	
	public function delete_childrens()	{
		$this->children->delete_childrens();
		unset ($this->children);
		$this->children = null;
	}
	
	public function __clone() {
		$this->children = clone $this->children; 	
	}
}

/////////////////////////////////////////////////////////////////////

/**
* \class qtype_correctwriting_unary_minus_operator
*
* \brief Класс для операции унарного минуса
*
*/
class qtype_correctwriting_unary_minus_operator extends qtype_correctwriting_one_dim_node {
	
	public function convert($parent) {
		$this->convert_each_childrens();
		//---
		if (get_class($this->children) == 'qtype_correctwriting_unary_minus_operator') {
			$this->ptonewchild = $this->children->children;
			return;
		}
		//--
		if (get_class($this->children) == 'qtype_correctwriting_operand' &&
				$this->children->number !== null) {
			$this->children->number = - $this->children->number;
			$this->children->name = strval($this->children->number);
			$this->children->treeinstring = $this->children->name;
			$this->ptonewchild = $this->children;
			return;
		}
		//--
		if (get_class($this->children) == 'qtype_correctwriting_plus_operator') {
			for ($i = 0; $i < count($this->children->childrens); $i++) {
				$t = new qtype_correctwriting_unary_minus_operator();
				$t->children = $this->children->childrens[$i];
				$this->children->childrens[$i] = $t;
				$t->calculate_tree_in_string();
			}
			$this->children->calculate_tree_in_string();
			$this->ptonewchild = $this->children;
			return;
		}
	}
}

/**
* \class qtype_correctwriting_not_logic_operator
*
* \brief Класс для операции логического отрицания НЕ
*
*/
class qtype_correctwriting_not_logic_operator extends qtype_correctwriting_one_dim_node {
	
	public function convert($parent) {
		$this->convert_each_childrens();

		if (get_class($this->children) == 'qtype_correctwriting_not_logic_operator') {
			$this->ptonewchild = $this->children->children;
		}
	}
}

/**
* \class qtype_correctwriting_dereference_operator
*
* \brief Класс для операции непрямого обращения (через указатель)
*
*/
class qtype_correctwriting_dereference_operator extends qtype_correctwriting_one_dim_node {
	
	public function convert($parent) {
		$this->convert_each_childrens();
		// проверка сына
		if (get_class($this->children) == 'qtype_correctwriting_reference_operator') {
			$this->ptonewchild = $this->children->children;
		}
	}
}

/**
* \class qtype_correctwriting_reference_operator
*
* \brief Класс для операции обращения к адресу
*
*/
class qtype_correctwriting_reference_operator extends qtype_correctwriting_one_dim_node {
	
	public function convert($parent) {
		$this->convert_each_childrens();
	}
}

?>