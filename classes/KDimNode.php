<?php

require_once 'BaseNode.php';

/**
 * \class qtype_correctwriting_k_dim_node
 *
 * \brief Базовый класс для операции сложения, умножения, логического сложения, логического умножения
 *
 */
abstract class qtype_correctwriting_k_dim_node extends qtype_correctwriting_base_node {
	
	public $childrens = array();			///< список сыновей
	
	/**
	 * Функция сортировки сыновей
	 */
	public function sortChildrens() {
		
		for ($i = 0; $i < count($this->childrens); $i++) {
			for ($j = $i + 1; $j < count($this->childrens); $j++){
				if ($this->childrens[$i]->treeinstring > $this->childrens[$j]->treeinstring) {
					$tmp = $this->childrens[$i];
					$this->childrens[$i] = $this->childrens[$j];				
					$this->childrens[$j] = $tmp;
				}
			}
		}
		
	}
	
	/**
	* Функция перенести сын вверх
	*/
	public function goUpChildrens() {
		for ($i = 0; $i < count($this->childrens); $i ++) {
			if (get_class($this) == get_class($this->childrens[$i])) {
				
				for ($j = count($this->childrens[$i]->childrens)-1; $j >=0; $j --) {
					array_push($this->childrens, $this->childrens[$i]->childrens[$j]);
				}
				array_splice($this->childrens, $i, 1);
				$i--;
			}
		}		
		
	}
	
	public function convertEachChildrens() {
		
		foreach ($this->childrens as &$children) {	
			// convert each children
			$children->convert($this);
			while ($children->ptonewchild !== null) {
				$tmp = $children->ptonewchild;
				$children = clone $children->ptonewchild;
				$tmp->deleteChildrens();
				$children->ptonewchild = null;
				$children->convert($this);
			}	
		}	
	}
	
	public function calculatetreeinstring() {
		$this->treeinstring = $this->getLabel(get_class($this)).' ';
		
		foreach ($this->childrens as $value) {
			$this->treeinstring .= $value->treeinstring.' ';
		}
	}
	
	public function deleteChildrens()	{ 
		for ($i = 0; $i < count($this->childrens); $i ++) {
			$this->childrens[$i]->deleteChildrens();
		}
		unset ($this->childrens);
		$this->childrens = array();
	}
	
	public function __clone() {
		for ($i = 0; $i < count($this->childrens); $i++) {
			$this->childrens[$i] = clone $this->childrens[$i];
		}
	}
}

/////////////////////////////////////////////////////////////////

/**
 * \class qtype_correctwriting_plus_operator
 *
 * \brief Класс для операции сложения
 * 
 */
class qtype_correctwriting_plus_operator extends qtype_correctwriting_k_dim_node {
	
	public function convert($parent) {
		// 1.	Проверка каждого его сына: 
		//		его сын – операция сложения то добавить его сыновья вверх – вызов 
		$this->goUpChildrens();
		
		// 2.	Вызов функции преобразования каждого его сына.
		//		Снова вызов goUpChildren
		$this->convertEachChildrens();
		$this->goUpChildrens();
		
		// 3.	Вычислить константу: сложение констант в списке сыновьей.
		$value = 0;								// новое значение
		$isAdd = FALSE;							// флаг что есть ли константа
		for ($i = 0; $i < count($this->childrens); $i ++){
			// Если сын - это операция
			if (get_class($this->childrens[$i]) == 'qtype_correctwriting_operand') {
				// Если сын - это констнатна
				if ($this->childrens[$i]->number !== null) {
					// добавить значение
					$value += $this->childrens[$i]->number;
					// удалить этот сын из списка сыновей
					array_splice($this->childrens, $i, 1);
					$i--;	
					// установить флаг
					$isAdd = TRUE;
				}
			}
		}
		
		// Если есть константа и ее значение не равно нулю
		if ($isAdd && $value != 0) {
			// создать новую константу
			$tmp = new qtype_correctwriting_operand(strval($value), $value);
			$tmp->calculatetreeinstring();
			// добавлять в список сыновей
			array_push($this->childrens, $tmp);
		}
		
		// 4.	Сортировать сыновей – вызов функция sortChildrens
		$this->sortChildrens();
		
		// 5.	Если в списке сыновей только один сын то узел преобразуется в вид его сын
		if (count($this->childrens) == 1)	{
			$this->ptonewchild = $this->childrens[0];
		}
	}
	
}

