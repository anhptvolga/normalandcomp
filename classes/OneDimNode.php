
<?php

require_once 'BaseNode.php';

/*!
 * \class OneDimNode
 *
 * \brief Базовый класс для унарных операций
 * 
 */
abstract class OneDimNode extends BaseNode {
	
	public $children;				///< указатель на сын
	
	public function convertEachChildrens() {
		// convert children
		$this->children->convert($this);
		while ($this->children->pToNewChild !== null) {
			$tmp = $this->children->pToNewChild;
			$this->children = clone $this->children->pToNewChild;
			$tmp->deleteChildrens();
			$this->children->pToNewChild = null;
			$this->children->convert($this);
		}
	}
	
	public function calculateTreeInString() {
		$this->treeInString = $this->getLabel(get_class($this))." ".$this->children->treeInString;	
	}
	
	public function deleteChildrens()	{
		$this->children->deleteChildrens();
		unset ($this->children);
		$this->children = null;
	}
	
	public function __clone() {
		$this->children = clone $this->children; 	
	}
}

/////////////////////////////////////////////////////////////////////

/*!
* \class UnaryMinusOperator
*
* \brief Класс для операции унарного минуса
*
*/
class UnaryMinusOperator extends OneDimNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		//---
		if (get_class($this->children) == 'UnaryMinusOperator') {
			$this->pToNewChild = $this->children->children;
			return;
		}
		//--
		if (get_class($this->children) == 'Operand' &&
				$this->children->number !== null) {
			$this->children->number = - $this->children->number;
			$this->children->name = strval($this->children->number);
			$this->children->treeInString = $this->children->name;
			$this->pToNewChild = $this->children;
			return;
		}
		//--
		if (get_class($this->children) == 'PlusOperator') {
			for ($i = 0; $i < count($this->children->childrens); $i++) {
				$t = new UnaryMinusOperator();
				$t->children = $this->children->childrens[$i];
				$this->children->childrens[$i] = $t;
				$t->calculateTreeInString();
			}
			$this->children->calculateTreeInString();
			$this->pToNewChild = $this->children;
			return;
		}
	}
}

/*!
* \class NotLogicOperator
*
* \brief Класс для операции логического отрицания НЕ
*
*/
class NotLogicOperator extends OneDimNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();

		if (get_class($this->children) == 'NotLogicOperator') {
			$this->pToNewChild = $this->children->children;
		}
	}
}

/*!
* \class DereferenceOperator
*
* \brief Класс для операции непрямого обращения (через указатель)
*
*/
class DereferenceOperator extends OneDimNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
		// проверка сына
		if (get_class($this->children) == 'ReferenceOperator') {
			$this->pToNewChild = $this->children->children;
		}
	}
}

/*!
* \class ReferenceOperator
*
* \brief Класс для операции обращения к адресу
*
*/
class ReferenceOperator extends OneDimNode {
	
	public function convert($parent) {
		$this->convertEachChildrens();
	}
}

?>