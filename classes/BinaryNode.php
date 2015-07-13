
<?php

require_once 'BaseNode.php';

abstract class BinaryNode extends BaseNode {
	
	public $left;				///< указатель на левый сын
	public $right;				///< указатель на правый сын
	
	public function convertEachChildrens() {
		// convert left children
		$this->left->convert($this);
		while ($this->left->pToNewChild !== null) {
			$tmp = $this->left->pToNewChild;
			$this->left = clone $this->left->pToNewChild;
			$tmp->deleteChildrens();
			$this->left->pToNewChild = null;
			$this->left->convert($this);
		}
		
		// convert right children
		$this->right->convert($this);
		while ($this->right->pToNewChild !== null) {
			$tmp = $this->right->pToNewChild;
			//var_dump($this);
			$this->right = clone $this->right->pToNewChild;
			//var_dump($this);
			$tmp->deleteChildrens();
			$this->right->pToNewChild = null;
			$this->right->convert($this);
		}
	}
	
	public function calculateTreeInString() {
		$this->treeInString = $this->getLabel(get_class($this))." ";
		$this->treeInString .= $this->left->treeInString." ";
		$this->treeInString .= $this->right->treeInString." ";	
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

/*!
* \class AssignOperator
*
* \brief Класс для операции присваивания
*
*/
class AssignOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	}
}

/*!
* \class DivOperator
*
* \brief Класс для операции деления
*
*/
class DivOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		//convertEachChildrens();
		// если делить на дробь: a / (b/c) => a*c/b
		while (get_class($this->right) == 'DivOperator') {
			$tmp = new MultiOperator();
			array_push($tmp->childrens, $this->left);
			array_push($tmp->childrens, $this->right->right);		
			$tmp->calculateTreeInString();
			
			$this->left = $tmp;
		}
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид a * (1/b)
		if (!(get_class($this->left) == 'Operand' &&
			$this->left->number == 1)) {
			$tmp = new MultiOperator();
			array_push($tmp->childrens, $this->left);
	
			$t2 = new DivOperator();
			$t2->right = $this->right;
			// константа 1
			$t2->left = new Operand("1", 1);
			$t2->calculateTreeInString();
			array_push($tmp->childrens, $t2);
			$tmp->calculateTreeInString();
			$this->pToNewChild = $tmp;
		}
	}
}

/*!
* \class ModOperator
*
* \brief Класс для операции вычисления остатка
*
*/
class ModOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	}
}

/*!
* \class PowFunction
*
* \brief Класс для функции pow()
*
*/
class PowFunction extends BinaryNode {
	
	public function convert($parent) {
		// преобразуем каждый сын
		$this->convertEachChildrens();
	
		if (get_class($this->right) == 'Operand')
			if (is_int($this->right->number)) {
				$newNode = new MultiOperator();
				// присваивать значения нового узла
				for ($i = 0; $i < $this->right->number; $i++)
					array_push($newNode->childrens, clone $this->left);
				$newNode->calculateTreeInString();
				$this->pToNewChild = $newNode;
			}
	}
}

/*!
* \class MinusOperator
*
* \brief Класс для операции вычитания
*
*/
class MinusOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид сложения
		$tmp = new PlusOperator();
		array_push($tmp->childrens, $this->left);
		$t = new UnaryMinusOperator();
		$t->children = $this->right;
		$t->calculateTreeInString();
		array_push($tmp->childrens, $t);
		$tmp->calculateTreeInString();
		$this->pToNewChild = $tmp;
	}
}

/*!
* \class NotEqualOperator
*
* \brief Класс для операции сравнения неравенства
*
*/
class NotEqualOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		// сортировать правый и левый
		if ($this->left->treeInString > $this->right->treeInString) {
			$tmp = $this->left;
			$this->left = $this->right;
			$this->right = $tmp;
		}
		// преобразовать в вид не равно
		$tmp = new NotLogicOperator();
		$t = new EqualOperator();
		$t->right = $this->right;
		$t->left = $this->left;
		$t->calculateTreeInString();
		$tmp->children = $t;
		$tmp->calculateTreeInString();
		$this->pToNewChild = $tmp;
	}
}