/**
* \class qtype_correctwriting_multi_operator
*
* \brief Класс для операции умножения
*
*/
class qtype_correctwriting_multi_operator extends qtype_correctwriting_k_dim_node {
	
	public function convert($parent) {
		$isHavePlus = FALSE;
		$isAdd = FALSE;
		
		// 1.	Проверка каждого его сына: 
		//		его сын – операция сложения то добавить его сыновья вверх – вызов goUpChildren
		$this->goUpChildrens();
		// 2.	Вызов функции преобразования каждого его сына.
		//		снова сыновья вверх – вызов goUpChildren
		$this->convertEachChildrens();
		$this->goUpChildrens();
		// 3.	Вычислить константу: сложение констант в списке сыновьей.
		$value = $this->calculateConst($isHavePlus, $isAdd);
		// сортировать сыновья
		$this->sortChildrens();
		// преобразовать дробей
		$this->convertDivInMult();
		// сортировать сыновья
		$this->sortChildrens();
		// если есть операция сложения то раскрывать скобки
		if ($isHavePlus) {
			$openedSum = $this->openBracket();
			$this->ptonewchild = $openedSum;
			return;
		}
		// если только 1 сын то преобразовать в вид только его
		if (count($this->childrens) == 1) {
			$this->ptonewchild = $this->childrens[0];
			return;
		}
		// проверка знак
		$numOfNegative = $this->countNegative($value);				// число операнд отрицательно
		// если нечетно то добавляем знак унарный минус
		if ($numOfNegative % 2 == 1) {
			$tmp = new qtype_correctwriting_unary_minus_operator();
			$tmp->children = clone $this;
			$tmp->calculatetreeinstring();
			$this->ptonewchild = $tmp;
			return;
		}
		// дублировать сыны
		if ($isAdd) {
			$this->duplicateChild($value);
		}
	}
	
	/**
	 * Вычислить константу: сложение констант в списке сыновьей.
	 *
	 * \param [out] isHavePlus флаг: есть ли в списке сыновей операция сложения
	 * \param [out] isAdd флаг: есть ли в списке сыновей целая константа
	 *
	 * \return значение целой константы если есть
	 */
	public function calculateConst(&$isHavePlus, &$isAdd) {
		$value = 1;							// произведение целых констант
		$dvalue = 1;						// произведение вещественных констант
		$isdAdd = FALSE;					// флаг есть ли вещественных констант
		// для каждого сына
		for ($i = 0; $i < count($this->childrens); $i++) {
			// проверка есть ли операция сложения
			if (get_class($this->childrens[$i]) == 'qtype_correctwriting_plus_operator')
				$isHavePlus = TRUE;
			// вычисление констант
			if (get_class($this->childrens[$i]) == 'qtype_correctwriting_operand') {
				if (is_int($this->childrens[$i]->number)) {
					$value *= $this->childrens[$i]->number;
					array_splice($this->childrens, $i, 1);
					$i--;
					$isAdd = true;
				}
				elseif (is_double($this->childrens[$i]->number)) {
					$dvalue *= $this->childrens[$i]->number;
					array_splice($this->childrens, $i, 1);
					$i--;
					$isdAdd = true;
				}
			}
		}
	
		if ($isAdd) {
			// создать новую константу
			$tmp = new qtype_correctwriting_operand(strval($value), $value);
			$tmp->calculatetreeinstring();
			// добавлять в список сыновей
			array_push($this->childrens, $tmp);
		}
		if ($isdAdd) {
			// создать новую константу
			$tmp = new qtype_correctwriting_operand(strval($dvalue), $dvalue);
			$tmp->calculatetreeinstring();
			// добавлять в список сыновей
			array_push($this->childrens, $tmp);
		}
		return $value;
	}
	
