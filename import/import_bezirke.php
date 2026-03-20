<?php

$dpsg = json_decode(file_get_contents('../dpsg-v2.json'));

$handle = fopen("bezirke_offiziell.csv", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        handleLine($line, $dpsg);
    }

    fclose($handle);
}

file_put_contents('../dpsg-v2.json', json_encode($dpsg, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE));


function handleLine(string $line, stdClass $dpsg): void {
    //echo $line;

    $fields = explode("\t", $line);

    if (count($fields) != 2) {
        echo $line;
        echo "FOUND ".count($fields)." FIELDS: ".$fields[0];
        echo " --- FEHLERHAFTE ZEILE ---";
        die();
    }

    $name = trim($fields[0]);
    $nummer = trim($fields[1]);

    echo "BEZIRK ".$name." (".$nummer.")";

    handleBezirk($name, $nummer, $dpsg);

    echo "\n";
}

function handleBezirk(string $name, string $nummer, stdClass $dpsg): void {
    $nummerFields = explode("/", $nummer);
    $dioezesennummer = (int)$nummerFields[0];
    $bezirksnummer = (int)$nummerFields[1];

    $dioezese = findDioezese($dioezesennummer, $dpsg);
    $dioezesenName = $dioezese->name;
    unset($dioezese->name);

    echo " \t DIÖZESE ".$dioezesenName;
    $bezirk = findBezirk($bezirksnummer, $dioezese);

    if ($bezirk) {
        //echo " \tB ".$bezirk->name;
        unset($bezirk->name);
    } else {
        echo " --- BEZIRK FEHLT ---";

        $bezirk = new stdClass();
        $bezirk->nummer = $bezirksnummer;
        $bezirk->staemme = new stdClass;

        $dioezese->bezirke->$name = $bezirk;
    }
}

function findBezirk(int $nummer, stdClass $dioezese): ?stdClass {
    //echo " \tSuche Bezirk ".$nummer;
    if (!property_exists($dioezese, "bezirke") || $dioezese->bezirke == null) {
        echo " \tKeine Bezirke";
        return null;
    }

    //echo "\tDI ".json_encode($dioezese);
    foreach($dioezese->bezirke as $name => $bezirk) {
        //echo "\tB ".$name." -> ".json_encode($bezirk);
        //echo "\tB ".$bezirk->nummer ." vs ". $nummer;

        if ($bezirk->nummer == $nummer) {
            if($name != null) {
                $bezirk->name = $name;
            }

            return $bezirk;
        }
    }

    return null;
}

function findDioezese(int $nummer, stdClass $dpsg): ?stdClass {
    foreach($dpsg as $name => $dioezese) {
        //echo "\tD ".json_encode($dioezese);
        //echo "\tD ".$dioezese->nummer;

        if ($dioezese->nummer == $nummer) {
            $dioezese->name = $name;

            return $dioezese;
        }
    }

    return null;
}
