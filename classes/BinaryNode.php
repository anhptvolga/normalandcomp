<?php

require_once 'BaseNode.php';

abstract class qtype_correctwriting_binary_node extends qtype_correctwriting_base_node {
	
	public $left;				///< указатель на левый сын
	public $right;				///< указатель на правый сын
	
	public function convertEachChildrens() {
		// convert left children
		$this->left->convert($this);
		while ($this->left->ptonewchild !== null) {
			$tmp = $this->left->ptonewchild;
			$this->left = clone $this->left->ptonewchild;
			$tmp->deleteChildrens();
			$this->left->ptonewchild = null;
			$this->left->convert($this);
		}
		
		// convert right children
		$this->right->convert($this);
		while ($this->right->ptonewchild !== null) {
			$tmp = $this->right->ptonewchild;
			//var_dump($this);
			$this->right = clone $this->right->ptonewchild;
			//var_dump($this);
			$tmp->deleteChildrens();
			$this->right->ptonewchild = null;
			$this->right->convert($this);
		}
	}
	
	public function calculatetreeinstring() {
		$this->treeinstring = $this->getLabel(get_class($this))." ";
		$this->treeinstring .= $this->left->treeinstring." ";
		$this->treeinstring .= $this->right->treeinstring." ";	
	}
	
	public function deleteChildrens()	{
		$this->left->deleteChildrens();
		$this->right->deleteChildrens();
		unset ($this->left);
		unset ($this->right);
		$this->left = null;
		$this->right = null;
	}
	
	public function __clone() {
		$this->left = clone $this->left;
		$this->right = clone $this->right;
	}
}

////////////////////////////////////////////////////////

/**
* \class AssignOperator
*
* \brief Класс для операции присваивания
*
*/
class qtype_correctwriting_assign_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	}
}

/**
* \class qtype_correctwriting_div_operator
*
* \brief Класс для операции деления
*
*/
class qtype_correctwriting_div_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		//convertEachChildrens();
		// если делить на дробь: a / (b/c) => a*c/b
		while (get_class($this->right) == 'qtype_correctwriting_div_operator') {
			$tmp = new qtype_correctwriting_multi_operator();
			array_push($tmp->childrens, $this->left);
			array_push($tmp->childrens, $this->right->right);		
			$tmp->calculatetreeinstring();
			
			$this->left = $tmp;
		}
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид a * (1/b)
		if (!(get_class($this->left) == 'qtype_correctwriting_operand' &&
			$this->left->number == 1)) {
			$tmp = new qtype_correctwriting_multi_operator();
			array_push($tmp->childrens, $this->left);
	
			$t2 = new qtype_correctwriting_div_operator();
			$t2->right = $this->right;
			// константа 1
			$t2->left = new qtype_correctwriting_operand("1", 1);
			$t2->calculatetreeinstring();
			array_push($tmp->childrens, $t2);
			$tmp->calculatetreeinstring();
			$this->ptonewchild = $tmp;
		}
	}
}

/**
* \class qtype_correctwriting_div_operator
*
* \brief Класс для операции вычисления остатка
*
*/
class qtype_correctwriting_mod_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	}
}

/**
* \class qtype_correctwriting_pow_function
*
* \brief Класс для функции pow()
*
*/
class qtype_correctwriting_pow_function extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразуем каждый сын
		$this->convertEachChildrens();
	
		if (get_class($this->right) == 'qtype_correctwriting_operand')
			if (is_int($this->right->number)) {
				$newNode = new qtype_correctwriting_multi_operator();
				// присваивать значения нового узла
				for ($i = 0; $i < $this->right->number; $i++)
					array_push($newNode->childrens, clone $this->left);
				$newNode->calculatetreeinstring();
				$this->ptonewchild = $newNode;
			}
	}
}

/**
* \class qtype_correctwriting_minus_operator
*
* \brief Класс для операции вычитания
*
*/
class qtype_correctwriting_minus_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид сложения
		$tmp = new qtype_correctwriting_plus_operator();
		array_push($tmp->childrens, $this->left);
		$t = new qtype_correctwriting_unary_minus_operator();
		$t->children = $this->right;
		$t->calculatetreeinstring();
		array_push($tmp->childrens, $t);
		$tmp->calculatetreeinstring();
		$this->ptonewchild = $tmp;
	}
}

