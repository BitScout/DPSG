# DPSG

Hier sollen alle Diözesen, Bezirke, Stämme und Siedlungen der DPSG maschinenlesbar im JSON-Format hinterlegt werden.

**Wer mag, kann gerne den Datenbestand erweitern, möglichst mit Hinweis auf die Quelle (Link).**

Das Format sieht folgendermaßen aus:

    [
      {
        "typ": "Diözese",
        "name": "Aachen",
        "bezirke": [
          {
            "typ": "Bezirk",
            "name": "Aachen-Land",
            "staemme": [
              {
                "typ": "Stamm",
                "name": "Brand-Kornelimünster"
              },
              {
                "typ": "Siedlung",
                "name": "Jakobiner"
              },
              ...
        ]
      },
      {
        "typ": "Diözese",
        "name": "Hamburg",
          "staemme": [
            {
              "typ": "Stamm",
              "name": "Brand-Kornelimünster"
            },
            {
              "typ": "Stamm",
              "name": "Eilendorf"
            },
            ...

Weitere Felder pro Ebene wie z. B. der Ort sind denkbar.
Eine Diözese kann jetzt auch direkt Stämme enthalten, wenn sie keine Bezirke hat.
Für die `dpsg.json` gibt es nun auch ein JSON-Schema, mit dem diese validiert werden kann.

Mit einer lokalen PHP-Umgebung kann aus der `dpsg.json` automatisch 
die `dpsg_einfach.json` erzeugt werden: 

`php converter.php`
