
<?php

require_once 'BaseNode.php';

abstract class KDimNode extends BaseNode {
	
	public $childrens = array();
	
	public function sortChildrens() {
		
		for ($i = 0; $i < count($this->childrens); $i++) {
			for ($j = $i + 1; $j < count($this->childrens); $j++){
				if ($this->childrens[$i]->treeInString > $this->childrens[$j]->treeInString) {
					$tmp = $this->childrens[$i]->treeInString;
					$this->childrens[$i]->treeInString = $this->childrens[$j]->treeInString;;					
					$this->childrens[$j]->treeInString = $tmp;
				}
			}
		}
		
	}
	
	public function goUpChildrens() {
		
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