/**
* \class qtype_correctwriting_not_equal_operator
*
* \brief Класс для операции сравнения неравенства
*
*/
class qtype_correctwriting_not_equal_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		// сортировать правый и левый
		if ($this->left->treeinstring > $this->right->treeinstring) {
			$tmp = $this->left;
			$this->left = $this->right;
			$this->right = $tmp;
		}
		// преобразовать в вид не равно
		$tmp = new qtype_correctwriting_not_logic_operator();
		$t = new qtype_correctwriting_equal_operator();
		$t->right = $this->right;
		$t->left = $this->left;
		$t->calculatetreeinstring();
		$tmp->children = $t;
		$tmp->calculatetreeinstring();
		$this->ptonewchild = $tmp;
	}
}

/**
* \class qtype_correctwriting_equal_operator
*
* \brief Класс для операции сравнения равенства
*
*/
class qtype_correctwriting_equal_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// сортировать левый и правый сыны
		if ($this->left->treeinstring > $this->right->treeinstring) {
			$tmp = $this->left;
			$this->left = $this->right;
			$this->right = $tmp;
		}
	
		$tmp = new qtype_correctwriting_plus_operator();
		// преобразовать в виде разность == 0
		$t = new qtype_correctwriting_unary_minus_operator();
		$t->children = $this->right;
		$t->calculatetreeinstring();
		array_push($tmp->childrens, $this->left);
		array_push($tmp->childrens, $t);
		$tmp->calculatetreeinstring();
		$this->left = $tmp;
		$this->right = new qtype_correctwriting_operand("0", 0);
	
		// преобразовать левый сын
		$this->left->ptonewchild = null;
		$this->left->convert($this);
		while ($this->left->ptonewchild !== null) {
			$this->left = $this->left->ptonewchild; 
			$this->left->ptonewchild = null;
			$this->left->convert($this);
		}
		$this->left->calculatetreeinstring();
	}
}

/**
* \class qtype_correctwriting_mem_acc_operator
*
* \brief Класс для операции обращения к члену структуры
*
*/
class qtype_correctwriting_mem_acc_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// проверка сына
		if (get_class($this->left) == 'qtype_correctwriting_dereference_operator') {
			$tmp = new qtype_correctwriting_pt_mem_acc_operator();
			$tmp->left = $this->left->children;
			$tmp->right = $this->right;
			$tmp->calculatetreeinstring();
			$this->ptonewchild = $tmp;
		}
	}
}

/**
* \class qtype_correctwriting_pt_mem_acc_operator
*
* \brief Класс для операции обращения к члену структуры (через указатель)
*
*/
class qtype_correctwriting_pt_mem_acc_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	}
}

/**
* \class qtype_correctwriting_subscript_operator
*
* \brief Класс для операции обращения к элементу массива
*
*/
class qtype_correctwriting_subscript_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид через указатель
		$tmp = new qtype_correctwriting_dereference_operator();
		$t = new qtype_correctwriting_plus_operator();
		array_push($t->childrens, $this->right);
		array_push($t->childrens, $this->left);
		$t->calculatetreeinstring();
		$tmp->children = $t;
		$tmp->calculatetreeinstring();
		$this->ptonewchild = $tmp;
	}
}

/**
* \class qtype_correctwriting_shift_right_operator
*
* \brief Класс для операции побитового сдвига право
*
*/
class qtype_correctwriting_shift_right_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// если целая константа то преобразовать в деление
		if (get_class($this->right) == 'qtype_correctwriting_operand' &&
			$this->right->number !== null) {
			$tmp = new qtype_correctwriting_div_operator();
			$tmp->left = $this->left;
			$this->right->number = pow(2, $this->right->number);
			$this->right->name = strval($this->right->number);
			$tmp->right = $this->right;
			$tmp->calculatetreeinstring();
			$this->ptonewchild = $tmp;
		}
	}
}

/**
* \class qtype_correctwriting_shift_left_operator
*
* \brief Класс для операции побитового сдвига влево
*
*/
class qtype_correctwriting_shift_left_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// если целая константа то преобразовать в вид умножение
		if (get_class($this->right) === 'qtype_correctwriting_operand' &&
			$this->right->number !== null) {
			$tmp = new qtype_correctwriting_multi_operator();
			array_push($tmp->childrens, $this->left);
			$this->right->number = pow(2, $this->right->number);
			$this->right->name = strval($this->right->number);
			$this->right->treeinstring = $this->right->name;
			array_push($tmp->childrens, $this->right);
			$tmp->calculatetreeinstring();
			$this->ptonewchild = $tmp;
		}
	}
}

