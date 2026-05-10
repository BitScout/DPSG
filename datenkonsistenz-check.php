<?php

$referenceFileName = "2021-07.txt";

$dpsg = json_decode(file_get_contents('dpsg-v2.json'));

$dioezesenNachNummer = [];
$stammesnummern = [];

foreach($dpsg as $name => $dioezese) {
    echo "\nDIÖZESE ".$dioezese->nummer." ".$name;

    if (array_key_exists($dioezese->nummer, $dioezesenNachNummer)) {
        echo "\n\t\t\tDIÖZESE ".$name." hat überlappende Nummer mit ".$dioezesenNachNummer[$dioezese->nummer];
    }

    if (property_exists($dioezese, "bezirke")) {
        foreach($dioezese->bezirke as $name => $bezirk) {
            echo "\n\tBEZIRK ".$bezirk->nummer." ".$name;
            handleBezirk($bezirk, $name, $stammesnummern);
        }
    } elseif (property_exists($dioezese, "staemme")) {
        handleBezirk($dioezese, $name, $stammesnummern);
    }
}

//echo "\n\n".implode("\n", array_keys($stammesnummern));
echo "\n\n".count($stammesnummern)." Stämme insgesamt in dpsg-v2.json";


$handle = fopen("import/satzung_anhang_gruppierungen/".$referenceFileName, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $split = explode("\t", $line);

        if(count($split) != 3) {
            echo "\n!!! Unlesbare Zeile: ".$line." (Enthält ".count($split)." Elemente)";
            continue;
        }

        $typ = trim($split[1]);
        $stammesnummer = trim($split[2]);

        if (array_key_exists($stammesnummer, $stammesnummern)) {
            $stammesnummern[$stammesnummer] =+ 1;
        } else {
            echo "\n!!! Stammesnummer fehlt im JSON: ".$line;
        }
    }

    fclose($handle);

    foreach($stammesnummern as $stammesnummer => $cnt) {
        if($cnt != 1) {
            echo "\n!!! Stammesnummer ".$stammesnummer." hat ".$cnt." Erwähnungen im offiziellen Dokument";
        }
    }
}

echo "\n\n";

function handleBezirk(stdClass $bezirk, string $name, &$stammesnummern) {
    foreach($bezirk->staemme as $name => $stamm) {
        handleStamm($stamm, $name);

        if (property_exists($stamm, "stammesnummer")) {
            if (array_key_exists($stamm->stammesnummer, $stammesnummern)) {
                echo "\n!!! Duplikat bei der Stammesnummer";
            }

            $stammesnummern[$stamm->stammesnummer] = 0;
        }
    }
}

function handleStamm(stdClass $stamm, string $name) {
    $error = null;
    $hatStammesnummer = property_exists($stamm, "stammesnummer");
    $istNichtAktiv = property_exists($stamm, "status") && $stamm->status != "aktiv";

    // Wenn der Stamm als aufgelöst oder Status unklar markiert wurde,
    // dann ignorieren wir, dass er keine offizielle Nummer hat.
    if(!$istNichtAktiv) {
        if (!property_exists($stamm, "nummer")) {
            $error = "!!! Keine Nummer";
        }

        if (!$hatStammesnummer) {
            $error = "!!! Keine Stammesnummer";
        }
    }

    echo "\n\t\tSTAMM ";
    if($hatStammesnummer) {
        echo $stamm->stammesnummer." ";
    }
    echo $name;

    if($error != null) {
        echo "\n!!! ".$error;
    }
}