/*!
* \class EqualOperator
*
* \brief Класс для операции сравнения равенства
*
*/
class EqualOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// сортировать левый и правый сыны
		if ($this->left->treeInString > $this->right->treeInString) {
			$tmp = $this->left;
			$this->left = $this->right;
			$this->right = $tmp;
		}
	
		$tmp = new PlusOperator();
		// преобразовать в виде разность == 0
		$t = new UnaryMinusOperator();
		$t->children = $this->right;
		$t->calculateTreeInString();
		array_push($tmp->childrens, $this->left);
		array_push($tmp->childrens, $t);
		$tmp->calculateTreeInString();
		$this->left = $tmp;
		$this->right = new Operand("0", 0);
	
		// преобразовать левый сын
		$this->left->pToNewChild = null;
		$this->left->convert($this);
		while ($this->left->pToNewChild !== null) {
			$this->left = $this->left->pToNewChild; 
			$this->left->pToNewChild = null;
			$this->left->convert($this);
		}
		$this->left->calculateTreeInString();
	}
}

/*!
* \class MemAccOperator
*
* \brief Класс для операции обращения к члену структуры
*
*/
class MemAccOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// проверка сына
		if (get_class($this->left) == 'DereferenceOperator') {
			$tmp = new PtMemAccOperator();
			$tmp->left = $this->left->children;
			$tmp->right = $this->right;
			$tmp->calculateTreeInString();
			$this->pToNewChild = $tmp;
		}
	}
}

/*!
* \class PtMemAccOperator
*
* \brief Класс для операции обращения к члену структуры (через указатель)
*
*/
class PtMemAccOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	}
}

/*!
* \class SubscriptOperator
*
* \brief Класс для операции обращения к элементу массива
*
*/
class SubscriptOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид через указатель
		$tmp = new DereferenceOperator();
		$t = new PlusOperator();
		array_push($t->childrens, $this->right);
		array_push($t->childrens, $this->left);
		$t->calculateTreeInString();
		$tmp->children = $t;
		$tmp->calculateTreeInString();
		$this->pToNewChild = $tmp;
	}
}

/*!
* \class ShiftRightOperator
*
* \brief Класс для операции побитового сдвига право
*
*/
class ShiftRightOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// если целая константа то преобразовать в деление
		if (get_class($this->right) == 'Operand' &&
			$this->right->number !== null) {
			$tmp = new DivOperator();
			$tmp->left = $this->left;
			$this->right->number = pow(2, $this->right->number);
			$this->right->name = strval($this->right->number);
			$tmp->right = $this->right;
			$tmp->calculateTreeInString();
			$this->pToNewChild = $tmp;
		}
	}
}

/*!
* \class ShiftLeftOperator
*
* \brief Класс для операции побитового сдвига влево
*
*/
class ShiftLeftOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// если целая константа то преобразовать в вид умножение
		if (get_class($this->right) === 'Operand' &&
			$this->right->number !== null) {
			$tmp = new MultiOperator();
			array_push($tmp->childrens, $this->left);
			$this->right->number = pow(2, $this->right->number);
			$this->right->name = strval($this->right->number);
			$this->right->treeInString = $this->right->name;
			array_push($tmp->childrens, $this->right);
			$tmp->calculateTreeInString();
			$this->pToNewChild = $tmp;
		}
	}
}

/*!
* \class GreaterEqualOperator
*
* \brief Класс для операции сравнения больше или равно
*
*/
class GreaterEqualOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// перенести влево
		$tmp = new PlusOperator();
		
		$t = new UnaryMinusOperator();
		$t->children = $this->left;
		$t->calculateTreeInString();
	
		array_push($tmp->childrens, $this->right);
		array_push($tmp->childrens, $t);
		
		// преобразовать в вид больше
		$gt = new GreaterOperator();
	
		$gt->right = new Operand("0", 0);
		$gt->left = $tmp;
		$gt->calculateTreeInString();
		
		// преобразовать в вид ! >
		$ngt = new NotLogicOperator();
		$ngt->children = $gt;
		$this->pToNewChild = $ngt;
	}
}