/**
* \class qtype_correctwriting_greater_equal_operator
*
* \brief Класс для операции сравнения больше или равно
*
*/
class qtype_correctwriting_greater_equal_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// перенести влево
		$tmp = new qtype_correctwriting_plus_operator();
		
		$t = new qtype_correctwriting_unary_minus_operator();
		$t->children = $this->left;
		$t->calculatetreeinstring();
	
		array_push($tmp->childrens, $this->right);
		array_push($tmp->childrens, $t);
		
		// преобразовать в вид больше
		$gt = new qtype_correctwriting_greater_operator();
	
		$gt->right = new qtype_correctwriting_operand("0", 0);
		$gt->left = $tmp;
		$gt->calculatetreeinstring();
		
		// преобразовать в вид ! >
		$ngt = new qtype_correctwriting_not_logic_operator();
		$ngt->children = $gt;
		$this->ptonewchild = $ngt;
	}
}

/**
* \class qtype_correctwriting_greater_operator
*
* \brief Класс для операции сравнения больше
*
*/
class qtype_correctwriting_greater_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// перенести операнд влево
		$tmp = new qtype_correctwriting_plus_operator();
		
		$t = new qtype_correctwriting_unary_minus_operator();
		$t->children = $this->right;
		$t->calculatetreeinstring();
		array_push($tmp->childrens, $this->left);
		array_push($tmp->childrens, $t);
	
		$this->left = $tmp;
		$this->right = new qtype_correctwriting_operand("0", 0);
		//var_dump($this->left);
		// преобразовать новый сын
		$this->left->ptonewchild = null;
		$this->left->convert($this);
		while ($this->left->ptonewchild !== null) {
			$this->left = $this->left->ptonewchild;
			$this->left->ptonewchild = null;
			$this->left->convert($this);
		}
		$this->left->calculatetreeinstring();
	}
}

/**
* \class qtype_correctwriting_less_equal_operator
*
* \brief Класс для операции сравнения меньше или равно
*
*/
class qtype_correctwriting_less_equal_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид >=
		$tmp = new qtype_correctwriting_greater_equal_operator();
		$tmp->left = $this->right;
		$tmp->right = $this->left;
		$tmp->calculatetreeinstring();
		$this->ptonewchild = $tmp;
	}
}

/**
* \class qtype_correctwriting_less_operator
*
* \brief Класс для операции сравнения меньше
*
*/
class qtype_correctwriting_less_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	
		// преобразовать в вид >
		$gt = new qtype_correctwriting_greater_operator();
	
		$gt->right = $this->left;
		$gt->left = $this->right;
		$gt->calculatetreeinstring();
		$this->ptonewchild = $gt;
	}
}

/**
* \class qtype_correctwriting_plus_assign_operator
*
* \brief Класс для операции сложение, совмещённое с присваиванием
*
*/
class qtype_correctwriting_plus_assign_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new qtype_correctwriting_plus_operator();
		array_push($tmp->childrens, clone $this->left);
		array_push($tmp->childrens, $this->right);
		$tmp->calculatetreeinstring();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculatetreeinstring();
		
		$this->ptonewchild = $newass;
	}
}

/**
* \class qtype_correctwriting_minus_assign_operator
*
* \brief Класс для операции вычитание, совмещённое с присваиванием
*
*/
class qtype_correctwriting_minus_assign_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new qtype_correctwriting_minus_operator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculatetreeinstring();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculatetreeinstring();
		
		$this->ptonewchild = $newass;
	}
}

/**
* \class qtype_correctwriting_div_assign_operator
*
* \brief Класс для операции деление, совмещённое с присваиванием
*
*/
class qtype_correctwriting_div_assign_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new qtype_correctwriting_div_operator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculatetreeinstring();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculatetreeinstring();
		
		$this->ptonewchild = $newass;
	}
}

/**
* \class qtype_correctwriting_multi_assign_operator
*
* \brief Класс для операции умножение, совмещённое с присваиванием
*
*/
class qtype_correctwriting_multi_assign_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new qtype_correctwriting_multi_operator();
		array_push($tmp->childrens, clone $this->left);
		array_push($tmp->childrens, $this->right);
		$tmp->calculatetreeinstring();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculatetreeinstring();
		
		$this->ptonewchild = $newass;
	}
}

/**
* \class qtype_correctwriting_shr_assign_operator
*
* \brief Класс для операции побитовый сдвиг вправо, совмещённый с присваиванием
*
*/
class qtype_correctwriting_shr_assign_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new qtype_correctwriting_shift_right_operator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculatetreeinstring();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculatetreeinstring();
		
		$this->ptonewchild = $newass;
	}
}

/**
* \class qtype_correctwriting_shl_assign_operator
*
* \brief Класс для операции побитовый сдвиг влево, совмещённый с присваиванием
*
*/
class qtype_correctwriting_shl_assign_operator extends qtype_correctwriting_binary_node {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new qtype_correctwriting_shift_left_operator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculatetreeinstring();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculatetreeinstring();
		
		$this->ptonewchild = $newass;
	}
}

?>