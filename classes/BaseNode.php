
<?php

/*!
 * \class BaseNode
 *
 * \brief Базовый класс для дерева
 * 
 */
abstract class BaseNode {
	
	public $pToNewChild = null;					///< указатель на новый сын если текущий сын заменился
	public $treeInString = null;				///< дерево в вид строки для сортировки
	
	/*!
	 * Функция преобразования узла
	 * \param [in] parent - указатель на родитель
	 */
	abstract public function convert($parent);
	
	/*!
	* Функция преобразования каждого сына текущего узла
	*/
	abstract public function convertEachChildrens();
	
	/*!
	* Функция вычисления дерево в вид строки
	*/
	abstract public function calculateTreeInString();
	
	/*!
	* Функция удаления всех сыновей текущего узла
	*/
	abstract public function deleteChildrens();
	
	/*!
	 * Взять тип класс в виде нормальной оператор
	 */
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