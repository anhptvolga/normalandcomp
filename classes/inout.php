<?php

$typeOfVars = array();

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
	
function readTypeVar($file) {
	
	ini_set('error_reporting', 30711);
	
	//$file = '..\testinput\test.xml';
	
	function startElement($parser, $name, $attrs) {
		global $typeOfVars;
		if ($name === "CONST") {
			if (count($attrs) != 2) {
				throw new Exception("Not enough attributes of counst in line ".xml_get_current_line_number($parser));
			}
			$typeOfVars[$attrs["VALUE"]] = $attrs["TYPE"]; 			
		}
		elseif ($name === "ARRAY" || $name === "VAR") {
			if (count($attrs) != 2) {
				throw new Exception("Not enough attributes of array in line ".xml_get_current_line_number($parser));
			}
			$typeOfVars[$attrs["NAME"]] = $attrs["TYPE"];
		}
		elseif ($name !== "EXPRESSION") {
			$line = xml_get_current_line_number($parser);
			throw new Exception("Unexpected element xlm in line ".$line." : ".$name);
		}
			   
	}
	
	function endElement($parser, $name) {
	}
	
	
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	if ( !file_exists($file) ) {
        throw new Exception("Xml-file not found");
    }
	
	$fp = fopen($file, "r");
	
	while ($data = fread($fp, 4096)) {
	    if (!xml_parse($xml_parser, $data, feof($fp))) {
	        throw new Exception("Error parsing xml in line ".xml_get_current_line_number($xml_parser));
	    }
	}
	
	xml_parser_free($xml_parser);
		
}


function printTreeToDOT($file, $curNode) {
	
}

?>