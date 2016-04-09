# DPSG

Hier sollen alle Diözesen, Bezirke, Stämme und Siedlungen der DPSG maschinenlesbar im JSON-Format hinterlegt werden.
**Wer mag kann gerne den Datenbestand erweitern, möglichst mit Hinweis auf die Quelle (Link).**

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
                "typ": "Stamm",
                "name": "Eilendorf"
              },
              ...

Weitere Felder pro Ebene wie z. B. der Ort sind denkbar.
