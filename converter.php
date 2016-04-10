<?php

$input = json_decode(file_get_contents('dpsg.json'));

$output = new stdClass();
foreach($input as $dioezeseKomplex) {
	if(!is_object($dioezeseKomplex)) {
		continue;
	}
	
	$dioezeseEinfach = new stdClass;
	
	if(property_exists($dioezeseKomplex, 'bezirke') && is_array($dioezeseKomplex->bezirke)) {
		foreach($dioezeseKomplex->bezirke as $bezirkKomplex) {
			if(!is_object($bezirkKomplex)) {
				continue;
			}
			
			$bezirkEinfach = array();
			
			if(property_exists($bezirkKomplex, 'staemme') && is_array($bezirkKomplex->staemme)) {
				foreach($bezirkKomplex->staemme as $stammKomplex) {
					if(!is_object($stammKomplex)) {
						continue;
					}
					array_push($bezirkEinfach, $stammKomplex->name);
				}
			}
			
			$bezirkName = $bezirkKomplex->name;
			$dioezeseEinfach->$bezirkName = $bezirkEinfach;
		}
	}
	
	$dioezeseName = $dioezeseKomplex->name;
	$output->$dioezeseName = $dioezeseEinfach;
}

file_put_contents('dpsg_einfach.json', json_encode($output, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE));


