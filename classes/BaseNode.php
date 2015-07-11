
<?php

interface VarType {
	const CONSTSHORT 		= 0;
	const CONSTINT 			= 1;
	const CONSTLONGINT 		= 2;
	
	const CONSTFLOAT 		= 3;
	const CONSTDOUBLE 		= 4;
	const CONSTLONGDOUBLE 	= 5;
	
	const SHORT 			= 6;
	const INT 				= 7;
	const LONGINT 			= 8;
	
	const FLOAT 			= 9;
	const DOUBLE 			= 10;
	const LONGDOUBLE 		= 11;
	
	const CHAR 				= 12;
	const ARRAYS 			= 13;
	const STRUCT			= 14;
	
	const BOOLVAR 			= 15;
	
	const ERRORVAR 			= 16;
}

abstract class BaseNode {
	
	public $pToNewChild = null;
	public $treeInString = null;
	
	abstract public function convert($parent);
	
	abstract public function convertEachChildrens();
	
}

?>