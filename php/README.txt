Vorbereitung: Fr. Riegel sollte die OXVM mit dem darin enthaltenen Demoshop inkl. Demodaten
https://github.com/OXID-eSales/oxvm_eshop installieren.



1)      Erstellen Sie mittels PHP ein Script, welches
    alle Artikelnummer (oxartnum) und
    Preise (oxprice) der Artikel in der Kategorie Kites zurückliefert

Hinweis: (aus den Datenbank-Tabellen oxarticle, oxcategory und oxobject2category).

2)      Erweitern Sie das Script so, dass zusätzlich der Durchschnittspreis der zurückgelieferten Artikel ausgegeben wird.

3)      Erweitern Sie das Script und die Datenbank so, dass für jede durchgeführte Abfrage die
Ergebnisse in zwei neuen Tabellen protokolliert werden

Hinweis: Tabelle L1  (LaufendenNr, Uhrzeit, Durchschnittspreis) , Tabelle L2 (NrAusL1, Artikelnummer, Preis)

4)      Erweitern Sie das Script so, dass die Ergebnisse zusätzlich in den Dateien log.json, log.csv
 (optional auch log.yaml und  log.xml) im jeweiligen Format protokolliert werden.

 Bei der Umsetzung sollte Fr. Riegel bitte auf Verständlichkeit, Sicherheit, Performance achten und darauf dass ihr Script mehrmals gleichzeitig ausgeführt werden kann.

 Doppelter Code sollte vermieden werden.


Die gewählten Lösungsansätze sollten kurz beschrieben werden.