	/**
	 * Считать количесво операндов отрицательные
	 *
	 * \param [out] value значение целой константы после преобразования знака
	 * \return 
	 */
	public function countNegative(&$value) {
		$numOfNegative = 0;			// результат
		for ($i = 0; $i < count($this->childrens); $i++) {
			if (get_class($this->childrens[$i]) == 'qtype_correctwriting_unary_minus_operator') {
				++$numOfNegative;
				$this->childrens[$i] = $this->childrens[$i]->children;
			}
			elseif (get_class($this->childrens[$i]) == 'qtype_correctwriting_operand' &&
				$this->childrens[$i]->number !== null &&
				$this->childrens[$i]->number < 0) {
				++$numOfNegative;
				$this->childrens[$i]->number =  - $this->childrens[$i]->number;
				$this->childrens[$i]->name = strval($this->childrens[$i]->number);
				if (is_int($this->childrens[$i]->number))
					$value = -$value;
			}
	
		}
		return $numOfNegative;
	}

	/**
	* Дублировать сыновей
	* 
	* \param [in] value количество раз для дублирования сыновей
	*/	
	public function duplicateChild($value) {
		$toDup = array();
		for ($i = 0; $i < count($this->childrens); $i++) {
			if (get_class($this->childrens[$i]) == 'qtype_correctwriting_operand') {
				if ($this->childrens[$i]->number === null || is_double($this->childrens[$i]->number)) { 
					array_push($toDup, $this->childrens[$i]);
				}
			}
			if (get_class($this->childrens[$i]) != 'qtype_correctwriting_operand')
				array_push($toDup, $this->childrens[$i]);
		}
		// создать сын для добавления
		$childToAdd;
		if (count($toDup) == 1) {
			$childToAdd = $toDup[0];
		}
		else {
			$childToAdd = new qtype_correctwriting_multi_operator();
			$childToAdd->childrens = $toDup;
			$childToAdd->calculatetreeinstring();
		}
		// преобразовать в qtype_correctwriting_plus_operator
		$tmp = new qtype_correctwriting_plus_operator();
		for ($i = 0; $i < abs($value); $i++) {
			array_push($tmp->childrens, clone $childToAdd);
		}
		$tmp->calculatetreeinstring();
		
		// добавление знак
		if ($value < 0) {
			$t = new qtype_correctwriting_unary_minus_operator();
			$t->children = $tmp;
			$t->calculatetreeinstring();
			$this->ptonewchild = $t;
		}
		else
			$this->ptonewchild = $tmp;
	}
	
	/**
	* Функция преобразования дробей при умножении
	*/
	public function convertDivInMult()	{
		$divop = array();				// временный массив
		// взять дроби
		for ($i = 0; $i < count($this->childrens); $i ++) {
			if (get_class($this->childrens[$i]) == 'DivOperator') {
				array_push($divop, $this->childrens[$i]);
				array_splice($this->childrens, $i, 1);
				$i --;
			}
		} 
		// создать новый 
		if (count($divop) > 1) {
			$tmp = new qtype_correctwriting_multi_operator();
			foreach ($divop as $i => $value) {
				array_push($tmp->childrens, $value->right);
			}
			$newch = new DivOperator();
			$newch->left = new qtype_correctwriting_operand("1", 1);
			$newch->right = $tmp;
			$newch->calculatetreeinstring();
			$divop = array($newch);
		}
		// возвращать в списку сыновей
		foreach ($divop as $value) {
			array_push($this->childrens, $value);
		}
	}
	
