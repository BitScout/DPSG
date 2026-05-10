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
            
            $bezirkEinfach = new stdClass();
            
            if(property_exists($bezirkKomplex, 'staemme') && is_array($bezirkKomplex->staemme)) {
                foreach($bezirkKomplex->staemme as $stammKomplex) {
                    if(!is_object($stammKomplex)) {
                        continue;
                    }

                    $stammesName = $stammKomplex->name;
                    $bezirkEinfach->$stammesName = new stdClass();
                    $bezirkEinfach->$stammesName->typ = $stammKomplex->typ;
                }
            }
            
            $bezirkName = $bezirkKomplex->name;
            $dioezeseEinfach->$bezirkName = new stdClass();
            $dioezeseEinfach->$bezirkName->staemme = $bezirkEinfach;
        }
    }

    $staemmeEinfach = new stdClass();

    if(property_exists($dioezeseKomplex, 'staemme') && is_array($dioezeseKomplex->staemme)) {
        foreach($dioezeseKomplex->staemme as $stammKomplex) {
            if(!is_object($stammKomplex)) {
                continue;
            }

            $stammesName = $stammKomplex->name;
            $staemmeEinfach->$stammesName = new stdClass();
            $staemmeEinfach->$stammesName->typ = $stammKomplex->typ;
        }

        $dioezeseEinfach = new stdClass();
        $dioezeseEinfach->staemme = $staemmeEinfach;

        $dioezeseName = $dioezeseKomplex->name;
        $output->$dioezeseName = new stdClass();
        $output->$dioezeseName->staemme = $dioezeseEinfach;
    } else {
        $dioezeseName = $dioezeseKomplex->name;
        $output->$dioezeseName = new stdClass();
        $output->$dioezeseName->bezirke = $dioezeseEinfach;
    }
}

file_put_contents('dpsg-v2_.json', json_encode($output, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE));
