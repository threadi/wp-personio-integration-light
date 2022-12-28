# Personio Formulare

Dieser Formulargenerator wird durch das Plugin mitgeliefert. Er ermöglicht es die von Personio unterstützten Felder für Bewerbungsformulare zu verwenden. Dazu zählen sowohl die Pflichtfelder als auch zusätzliche Felder, die in einem Formular dem Interessenten angezeigt werden können.

Die Formulare werden in Form von Formular-Templates verwaltet. In diesen wird definiert, welche Felder im Frontend zu sehen sein sollen.

## Pflichtangaben

Seitens Personio gibt es folgende Pflichtangaben, die vom Generator berücksichtigt werden:

* Vorname und Nachname sind Pflichtfelder und können nicht deaktiviert oder verändert werden.
* E-Mail ist ein Pflichtfeld und kann ebenfalls nicht deaktiviert oder verändert werden.
* Lebenslauf als Datei-Upload muss vorhanden sein, ist aber nicht zwingend ein Pflichtfeld.

Eine E-Mail-Adresse ist pro Stelle nur 1 Mal zulässig. D.h. ein Bewerber kann sich nur 1 Mal auf eine bestimmte Stelle bewerben. Das Plugin berücksichtigt diese Maßgabe durch Personio.

## Einstellungen

* Formular-Template wählen
  * Die Liste der Formular-Templates ist unter "Bewerbungsformulartemplates" (im Menü) zu finden. Dort können sie auch konfiguriert werden. 
* Dateitypen für Upload-Feld auswählen
  * Die Angabe hier gilt für alle Upload-Felder.
* Aktiviere Datenschutz-Häkchen
  * Wenn aktiviert, wird unter jedem Bewerbungsformular ein Kontrollkästchen angezeigt, das den Bewerber auffordert, der Datenschutzerklärung der Website zuzustimmen. Bitte halte vor der Deaktivierung diesbezüglich Rücksprache mit deinem Datenschutzbeauftragten.
* Text für Datenschutz-Checkbox
* Nach Absenden des Formulars
  * Auswahl was nach Absenden des Formulars passieren soll:
    * Nachricht über Formular anzeigen
    * Nachricht ohne Formular anzeigen
    * Weiterleiten auf spezifische Seite
* Wähle eine Seite zur Weiterleitung aus
  * Um dieses Feld zu nutzen, wähle in der Einstellung „Nach Absenden des Formulars“ die Option „Weiterleiten auf spezifische Seite“ aus.

## Formulartemplates

Die Verwaltung von diesen ist im Menü unter "Bewerbungsformulartemplates" zu finden.

Das Plugin liefert bereits 3 fertige Templates mit. Man kann beliebig viele weitere Templates zusammenbauen.

Jedes Template kann man bearbeiten und duplizieren. Man kann sie auch löschen, außer das welches aktuell für Bewerbungsformulare verwendet wird.

Der Download der Konfiguration jedes Templates ist ebenfalls möglich um dieses z.B. zu einem anderen Projekt zu übertragen. Dort kann man die Konfiguration importieren.

Ein Template dient lediglich als Vorlage für die Ausgabe im Frontend. Es wird nicht für die weitere Verarbeitung von Bewerbungen verwendet und kann daher jederzeit angepasst werden ohne Rücksicht auf vorhandene Daten.

### Bearbeitung eines Templates

Die Boxen in der Bearbeitungsseite bauen sich wie folgt auf:

* Konfigurierte Felder
  * Diese Ansicht zeigt den Aufbaue des Formulars in dem Template.
  * Man kann die Felder per Drag&Drop verschieben.
  * Wo möglich, kann man per Häkchen definieren, dass das Feld ein Pflichtfeld ist.
  * Einige Felder kann man auch löschen per Klick auf das X rechts davon.
* Personio-Felder hinzufügen
  * Diese Box enthält einer Liste aller von Personio unterstützten optionalen Felder.
  * Sie können per Drag&Drop in die Box "Konfigurierte Felder" geschoben werden, um sie im Formular zu verwenden.
  * Wenn ein Feld bereits in "Konfigurierte Felder" steht, kann man es nicht ein 2. Mal hinzufügen.
* Individuelles Feld hinzufügen
  * Hier kann man ein Custom Field hinzufügen. Dieses muss man zunächst in Personio unter Einstellungen > Bewerbungen > Attribute erzeugen.
  * Danach kann man hier den Feld-Typ auswählen, den Namen des Feldes von Personio eintragen und die Beschriftung für das Formular ergänzen.
  * Nach Klick auf "Hinzufügen" wird das Feld in der Box "Konfigurierte Felder" eingefügt.
* Templates zu Stellen zuordnen
  * Ein Template kann auch ein oder mehreren Stellen direkt zugewiesen werden.
  * Dadurch ist es möglich unterschiedliche Bewerbungsformulare für die eigenen offenen Stellen zu verwenden.
  * In der Box hier muss man lediglich ein Häkchen vor der Stelle setzen in der dieses Template angezeigt werden soll.