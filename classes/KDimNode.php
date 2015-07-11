
<?php

require_once 'BaseNode.php';

abstract class KDimNode extends BaseNode {
	
	public $childrens = array();
	
	public function sortChildrens() {
		
		for ($i = 0; $i < count($this->childrens); $i++) {
			for ($j = $i + 1; $j < count($this->childrens); $j++){
				if ($this->childrens[$i]->treeInString > $this->childrens[$j]->treeInString) {
					$tmp = $this->childrens[$i];
					$this->childrens[$i] = $this->childrens[$j];				
					$this->childrens[$j] = $tmp;
				}
			}
		}
		
	}
	
	public function goUpChildrens() {
		
		for ($i = 0; $i < count($this->childrens); $i ++) {
			if (get_class($this) == get_class($this->childrens[$i])) {
				
				for ($j = count($this->childrens[$i]->childrens)-1; $j >=0; $j --) {
					array_push($this->childrens, $this->childrens[$i]->childrens[$j]);
				}
				array_splice($this->childrens, $i, 1);
				$i--;
			}
		}		
		
	}
	
	public function convertEachChildrens() {
		
		foreach ($this->childrens as &$children) {	
			// convert each children
			$children->convert($this);
			while ($children->pToNewChild !== null) {
				$tmp = $this->children;
				$children = $children->pToNewChild;
				$children->pToNewChild = null;
				$children->convert($this);
			}	
		}
		
	}
	
}

/////////////////////////////////////////////////////////////////

class PlusOperator extends KDimNode {
	
	public function convert($parent) {

	}
	
}

class MultiOperator extends KDimNode {
	
	public function convert($parent) {
		
	}
	
	public function calculateConst(&$isHavePlus, &$isAdd) {
		
	}
	
	public function countNegative(&$value) {
		
	}
	
	public function duplicateChild($value) {
		
	}
	
	public function convertDivInMult()	{
		$divop = array();				// временный массив
		// взять дроби
		for ($i = 0; $i < count($this->childrens); $i ++) {
			if (get_class($this->childrens[$i]) == 'DivOperator'){
				array_push($divop, $this->childrens[$i]);
				array_splice($this->childrens, $i, 1);
				$i --;
			}
		} 
		// создать новый 
		if (count($divop) > 1) {
			$tmp = new MultiOperator();
			foreach ($divop as $i => $value) {
				array_push($tmp->childrens, $value->right);
			}
			$newch = new DivOperator();
			$newch->left = new Operand("1", 1, VarType::INT);
			$newch->right = $tmp;
			
			array_push($divop, $newch);
		}
		// возвращать в списку сыновей
		foreach ($divop as $key => $value) {
			array_push($this->childrens, $value);
		}
	}
	
	public function openBracket(){
		$multElements = array();		// список умножителей
		$conf = array();				// перестановка
		// Инициализировать список умножителей.
		for ($i = 0; $i < count($this->childrens); $i++)
		{
			$conf[$i] = 0;
			if (get_class($this->childrens[$i]) == 'PlusOperator'){
				array_push($multElements, $this->childrens[$i]->childrens);
			}
			else {
				array_push($multElements, array($this->childrens[$i]));
			}
		}
		//////////////////////////////////////////////////////////////////////////
		$res = new PlusOperator();				// новая корень
		$isStop = FALSE;						// флаг стопа
		while (!$isStop)	{
			
			// создать узел умножения
			$cur = new MultiOperator();
			for ($i = 0; $i < count($multElements); $i++){
				array_push($cur->childrens, $multElements[$i][$conf[$i]]);
			}
			array_push($res->childrens, $cur);
			
			// Вычисление следующей перестановки
			$prev = 1;
			for ($i = count($multElements) - 1; $i >= 0; $i--){
				$conf[$i] = (1 + $conf[$i]) % count($multElements[$i]);
				if ($i == 0 && $conf[$i] == 0)
					$isStop = true;
				if ($conf[$i] != 0)
					break;
			}
			
		}
		
		return $res;
	}
	
}

class AndLogicOperator extends KDimNode {
	
	public function convert($parent) {
		
	}
	
}


class OrLogicOperator extends KDimNode {
	
	public function convert($parent) {
		
	}
	
	public function isChildsSame($one, $two) {
		
	}
	
	public function reduceCompare()	{
		
	}
	
	public function isChangeable($one, $two)	{
		
	}
	
	public function isCover($imp, $old)	{
		
	}
	
	public function isHaveNode($node, $vec)	{
		
	}
	
	public function posInFullExp($node, $vec)	{
		
	}
	
	public function makeFullExp(&$fullExp)	{
		
	}
	
	public function makeEachChildrenNotation($fullExp, &$eachChild)	{
		
	}
	
	public function makeImplicate(&$eachChild) {
		
	}
	
	public function findMinCover($eachChild, $impl, $coverage)	{
		
	}
	
	public function makeMinTree($eachChild, $mincf, $fullExp)	{
		
	}
	
	public function convertQuineMcCluskey()	{
		
	}
}


?>