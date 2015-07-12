
<?php

require_once 'BaseNode.php';

abstract class BinaryNode extends BaseNode {
	
	public $left;
	public $right;
	
	public function convertEachChildrens() {
		// convert left children
		$this->left->convert($this);
		while ($this->left->pToNewChild !== null) {
			$tmp = $this->left;
			$this->left = $this->left->pToNewChild;
			$this->left->pToNewChild = null;
			$this->left->convert($this);
		}
		
		// convert right children
		$this->right->convert($this);
		while ($this->right->pToNewChild !== null) {
			$tmp = $this->right;
			$this->right = $this->right->pToNewChild;
			$this->right->pToNewChild = null;
			$this->right->convert($this);
		}
	}
	
	public function calculateTreeInString() {
		$this->treeInString = $this->getLabel(get_class($this))." ";
		$this->treeInString .= $this->left->treeInString." ";
		$this->treeInString .= $this->right->treeInString." ";	
	}
	
}

////////////////////////////////////////////////////////

class AssignOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class DivOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class ModOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class PowFunction extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class MinusOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class NotEqualOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class EqualOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class MemAccOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class PtMemAccOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class SubscriptOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class ShiftRightOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class ShiftLeftOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class GreaterEqualOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class GreaterOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class LessEqualOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class LessOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class PlusAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class MinusAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class DivAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class MultiAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class ShrAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}

class ShlAssignOperator extends BinaryNode {
	
	public function convert($parent) {
		
	}
}
?>