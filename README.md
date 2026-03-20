# DPSG

Hier sollen alle Diözesen, Bezirke, Stämme und Siedlungen der DPSG maschinenlesbar im JSON-Format hinterlegt werden.

**Wer mag, kann gerne den Datenbestand erweitern, möglichst mit Hinweis auf die Quelle (Link).**

Einige grundlegende Quellen:
* https://www.dpsg.de/sites/default/files/2026-01/satzung_anhang_gruppierungen.pdf
* https://www.dpsg.de/sites/default/files/2021-05/satzung_anhang_gruppierungen.pdf

Das Format in der Version 2 sieht folgendermaßen aus:

```
{
  "Eichstätt": {
    "nummer": 5,
    "staemme": {
      "Eichstätt Dom": {
        "typ": "Stamm",
        "ort": "Eichstätt",
        "web": "https:\/\/www.pfadfinder-eichstaett.de\/",
        "email": "mitmachen@pfadfinder-eichstaett.de",
        "quellen": ["https:\/\/www.dpsg-eichstaett.de\/index.php\/vor-ort\/eichstaett"]
      }
    }
  },
  "Würzburg": {
    "nummer": 22,
    "bezirke": {
      "Mainfranken": {
        "nummer": 10,
        "email": "bez-mfr@dpsg-wuerzburg.de",
        "staemme": {
          "Sankt Stephanus": {
            "typ": "Stamm",
            "ort": "Randersacker",
            "nummer": 1,
            "stammesnummer": "22\/10\/01",
            "quellen": [
              "https:\/\/www.dpsg-wuerzburg.de\/der-dv-wuerzburg\/bezirke\/mainfranken.html"
            ]
          }
        }
      }
    }
  }
}
```

**Hinweis:** JSON sieht vor, dass jedem `/` ein `\` vorangestellt wird.
Bei Verwendung dieser Datei muss also eine entsprechende Bibliothek verwendet werden
oder die `\` müssen auf andere Art entfernt werden.
