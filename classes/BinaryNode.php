
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
	
}

?>