	/**
	* Функция раскрытия скобок
	* \return указатель на узел сложения произведений
	*/
	public function openBracket(){
		$multElements = array();		// список умножителей
		$conf = array();				// перестановка
		// Инициализировать список умножителей.
		for ($i = 0; $i < count($this->childrens); $i++) {
			$conf[$i] = 0;
			if (get_class($this->childrens[$i]) == 'qtype_correctwriting_plus_operator') {
				array_push($multElements, $this->childrens[$i]->childrens);
			}
			else {
				array_push($multElements, array($this->childrens[$i]));
			}
		}
		//////////////////////////////////////////////////////////////////////////
		$res = new qtype_correctwriting_plus_operator();				// новая корень
		$isStop = FALSE;						// флаг стопа
		while (!$isStop)	{
			
			// создать узел умножения
			$cur = new qtype_correctwriting_multi_operator();
			for ($i = 0; $i < count($multElements); $i++) {
				array_push($cur->childrens, clone $multElements[$i][$conf[$i]]);
			}
			array_push($res->childrens, $cur);
			
			// Вычисление следующей перестановки
			$prev = 1;
			for ($i = count($multElements) - 1; $i >= 0; $i--) {
				$conf[$i] = (1 + $conf[$i]) % count($multElements[$i]);
				if ($i == 0 && $conf[$i] == 0)
					$isStop = true;
				if ($conf[$i] != 0)
					break;
			}
			
		}
		
		return $res;
	}
	
}

/**
 * \class qtype_correctwriting_and_logic_operator
 *
 * \brief Класс для операции логического умножения
 * 
 */
class qtype_correctwriting_and_logic_operator extends qtype_correctwriting_k_dim_node {
	
	public function convert($parent) {
		// преобразуем каждый сын
		$this->convertEachChildrens();
	
		// При использовании логической операции И одинаковых операндов, 
		// преобразуется в вид только операнд
		$isAllNot = TRUE;
		for ($i = 0; $i < count($this->childrens); $i++) {
			if (get_class($this->childrens[$i]) != 'qtype_correctwriting_not_logic_operator') {
				$isAllNot = FALSE;
			}
			for ($j = $i + 1; $j < count($this->childrens); $j++) {
				// если одинаковые то удалить один узел
				if (isTreeEqual($this->childrens[$i], $this->childrens[$j])) {
					array_splice($this->childrens, $i, 1);
					$j--;
				}
			}
		}
		
		// если отстаться только 1 узел то преобразуем в виде только сын
		if (count($this->childrens) == 1) {
			$this->ptonewchild = $this->childrens[0];
			return;
		}
		
		// если все операнды - операция ! то преобразовать в вид  операции ||
		if ($isAllNot) {
			$newChild = new qtype_correctwriting_or_logic_operator();
			for ($i = 0; $i < count($this->childrens); $i++)
				array_push($newChild->childrens, clone $this->childrens[$i]->children);
			$newChild->calculatetreeinstring();
			
			$tmp = new qtype_correctwriting_not_logic_operator();
			$tmp->children = $newChild;
			$tmp->calculatetreeinstring();
			$this->ptonewchild = $tmp;
			return;
		}
		// сортировать сыновья
		$this->sortChildrens();
	}
	
}

/**
* \class qtype_correctwriting_or_logic_operator
*
* \brief Класс для операции логического сложения
*
*/
class qtype_correctwriting_or_logic_operator extends qtype_correctwriting_k_dim_node {
	
	public function convert($parent) {
		// преобразуем каждый сын
		$this->convertEachChildrens();
		// удалить одинаковые узлы
		$vtemp = array();			// список новых сыновей после удаления
		for ($i = 0; $i < count($this->childrens); $i++) {
			$isAdd = TRUE;
			$j = 0;
			// проверка текущего узла был ли раньше
			while ($isAdd && $j < count($vtemp)) {
				if (isTreeEqual($this->childrens[$i], $vtemp[$j])) {
					$isAdd = false;
				}
				++$j;
			}
			// если не был то добавляем
			if ($isAdd)	{
				array_push($vtemp, $this->childrens[$i]);
			}
		}
		// присваивать новый список сыновей
		$this->childrens = $vtemp;
		// преобразуем сравнений операций в вид >=, <=
		$this->reduceCompare();
		// преобразуем каждый сын
		$this->convertEachChildrens();
		// сортируем сыновья
		$this->sortChildrens();
		// преобразуем в МДНФ 
		$this->convertQuineMcCluskey();
	}
	
