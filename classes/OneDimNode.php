
<?php

require_once 'BaseNode.php';

abstract class OneDimNode extends BaseNode {
	
	public $children;
	
	public function convertEachChildrens() {
		// convert children
		$this->children->convert($this);
		while ($this->children->pToNewChild !== null) {
			$tmp = $this->children;
			$this->children = $this->children->pToNewChild;
			$this->children->pToNewChild = null;
			$this->children->convert($this);
		}
	}
	
	public function calculateTreeInString() {
		$this->treeInString = $this->getLabel(get_class($this))." ".$this->children->treeInString;	
	}
	
}

/////////////////////////////////////////////////////////////////////

class UnaryMinusOperator extends OneDimNode {
	
	public function convert($parent) {
		
	}
}


class NotLogicOperator extends OneDimNode {
	
	public function convert($parent) {
		
	}
}


class DereferenceOperator extends OneDimNode {
	
	public function convert($parent) {
		
	}
}


class ReferenceOperator extends OneDimNode {
	
	public function convert($parent) {
		
	}
}

?>