
<?php

abstract class BaseNode {
	
	public $pToNewChild;
	public $treeInString;
	
	abstract public function convert($parent);
	
	abstract public function convertEachChildrens();
	
}

?>