	/** 
	 * Проверка можно ли два операнды операции сравнения одинаковые
	 * 
	 * \param [in] one операнд первой операции сравнения
	 * \param [in] two операнд второй операции сравнения
	 *
	 * \return true если они одинаковые и можно преобразовать в вид >= или <=, в противном случае false
	 */
	public function isChildsSame($one, $two) {
		// проверка левого сына a и b
		if (isTreeEqual($one->left, $two->left))
			return TRUE;
		// проверка левого сына а с обратном знаком
		$tmp = new qtype_correctwriting_unary_minus_operator();
		// создать унарный минус
		$tmp->children = clone $one->left;
		// преобразовать новый узел
		$tmp->ptonewchild = null;
		$tmp->convert(tmp);
		while ($tmp->ptonewchild !== null) {
			$tmp = $tmp->ptonewchild;
			$tmp->ptonewchild = null;
			$tmp->convert(tmp);
		}
		// сравнение сына a с обратном знаком
		return isTreeEqual($tmp, $two->left);
	}
	
	/** 
	 * Преобразовать операций сравнений > (<) и == в вид >= (<=)
	 */
	public function reduceCompare()	{
		$vtemp = array();		// временной вектор сыновей
		// нахождение сравнения можно сокращать
		for ($i = 0; $i < count($this->childrens); $i++) {
			if ($this->childrens[$i] !== null) {
				$isAdd = TRUE;			// флаг : добавить текущий узел в временной вектор
				$lnode;					// указатель на узел сравнение 
				$enode;					// указатель на узел равенства
				for ($j = $i + 1; $j < count($this->childrens) && $isAdd; $j++) {
					$isComp = FALSE;		// флаг: нашлось ли узлы для сокращения
					if (get_class($this->childrens[$i]) == 'qtype_correctwriting_greater_operator' &&
						get_class($this->childrens[$j]) == 'qtype_correctwriting_equal_operator') {
						$isComp = TRUE;
						$lnode = $this->childrens[$i];
						$enode = $this->childrens[$j];
					}
					else if (get_class($this->childrens[$i]) == 'qtype_correctwriting_equal_operator' &&
						get_class($this->childrens[$j]) == 'qtype_correctwriting_greater_operator') {
						$isComp = true;
						$lnode = $this->childrens[$j];
						$enode = $this->childrens[$i];
					}
					else if (get_class($this->childrens[$i]) == 'qtype_correctwriting_less_operator' &&
						get_class($this->childrens[$j]) == 'qtype_correctwriting_equal_operator') {
						$isComp = true;
						$lnode = $this->childrens[$i];
						$enode = $this->childrens[$j];
					}
					else if (get_class($this->childrens[$i]) == 'qtype_correctwriting_equal_operator' &&
						get_class($this->childrens[$j]) == 'qtype_correctwriting_less_operator') {
						$isComp = true;
						$lnode = $this->childrens[$j];
						$enode = $this->childrens[$i];
					}
					// проверка можно ли сокращать
					if ($isComp && $this->isChildsSame($lnode, $enode)){
						// создать новый узел
						if (get_class($lnode) == 'qtype_correctwriting_less_operator')
							$tmp = new qtype_correctwriting_less_equal_operator();
						else
							$tmp = new qtype_correctwriting_greater_equal_operator();
						// присваивать значения
						$tmp->left = $lnode->left;
						$tmp->right = $lnode->right;
						$isAdd = FALSE;
						$tmp->calculatetreeinstring();
						// добавить в временной вектор сыновей
						array_push($vtemp, $tmp);
						// удалить из вектора сыновей
						$this->childrens[$i] = $this->childrens[$j] = null;
					}
	
				}
				if ($isAdd)
					array_push($vtemp, $this->childrens[$i]);
			}
		}
		// присваивать новые сыновья
		$this->childrens = $vtemp;
	}
	
