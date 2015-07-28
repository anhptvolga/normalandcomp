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
	abstract public function convert_each_childrens();
	
	/**
	* Функция вычисления дерево в вид строки
	*/
	abstract public function calculate_tree_in_string();
	
	/**
	* Функция удаления всех сыновей текущего узла
	*/
	abstract public function delete_childrens();
	
	/**
	 * Взять тип класс в виде нормальной оператор
	 */
	public function get_label($nodetype)	{
		$label = array(
			'qtype_correctwriting_plus_operator'			=> '+',
			'qtype_correctwriting_multi_operator'	 	    => '*',
			'qtype_correctwriting_and_logic_operator'		=> '&&',
			'qtype_correctwriting_or_logic_operator'  		=> '||',
			'qtype_correctwriting_not_logic_operator' 		=> "!",
			'qtype_correctwriting_unary_minus_operator'		=> '-',
			'qtype_correctwriting_dereference_operator'		=> '*',
			'qtype_correctwriting_reference_operator'		=> '&',
			'qtype_correctwriting_pow_function'				=> 'pow()',
			'qtype_correctwriting_assign_operator'			=> '=',
			'qtype_correctwriting_minus_operator'			=> '-',
			'qtype_correctwriting_div_operator' 			=> '%',
			'qtype_correctwriting_div_operator'				=> '/',
			'qtype_correctwriting_equal_operator'			=> '==',
			'qtype_correctwriting_not_equal_operator'		=> '!=',
			'qtype_correctwriting_mem_acc_operator' 		=> '.',
			'qtype_correctwriting_pt_mem_acc_operator'		=> '->',		
			'qtype_correctwriting_subscript_operator'		=> '[]',
			'qtype_correctwriting_shift_right_operator' 	=> '>>',
			'qtype_correctwriting_shift_left_operator'		=> '<<',
			'qtype_correctwriting_less_equal_operator' 		=> '<=',
			'qtype_correctwriting_greater_equal_operator' 	=> '>=',
			'qtype_correctwriting_less_operator'			=> '<',
			'qtype_correctwriting_greater_operator'			=> '>',
			'qtype_correctwriting_plus_assign_operator'		=> '+=',
			'qtype_correctwriting_minus_assign_operator'	=> '-=',
			'qtype_correctwriting_multi_assign_operator' 	=> '*=',
			'qtype_correctwriting_div_assign_operator'		=> '/=',
			'qtype_correctwriting_shl_assign_operator'		=> '<<=',
			'qtype_correctwriting_shr_assign_operator'		=> '>>='
		);
		return $label[$nodetype];
	}

	
}

?>