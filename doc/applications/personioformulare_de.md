# Personio Formulare

Dieser Formulargenerator wird durch das Pro-Plugin mitgeliefert. Er ermöglicht es die von Personio unterstützten Felder für Bewerbungsformulare zu verwenden. Dazu zählen sowohl die Pflichtfelder als auch zusätzliche Felder, die in einem Formular dem Interessenten angezeigt werden können.

Die Formulare werden in Form von Formular-Templates verwaltet. In diesen wird definiert, welche Felder im Frontend zu sehen sein sollen.

## Pflichtangaben

Seitens Personio gibt es folgende Pflichtangaben, die vom Generator berücksichtigt werden:

* Vorname und Nachname sind Pflichtfelder und können nicht deaktiviert oder verändert werden.
* E-Mail ist ein Pflichtfeld und kann ebenfalls nicht deaktiviert oder verändert werden.
* Lebenslauf als Datei-Upload muss vorhanden sein, ist aber nicht zwingend ein Pflichtfeld.

Eine E-Mail-Adresse ist pro Stelle nur 1 Mal zulässig. D.h. ein Bewerber kann sich nur 1 Mal auf eine bestimmte Stelle bewerben. Diese Vorgabe stammt von Personio und wird vom Plugin berücksichtigt.

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
  * Siehe Abschnitt unten.
* Templates zu Stellen zuordnen
  * Ein Template kann auch ein oder mehreren Stellen direkt zugewiesen werden.
  * Dadurch ist es möglich unterschiedliche Bewerbungsformulare für die eigenen offenen Stellen zu verwenden.
  * In der Box hier muss man lediglich ein Häkchen vor der Stelle setzen in der dieses Template angezeigt werden soll.

### Individuelle Felder

In dieser Box kann man ein Custom Field hinzufügen. Die Schritte dazu sind wie folgt:

1. In Personio unter Einstellungen > Bewerbungen > Attribute das gewünschte Feld anlegen.
2. In WordPress bei der Formular-Bearbeitung den Feld-Typ auswählen. Bitte die Hinweise unterhalb dazu beachten.
3. Danach den Namen des Feldes von Personio übernehmen (muss mit _custom_attribute_ beginnen) und die Beschriftung für das Formular in der Webseite ergänzen.
4. Nach Klick auf "Hinzufügen" wird das Feld in der Box "Konfigurierte Felder" eingefügt.

#### Feld-Typen

* Select
  * Sollte für Personio-Attribut-Felder vom Typ "Liste mit Optionen" verwendet werden.
  * Nach Auswahl dieses Typs muss man in einem 2. Feld die zur Auswahl stehenden Optionen eintragen.
  * Dazu folgendes Format pro Zeile verwenden:
    * Personio-custom-attribute:value
  * **Personio-custom-attribute** ist dabei der Wert für das Feld von Personio (muss mit _custom_attribute_ beginnen).
  * **value** ist die dem Nutzer angezeigte Bezeichnung
  * Unser Plugin übergibt das custom-attribut an die API von Personio wodurch Personio die Angabe eindeutig einem Listen-Wert zuordnen kann.
* Text
  * Sollte für Personio-Attribut-Felder vom Typ "Text" verwendet werden.
  * Keine weitere Konfiguration notwendig.
* Date
  * Sollte für Personio-Attribut-Felder vom Typ "Datum" verwendet werden.
  * Keine weitere Konfiguration notwendig.
* Multifile
  * Kann für Personio-Attribut-Felder vom Typ "Text" verwendet werden.
  * Wird durch Personio noch nicht unterstützt.
* File
  * Kann für Personio-Attribut-Felder vom Typ "Text" verwendet werden.
  * Wird durch Personio noch nicht unterstützt.
* Checkbox
  * Kann für Personio-Attribut-Felder vom Typ "Text" verwendet werden.
  * Wird durch Personio noch nicht unterstützt.
* E-Mail
  * Kann für Personio-Attribut-Felder vom Typ "Text" verwendet werden.
  * Wird durch Personio noch nicht unterstützt.
* Textarea
  * Kann für Personio-Attribut-Felder vom Typ "Text" verwendet werden.
  * Wird durch Personio noch nicht unterstützt.