	/** 
	 * Проверка возможно скеивания и нахождение разной позиции в записях сыновей
	 *
	 * \param [in] one первая двоичная запись сына
	 * \param [in] two вторая двоичная запись сына
	 *
	 * \return неотрицательное число - разная позиция для склеивания при возможно, в противном случае -1
	 */
	public function isChangeable($one, $two) {
		$diff = 0;			// общее число разных позициях
		$diff01 = 0;		// число разных позициях 0 и 1
		$res = -1;			// результат
		// проверка каждой позиции записях
		for ($i = 0; $i < strlen($one); $i++) {
			if ($one[$i] != $two[$i]) {
				$diff ++;
				if ($one[$i] != '-' && $two[$i] != '-')
					++$diff01;
				$res = $i;
			}
		}
		// если 2 записи разные только в 1 позиции
		// и в этой позиции 0 и 1
		if ($diff == 1 && $diff01 == 1)
			return $res;
		// невозможно склеивать
		return -1;
	}
	
	/** 
	 * Считать значение в таблице покрытий
	 *
	 * \param [in] imp простая импликанта
	 * \param [in] old импликанта
	 *
	 * \return значение в таблице покрытий
	 */
	public function isCover($imp, $old)	{
		// проверка каждой записи
		for ($i = 0; $i < strlen($imp); $i++) {
			if ($imp[$i] != '-' && $imp[$i] != $old[$i])
				return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Проверка узел был ли в векторе
	 * \param [in] node укзатель узла, нужен проверять
	 * \param [in] vec вектор узлов
	 *
	 * \return true если в векторе появился узел, в противном случае false
	 */
	public function isHaveNode($node, $vec)	{
		foreach ($vec as $value) {
			if (isTreeEqual($node, $value)){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * Найти позицию сына в полном виде функции
	 * \param [in] node узказатель на сын
	 * \param [in] vec вектор полного вида функции
	 *
	 * \return позицию сына в полном виде функции
	 */
	public function posInFullExp($node, $vec)	{
		for ($i = 0; $i < count($vec); $i ++) {
			if (isTreeEqual($node, $vec[$i])){
				return $i;
			}
		}
		return -1;
	}
	
	/**
	 * Создать полный вид функции
	 *
	 * \param [out] fullExp полный вид функции
	 */
	public function makeFullExp(&$fullExp)	{
		// каждый сын
		for ($i = 0; $i < count($this->childrens); $i++) {
			$isNew = TRUE;
			$nodetype = get_class($this->childrens[$i]);
	
			if ($nodetype == 'qtype_correctwriting_not_logic_operator') { // если он операция ! 
				if (!$this->isHaveNode($this->childrens[$i]->children, $fullExp)) {
					array_push($fullExp, $this->childrens[$i]->children);
				}
			}
			else if ($nodetype == 'qtype_correctwriting_and_logic_operator') { // если он операция &&
				// каждый сын операции &&
				for ($j = 0; $j < count($this->childrens[$i]->childrens); $j++){
					$nodetype = get_class($this->childrens[$i]->childrens[$j]);
					if ($nodetype == 'qtype_correctwriting_not_logic_operator') { // если он операция !
						if (! $this->isHaveNode($this->childrens[$i]->childrens[$j]->children, $fullExp)) {
							array_push($fullExp, $this->childrens[$i]->childrens[$j]->children);
						}
					}
					else {
						if (!$this->isHaveNode($this->childrens[$i]->childrens[$j], $fullExp))
							array_push($fullExp, $this->childrens[$i]->childrens[$j]);
					}
				}
			}
			else {
				if (!$this->isHaveNode($this->childrens[$i], $fullExp))
					array_push($fullExp, $this->childrens[$i]);
			}
		}
	}
	
	/**
	 * Создать двоичный вид для каждого сына
	 * 
	 * \param [in] fullExp полный вид функции
	 * \param [out] eachChild двоичные записи каждого сына
	 */
	public function makeEachChildrenNotation($fullExp, &$eachChild)	{
		// создать двоичный вид для каждого сына
		for ($i = 0; $i < count($this->childrens); $i++)
		{
			// инициализация все -----
			$eachChild[$i] = "";
			for ($j = 0; $j < count($fullExp); $j++)
				$eachChild[$i] .= '-';
			$nodetype = get_class($this->childrens[$i]);
			if ($nodetype == 'qtype_correctwriting_not_logic_operator') { // если операция !
				$eachChild[$i][$this->posInFullExp($this->childrens[$i]->children, $fullExp)] = '0';
			}
			else if ($nodetype == 'qtype_correctwriting_and_logic_operator') { // если операция &&
				for ($k = 0; $k < count($this->childrens[$i]->childrens); $k++)
				{
					$nodetype = get_class($this->childrens[$i]->childrens[$k]);
					if ($nodetype == 'qtype_correctwriting_not_logic_operator') { // если он операция !
						$eachChild[$i][$this->posInFullExp($this->childrens[$i]->childrens[$k]->children, $fullExp)] = '0';
					}
					else {
						$eachChild[$i][$this->posInFullExp($this->childrens[$i]->childrens[$k], $fullExp)] = '1';
					}
				}
			}
			else { // просто операнд
				$eachChild[$i][$this->posInFullExp($this->childrens[$i], $fullExp)] = '1';
			}
		}
	}
	
	/**
	 * Создать простые импликанты из двоичных записях
	 *
	 * \param [in,out] eachChild двоичные записи преобразуется в простые импликанты
	 */
	public function makeImplicate(&$eachChild) {
		// создать простые импликанты
		$isStop = FALSE;			// флаг стопа
		while (!$isStop) {
			$tmp = array();				// временной вектор
			$isStop = TRUE;
			$isChanged = array_fill(0, count($eachChild), FALSE);  // флаг склеивали записях
			for ($i = 0; $i < count($eachChild); $i++)	{
				for ($j = $i + 1; $j < count($eachChild); $j++) {
					// взять разную позицию в записях
					$pos = $this->isChangeable($eachChild[$i], $eachChild[$j]);
					if ($pos != -1) {
						// склеивать
						$s = $eachChild[$i];
						$s[$pos] = '-';
						$isAdded = FALSE;
						for ($k = 0; $k < count($tmp); $k++) {
							if ($tmp[$k] == $s)
								$isAdded = true;
						}
						if (!$isAdded)
							array_push($tmp, $s);
						$isChanged[$i] = true;
						$isChanged[$j] = true;
						$isStop = false;
					}
				}
			}
			// добавлять записи не склеивали
			for ($i = 0; $i < count($eachChild); $i++) {
				if (!$isChanged[$i])
					array_push($tmp, $eachChild[$i]);
			}
			$eachChild = $tmp;
		}
		
		// удалить лишный
		$isDel = FALSE;
		$i = 0;
		while ($i < count($eachChild) && !$isDel)
		{
			if ($this->find_first_not_of($eachChild[$i],'-') == -1) {
				$isDel = TRUE;
			}
			++$i;
		}
		if ($isDel)
			$eachChild = array();
	}
	
	/**
	 * Нахождение совокупности простых импликант, соответствующих минимальной ДНФ
	 * \param [in] eachChild простые импликанты
	 * \param [in] impl начальные импликанты
	 * \param [in] coverage таблица покрытий
	 *
	 * \return число, биты которого описывается МДНФ
	 */
	public function findMinCover($eachChild, $impl, $coverage)	{
		// нахождение совокупности простых импликант, соответствующих минимальной ДНФ
		$mincf = (1 << count($eachChild)) - 1;		// минимальный совокупность, отмечать в битах
		$minsize = 1000000;								// текущий размер
		$cur;											// минимальный размер
		$columCover;								// текущий совокупность, отмечать в битах
	
		for ($cf = 1; $cf < (1 << count($eachChild)); $cf++) {
			$cur = 0;
			$columCover = 0;
			
			for ($i = 0; $i < count($eachChild); $i++) {
				// i-й бит
				if (($cf >> $i) & 1 == 1) {
					++$cur;
					if ($cur > $minsize)
						break;
					// если покрывал то включить j-й бит
					for ($j = 0; $j < count($impl); $j++) {
						if ($coverage[$i][$j])
							$columCover = $columCover | (1 << $j);
					}
				}
			}
			
			// если текущая совокупность покрывал все
			if ($columCover == (1 << count($impl)) - 1 && $cur < $minsize) {
				$minsize = $cur;
				$mincf = $cf;
			}
		}
		
		return $mincf;
	}
	
	/**
	 * Функция нахождения первой позиции не символа в строке
	 * \param [in] str строка, в которой нужно найти
	 * \param [in] ch символ
	 * \param [pos] начальная позиция нахождения
	 * \return позиция при удалось найти, инача -1
	 */
	public function find_first_not_of($str, $ch, $pos = 0)	{
		for ($i = $pos; $i < strlen($str); $i ++) {
			if ($str[$i] != $ch) {
				return $i;
			}
		}
		return -1;
	}
	
	/**
	 * Создать новый дерево МДНФ
	 *
	 * \param [in] eachChild простые импликанты
	 * \param [in] mincf число, биты которого описывается МДНФ
	 * \param [in] fullExp полный вид функции
	 */
	public function makeMinTree($eachChild, $mincf, $fullExp)	{
		// удалить текущие сыновья
		$this->childrens = array();
		for ($i = 0; $i < count($eachChild); $i++) {
			// Если i-й бит включенно
			if (($mincf >> $i) & 1 == 1) {
				$isOne = -1 == $this->find_first_not_of($eachChild[$i], '-', $this->find_first_not_of($eachChild[$i],'-') + 1);
				if ($isOne) {
					// только 1 операнд
					$pos = $this->find_first_not_of($eachChild[$i],'-');
					if ($pos != -1) {
						if ($eachChild[$i][$pos] == '1')
							array_push($this->childrens, clone $fullExp[$pos]);
						else {
							$tmp = new qtype_correctwriting_not_logic_operator();
							$tmp->children = clone $fullExp[$pos];
							array_push($this->childrens, $tmp);
							$tmp->calculatetreeinstring();
						}
					}
				}
				else {
					// операция &&
					$tmp = new qtype_correctwriting_and_logic_operator();
					for ($j = 0; $j < strlen($eachChild[$i]); $j++) {
						if ($eachChild[$i][$j] == '1') {
							array_push($tmp->childrens, clone $fullExp[$j]);
						}
						elseif ($eachChild[$i][$j] == '0') {
							$notOp = new qtype_correctwriting_not_logic_operator();
							$notOp->children = clone $fullExp[$j];
							$notOp->calculatetreeinstring();
							array_push($tmp->childrens, $notOp);
						}
					}
					$tmp->sortChildrens();
					$tmp->calculatetreeinstring();
					array_push($this->childrens, $tmp);
				}
			}
		}
	}
	
	/**
	 * Преобразовать методом Квайна - Мак-Класки
	 */
	public function convertQuineMcCluskey()	{
		$fullExp = array();							// полный вид функции
		$eachChild = array();						// двоичная запись каждого сына
		
		// создать полный вид функции
		$this->makeFullExp($fullExp);
		//var_dump($fullExp);
		
		// создать двоичный вид для каждого сына
		$this->makeEachChildrenNotation($fullExp, $eachChild);
		//var_dump($eachChild);
		
		$impl = $eachChild;					// вектор импликант
		// создать простые импликанты, сохраняются в векторе eachChild
		$this->makeImplicate($eachChild);
		
		// создать таблицу покрытий
		$coverage = array();
		for ($i = 0; $i < count($eachChild); $i++)	{
			$coverage[$i] = array();
			for ($j = 0; $j < count($impl); $j++) {
				$coverage[$i][$j] = $this->isCover($eachChild[$i], $impl[$j]);
			}
		}
		
		// нахождение совокупности простых, импликантсоответствующих минимальной ДНФ
		$mincf = $this->findMinCover($eachChild, $impl, $coverage);
		//var_dump($mincf);
		// создать новый дерево МДНФ
		$this->makeMinTree($eachChild, $mincf, $fullExp);
		// если только 1 сын то преобразовать в вид только сын
		if (count($this->childrens) == 1)
			$this->ptonewchild = $this->childrens[0];
	}
}


?>