<?php


function readExp(&$exp1, &$exp2) {
	
	$file = fopen('..\testinput\expession.txt', "r");
	if ($file !== FALSE) {
		$exp1 = fgets($file);
		$exp2 = fgets($file);
		fclose($file);
	}
	else {
		throw new Exception("No file input");
	}
	
	if (strlen($exp1) == 0) {
		throw new Exception("No expession in file");
	}
	if (strlen($exp2) == 0) {
		throw new Exception("Only one expression in file");
		
	}
}

function buildTree($expession, $typeOfVars) {
	
}


function printTreeToDOT($file, $curNode) {
	
}


?>