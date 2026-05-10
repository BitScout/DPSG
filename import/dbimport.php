<?php

$initDateienSql = "DROP TABLE IF EXISTS datei; CREATE TABLE IF NOT EXISTS datei (datum DATE NOT NULL);";
$initEintragSql = "DROP TABLE IF EXISTS eintrag;
    CREATE TABLE IF NOT EXISTS eintrag (
       datum DATE NOT NULL,
       name TEXT NOT NULL,
       typ TEXT NOT NULL,
       nummer TEXT NOT NULL
   );";
$initDioezesenViewSql = "DROP VIEW IF EXISTS dioezesen; CREATE VIEW dioezesen AS SELECT nummer, group_concat(DISTINCT name) namen, group_concat(datum) datum FROM eintrag WHERE typ = 'Diözese' GROUP BY nummer;";
$initBezirkeViewSql = "DROP VIEW IF EXISTS bezirke; CREATE VIEW bezirke AS SELECT nummer, group_concat(DISTINCT name) namen, group_concat(datum) datum FROM eintrag WHERE typ = 'Bezirk' GROUP BY nummer;";
$initStaemmeViewSql = "DROP VIEW IF EXISTS staemme; CREATE VIEW staemme AS SELECT nummer, group_concat(DISTINCT name) namen, group_concat(DISTINCT typ) typen, group_concat(datum) datum FROM eintrag WHERE typ = 'Stamm' OR  typ = 'Siedlung' GROUP BY nummer;";


$db = new SQLite3("dpsg.sqlite");
$db->exec($initDateienSql);
$db->exec($initEintragSql);
$db->exec($initDioezesenViewSql);
$db->exec($initBezirkeViewSql);
$db->exec($initStaemmeViewSql);


$importDir = 'importdateien';
$inputFiles = [];
$scannedFiles = scandir($importDir);

foreach($scannedFiles as $file) {
    if(preg_match('~^\d{4}-\d{2}-\d{2}\.txt$~', $file)) {
        array_push($inputFiles, $importDir.'/'.$file);
    }
}

echo "\n\t\t\tDiözesen\tBezirke\tStämme\tSiedlungen";
foreach($inputFiles as $file) {
    importFile($db, $file);
}
echo "\n\nImport abgeschlossen\n\n";

function importFile($db, $path) {
    preg_match('/(\d{4}-\d{2}-\d{2})/', $path, $matches);
    $date = trim($matches[1]);
    echo "\nImportiere ".$date." \t";

    $sql = "INSERT INTO datei (datum) VALUES (DATE('".$date."'));";
    $db->exec($sql);

    $stats = [
        'Diözese' => 0,
        'Bezirk' => 0,
        'Stamm' => 0,
        'Siedlung' => 0,
    ];
    $lineCounter = 0;

    $handle = fopen($path, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $lineCounter++;
            $typ = handleLine($db, $date, $line);

            if($typ == null) {
                echo "\nFehlerhafte Zeile ".$lineCounter.": ".$line."\n";
                continue;
            }

            $stats[$typ]++;
        }

        fclose($handle);
    }

    echo $stats['Diözese']."\t\t".$stats['Bezirk']."\t".$stats['Stamm']."\t".$stats['Siedlung']."\t";
}

function handleLine($db, $date, $line): ?string {
    $fields = explode("\t", $line);

    if (count($fields) != 3 || str_contains($line, 'DPSG') || str_contains($line, 'DPSG')) {
        return null;
    }

    $name = trim(str_replace("'", '', $fields[0]));
    $typ = trim($fields[1]);
    $nummer = trim($fields[2]);

    if (strlen($nummer) != 8 || str_contains($name, 'Stamm ') || str_contains($name, 'Stamm)')) {
        return null;
    }

    $sql = "INSERT INTO eintrag (datum, name, typ, nummer) VALUES (DATE('".$date."'), '".$name."', '".$typ."', '".$nummer."');";
    $db->exec($sql);

    return $typ;
}
