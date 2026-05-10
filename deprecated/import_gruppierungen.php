<?php

/*
https://dpsg.de/sites/default/files/2026-01/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2025-07/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2025-01/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2024-07/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2023-08/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2023-01/satzung__anhang_gruppierungen%202023.pdf
https://dpsg.de/sites/default/files/2022-07/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2022-01/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2021-07/satzung_anhang_gruppierungen.pdf
https://dpsg.de/sites/default/files/2021-05/satzung_anhang_gruppierungen.pdf
*/

$quelldateien = [
    "2026-01.txt",
    //"2025-07.txt",
    //"2025-01.txt",
    //"2024-07.txt",
    //"2023-08.txt",
    //"2023-01.txt",
    //"2022-07.txt",
    //"2022-01.txt",
    "2021-07.txt",
    "2021-05.txt"
];

$dpsg = json_decode(file_get_contents('../dpsg-v2.json'));

$useSource = "2021-07";
$handle = fopen("satzung_anhang_gruppierungen/".$useSource.".txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        handleLine($line, $dpsg, $useSource);
    }

    fclose($handle);
}

file_put_contents('../dpsg-v2.json', json_encode($dpsg, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE));


function handleLine(string $line, stdClass $dpsg, string $source): void {
    $fields = explode("\t", $line);

    if (count($fields) != 3) {
        echo $line;
        echo "FOUND ".count($fields)." FIELDS: ".$fields[0];
        echo " --- FEHLERHAFTE ZEILE ---";
        die();
    }

    $ort = null;
    $name = trim($fields[0]);
    $typ = trim($fields[1]);
    $nummer = trim($fields[2]);

    $namenFields = explode(",", $name);
    $nummerFields = explode("/", $nummer);

    if (count($nummerFields) != 3) {
        echo $line;
        echo " --- FEHLERHAFTE STAMMESNUMMER ---";
        die();
    }

    if (count($namenFields) == 2) {
        $name = trim($namenFields[1]);
        $ort = trim($namenFields[0]);
    }

    echo $nummer." STAMM ".$name." ";

    if ($ort) {
        echo " IN ".$ort;
    }

    echo " \t";

    handleStamm($name, $ort, $nummer, $dpsg, $typ, $source);

    echo "\n";
}

function handleStamm(string $name, ?string $ort, string $nummer, stdClass $dpsg, $typ, string $source): void {
    $nummerFields = explode("/", $nummer);
    $dioezesennummer = (int)$nummerFields[0];
    $bezirksnummer = (int)$nummerFields[1];
    $stammesnummer = (int)$nummerFields[2];

    //if ($bezirksnummer > 3) die();

    $dioezese = findDioezese($dioezesennummer, $dpsg);
    $dioezesenname = $dioezese->name;
    unset($dioezese->name);
    echo " \tDIÖZESE ".$dioezesenname;

    $bezirk = findBezirk($bezirksnummer, $dioezese);

    if($bezirk == null) {
        echo "\tKEINE BEZIRKE";

        $bezirk = $dioezese;
        $bezirksname = "(Diözese)";
    } else {
        $bezirksname = $bezirk->name;
        unset($bezirk->name);

        if (!$bezirk) {
            echo " --- BEZIRK FEHLT ---";

            return;
        }
    }

    echo " \tBEZIRK ".$bezirksname;
    $stamm = findStamm($nummer, $name, $bezirk);

    if ($stamm != null) {
        echo " \tSTAMM BEREITS VORHANDEN: ".$name;
        //echo "\n".json_encode($stamm);

        if(!property_exists($stamm, "stammesnummer")) {
            $stamm->stammesnummer = $nummer; // Vollständige Stammesnummer
        }

        if(!property_exists($stamm, "nummer")) {
            $stamm->nummer = $stammesnummer; // Nummer des Stammes im Bezirk
        }

        if ($ort) {
            $stamm->ort = $ort;
        }

        return;
    } else {
        echo " \tSTAMM NICHT GEFUNDEN";
    }

    $stamm = new stdClass();
    $stamm->typ = $typ;
    $stamm->nummer = $stammesnummer; // Nummer des Stammes im Bezirk
    $stamm->stammesnummer = $nummer; // Vollständige Stammesnummer
    $stamm->quellen = ["https://www.dpsg.de/sites/default/files/".$source."/satzung_anhang_gruppierungen.pdf"];

    if ($ort) {
        $stamm->ort = $ort;
    }

    if (!property_exists($bezirk, "staemme")) {
        $bezirk->staemme = new stdClass();
    }

    $bezirk->staemme->$name = $stamm;
}

function findStamm(string $stammesnummer, string $name, stdClass $bezirk): ?stdClass {
    if (!property_exists($bezirk, "staemme")) {
        return null;
    }

    foreach($bezirk->staemme as $stamm) {
        if (property_exists($stamm, "stammesnummer") && $stamm->stammesnummer == $stammesnummer) {
            return $stamm;
        }
    }

    if (property_exists($bezirk->staemme, $name)) {
        return $bezirk->staemme->$name;
    }

    return null;
}

function findBezirk(int $nummer, stdClass $dioezese): ?stdClass {
    if (!property_exists($dioezese, "bezirke") || $dioezese->bezirke == null) {
        return null;
    }

    foreach($dioezese->bezirke as $name => $bezirk) {
        if ($bezirk->nummer == $nummer) {
            $bezirk->name = $name;

            return $bezirk;
        }
    }

    return null;
}

function findDioezese(int $nummer, stdClass $dpsg): ?stdClass {
    foreach($dpsg as $name => $dioezese) {
        if ($dioezese->nummer == $nummer) {
            $dioezese->name = $name;

            return $dioezese;
        }
    }

    return null;
}
