<?php

/**
 * \class BaseNode
 *
 * \brief Базовый класс для дерева
 * 
 */
abstract class qtype_correctwriting_base_node {
	
	public $ptonewchild = null;					///< указатель на новый сын если текущий сын заменился
	public $treeinstring = null;				///< дерево в вид строки для сортировки
	
	/**
	 * Функция преобразования узла
	 * \param [in] parent - указатель на родитель
	 */
	abstract public function convert($parent);
	
	/**
	* Функция преобразования каждого сына текущего узла
	*/
	abstract public function convertEachChildrens();
	
	/**
	* Функция вычисления дерево в вид строки
	*/
	abstract public function calculatetreeinstring();
	
	/**
	* Функция удаления всех сыновей текущего узла
	*/
	abstract public function deleteChildrens();
	
	/**
	 * Взять тип класс в виде нормальной оператор
	 */
	public function getLabel($nodetype)	{
		switch ($nodetype) {
			case 'qtype_correctwriting_plus_operator':
				return '+';
			case 'qtype_correctwriting_multi_operator':
				return '*';
			case 'qtype_correctwriting_and_logic_operator':
				return '&&';
			case 'qtype_correctwriting_or_logic_operator':
				return '||';
			case 'qtype_correctwriting_not_logic_operator':
				return "!";
			case 'qtype_correctwriting_unary_minus_operator':
				return '-';
			case 'qtype_correctwriting_dereference_operator':
				return '*';
			case 'qtype_correctwriting_reference_operator':
				return '&';
				
			case 'qtype_correctwriting_pow_function':
				return 'pow()';
			case 'qtype_correctwriting_assign_operator':
				return '=';
			case 'qtype_correctwriting_minus_operator':
				return '-';
			case 'qtype_correctwriting_div_operator':
				return '%';
			case 'qtype_correctwriting_div_operator':
				return '/';
			case 'qtype_correctwriting_equal_operator':
				return '==';
			case 'qtype_correctwriting_not_equal_operator':
				return '!=';
			case 'qtype_correctwriting_mem_acc_operator':
				return '.';
			case 'qtype_correctwriting_pt_mem_acc_operator':
				return '->';		
			case 'qtype_correctwriting_subscript_operator':
				return '[]';
			case 'qtype_correctwriting_shift_right_operator':
				return '>>';
			case 'qtype_correctwriting_shift_left_operator':
				return '<<';
			case 'qtype_correctwriting_less_equal_operator':
				return '<=';
			case 'qtype_correctwriting_greater_equal_operator':
				return '>=';
			case 'qtype_correctwriting_less_operator':
				return '<';
			case 'qtype_correctwriting_greater_operator':
				return '>';
			case 'qtype_correctwriting_plus_assign_operator':
				return '+=';
			case 'qtype_correctwriting_minus_assign_operator':
				return '-=';
			case 'qtype_correctwriting_multi_assign_operator':
				return '*=';
			case 'qtype_correctwriting_div_assign_operator':
				return '/=';
			case 'qtype_correctwriting_shl_assign_operator':
				return '<<=';
			case 'qtype_correctwriting_shr_assign_operator':
				return '>>=';
		}
	}

	
}

?>