/*!
* \class GreaterOperator
*
* \brief Класс для операции сравнения больше
*
*/
class GreaterOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// перенести операнд влево
		$tmp = new PlusOperator();
		
		$t = new UnaryMinusOperator();
		$t->children = $this->right;
		$t->calculateTreeInString();
		array_push($tmp->childrens, $this->left);
		array_push($tmp->childrens, $t);
	
		$this->left = $tmp;
		$this->right = new Operand("0", 0);
		//var_dump($this->left);
		// преобразовать новый сын
		$this->left->pToNewChild = null;
		$this->left->convert($this);
		while ($this->left->pToNewChild !== null) {
			$this->left = $this->left->pToNewChild;
			$this->left->pToNewChild = null;
			$this->left->convert($this);
		}
		$this->left->calculateTreeInString();
	}
}

/*!
* \class LessEqualOperator
*
* \brief Класс для операции сравнения меньше или равно
*
*/
class LessEqualOperator extends BinaryNode {
	
	public function convert($parent) {
		// преобразовать каждый сын
		$this->convertEachChildrens();
		// преобразовать в вид >=
		$tmp = new GreaterEqualOperator();
		$tmp->left = $this->right;
		$tmp->right = $this->left;
		$tmp->calculateTreeInString();
		$this->pToNewChild = $tmp;
	}
}

/*!
* \class LessOperator
*
* \brief Класс для операции сравнения меньше
*
*/
class LessOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	
		// преобразовать в вид >
		$gt = new GreaterOperator();
	
		$gt->right = $this->left;
		$gt->left = $this->right;
		$gt->calculateTreeInString();
		$this->pToNewChild = $gt;
	}
}

/*!
* \class PlusAssignOperator
*
* \brief Класс для операции сложение, совмещённое с присваиванием
*
*/
class PlusAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new PlusOperator();
		array_push($tmp->childrens, clone $this->left);
		array_push($tmp->childrens, $this->right);
		$tmp->calculateTreeInString();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculateTreeInString();
		
		$this->pToNewChild = $newass;
	}
}

/*!
* \class MinusAssignOperator
*
* \brief Класс для операции вычитание, совмещённое с присваиванием
*
*/
class MinusAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new MinusOperator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculateTreeInString();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculateTreeInString();
		
		$this->pToNewChild = $newass;
	}
}

/*!
* \class DivAssignOperator
*
* \brief Класс для операции деление, совмещённое с присваиванием
*
*/
class DivAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new DivOperator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculateTreeInString();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculateTreeInString();
		
		$this->pToNewChild = $newass;
	}
}

/*!
* \class MultiAssignOperator
*
* \brief Класс для операции умножение, совмещённое с присваиванием
*
*/
class MultiAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new MultiOperator();
		array_push($tmp->childrens, clone $this->left);
		array_push($tmp->childrens, $this->right);
		$tmp->calculateTreeInString();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculateTreeInString();
		
		$this->pToNewChild = $newass;
	}
}

/*!
* \class ShrAssignOperator
*
* \brief Класс для операции побитовый сдвиг вправо, совмещённый с присваиванием
*
*/
class ShrAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new ShiftRightOperator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculateTreeInString();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculateTreeInString();
		
		$this->pToNewChild = $newass;
	}
}

/*!
* \class ShlAssignOperator
*
* \brief Класс для операции побитовый сдвиг влево, совмещённый с присваиванием
*
*/
class ShlAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		
		$tmp = new ShiftLeftOperator();
		$tmp->left = clone $this->left;
		$tmp->right = $this->right;
		$tmp->calculateTreeInString();
		
		$newass = new AssignOperator();
		$newass->left = $this->left;
		$newass->right = $tmp;
		$newass->calculateTreeInString();
		
		$this->pToNewChild = $newass;
	}
}

?>