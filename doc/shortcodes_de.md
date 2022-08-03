# Shortcodes

Die folgenden Shortcodes können verwendet werden, um in das WordPress-Projekt importierte offene Stellen in der Webseite auszugeben.

Bis auf einen, sind alle Parameter an beiden Shortcodes sind optional. Wenn ein Parameter nicht angegeben wird, gelten die Einstellungen zu Templates aus den Plugin-Einstellungen.

## für eine Listen-Ansicht

Aufbau des Shortcodes:

`[personioPositions lang="de" showfilter="1" filter="recruitingCategory,schedule" filtertype="linklist" template="title,excerpt" excerpt="recruitingCategory,schedule" sort="asc" sortby="title"]`

## für eine Einzel-Ansicht

`[personioPosition lang="de" template="title,content,formular" id="42"]`

## Parameter

Die Parameter in den Shortcodes haben folgende Aufgaben:

### lang

* legt die Ausgabe-Sprache fest
* muss eine vom Plugin unterstützte Sprache sein
* wird als 2-Zeichen-Wert angegeben, z.B. "it" für italienisch
* Beispiel:
  `[personioPositions lang="de"]`

### showfilter

* nur für Listen-Ansicht verfügbar
* Wert 1 um Filter-Ansicht zu aktivieren
* Wert 0 um Filter-Ansicht zu deaktivieren
* welche Inhalte der Filter hat, wird im Parameter "filter" festgelegt

### filter

* nur für Listen-Ansicht verfügbar
* legt fest, welche Filter angezeigt werden
* verfügbare Werte werden kommagetrennt angegeben:
  * recruitingCategory => für die Kategorie
  * schedule
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * schedule
  * experience

### filtertype

* nur für Listen-Ansicht verfügbar
* legt fest wie die Filter-Ansicht ausgegeben wird
* nur einer der folgenden Werte darf angegeben werden:
  * linklist
  * selectbox

### templates

* legt den Aufbau des Templates für eine Stelle fest
* verfügbare Werte werden kommagetrennt angegeben:
  * title => für den Titel der Stelle
  * excerpt => für die Liste der Kategorien denen die Stelle zugeordnet ist
  * content => für den Beschreibungstext zur Stelle
  * formular => für den Link zum Bewerbungsformular
  * meta => nur in Pro-Version verfügbar, wenn SEO aktiviert ist

### excerpt

* legt fest welche Kategorien an der Stelle als Ausschnitt angezeigt werden
* verfügbare Werte werden kommagetrennt angegeben:
  * recruitingCategory => für die Kategorie
  * schedule
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * schedule
  * experience

### sort

* nur für Listen-Ansicht verfügbar
* legt die Sortierreihenfolge für die Stellen in der Liste fest
* nur einer der folgenden Werte darf angegeben werden:
  * asc => für aufsteigend
  * desc => für absteigend

### sortby

* nur für Listen-Ansicht verfügbar
* legt fest nach welchem Wert die Liste der Stellen sortiert werden soll
* nur einer der folgenden Werte darf angegeben werden:
  * title => nach dem Titel der Stelle
  * date => nach dem Datum zu dem die Stelle in Wordpress importiert wurde

### ids

* nur für Listen-Ansicht verfügbar
* schränkt die Ansicht auf die angegebenen PersonioIDs ein
* welche IDs verfügbar sind, ist in der Liste "offene Stellen" in der Spalte "PersonioId" zu sehen

### personioid

* einziges Pflichtfeld für Shortcodes
* nur für Detail-Ansicht verfügbar
* legt fest, welche konkrete Stelle angezeigt wird
* welche IDs verfügbar sind, ist in der Liste "offene Stellen" in der Spalte "PersonioId" zu sehen

## Beispiele

### Liste ohne Filter & mit Titel & Beschreibungstext pro Stelle

`[personioPositions template="title,content"]`

### Liste mit Filter & Titel & Auszug pro Stelle

`[personioPositions showfilter="1" filter="recruitingCategory,schedule" template="title,excerpt"]`

### Einzelansicht einer Stelle mit Titel, Inhalt & Bewerbungslink

`[personioPosition id="42" template="title,content,formular"]`