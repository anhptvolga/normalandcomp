
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
	
	abstract public function calculateTreeInString();
	
	public function getLabel($nodetype)	{
		switch ($nodetype) {
			case 'PlusOperator':
				return '+';
			case 'MultiOperator':
				return '*';
			case 'AndLogicOperator':
				return '&&';
			case 'OrLogicOperator':
				return '||';
			case 'NotLogicOperator':
				return "!";
			case 'UnaryMinusOperator':
				return '-';
			case 'DereferenceOperator':
				return '*';
			case 'ReferenceOperator':
				return '&';
				
			case 'PowFunction':
				return 'pow()';
			case 'AssignOperator':
				return '=';
			case 'MinusOperator':
				return '-';
			case 'ModOperator':
				return '%';
			case 'DivOperator':
				return '/';
			case 'EqualOperator':
				return '==';
			case 'NotEqualOperator':
				return '!=';
			case 'MemAccOperator':
				return '.';
			case 'PtMemAccOperator':
				return '->';		
			case 'SubscriptOperator':
				return '[]';
			case 'ShiftRightOperator':
				return '>>';
			case 'ShiftLeftOperator':
				return '<<';
			case 'LessEqualOperator':
				return '<=';
			case 'GreaterEqualOperator':
				return '>=';
			case 'LessOperator':
				return '<';
			case 'GreaterOperator':
				return '>';
			case 'PlusAssignOperator':
				return '+=';
			case 'MinusAssignOperator':
				return '-=';
			case 'MultiAssignOperator':
				return '*=';
			case 'DivAssignOperator':
				return '/=';
			case 'ShlAssignOperator':
				return '<<=';
			case 'ShrAssignOperator':
				return '>>=';
		}
	}
	
}

?>