<?php
    
    
require_once('inout.php');


function process_each_expression($filename, $filegv) {
	try {
		$exp = readExp($filename);
		$tree = build_tree($exp);
		// преобразовать
		$tree->ptonewchild = null;
		$tree->convert($tree);
		while ($tree->ptonewchild != null){
			$tree = $tree->ptonewchild;
			$tree->ptonewchild = null;
			$tree->convert($tree);
		}
		// печать DOT файл
		$file = fopen($filegv, "w");
		fwrite($file,"digraph {\n");
		print_tree_to_dot($file, $tree);
		fwrite($file,'}');
		fclose($file);
		
		return $tree;
	}
	catch (Exception $e) {
		echo "In file $filename: ".$e->getMessage()." \n";
		return null;
	}
}

if ($argc > 3) {
	echo "Too much argument in command line\n";
}
elseif ($argc < 3) {
	echo "Too few argument in command line\n";
}
else {
	// преобразовать каждое выражение
	$tree1 = process_each_expression($argv[1], "tree1.gv");
	$tree2 = process_each_expression($argv[2], "tree2.gv");
	
	// сравнение
	if ($tree1 !== null && $tree2 !== null) {
		$file = fopen("result.txt", "w");
		if (is_tree_equal($tree1, $tree2)) {
			fwrite($file, 'Expression equals');
		}	
		else {
			fwrite($file, 'Epression NOT equals');
		}
		fclose($file);
	}
}
    
    
?>