# Shortcodes

Die folgenden Shortcodes können verwendet werden, um in das WordPress-Projekt importierte offene Stellen in der Webseite auszugeben.

Bis auf einen, sind alle Parameter an beiden Shortcodes sind optional. Wenn ein Parameter nicht angegeben wird, gelten die Einstellungen zu Templates aus den Plugin-Einstellungen.

## für eine Listen-Ansicht

Aufbau des Shortcodes:

`[personioPositions lang="de" showfilter="1" filter="recruitingCategory,schedule" filtertype="linklist" templates="title,excerpt" excerpt="recruitingCategory,schedule" sort="asc" sortby="title"]`

### Hinweis

Dieses Plugin beschränkt die Anzahl der Einträge auf max. 10.
Die Pro-Version hat diese Beschränkung nicht.

## für eine Einzel-Ansicht

`[personioPosition lang="de" templates="title,content,formular" personioid="42"]`

## Parameter

Es sind folgende Parameter hierfür verfügbar:

### lang

* legt die Ausgabe-Sprache fest
* muss eine vom Plugin unterstützte Sprache sein
* wird als 2-Zeichen-Wert angegeben, z.B. "it" für Italienisch
* Beispiel:
  `[personioPositions lang="de"]`

### listing_template

* legt ein Listing-Template fest, welches genutzt werden soll

### showfilter

* nur für Listen-Ansicht verfügbar
* Wert 1 um Filter-Ansicht zu aktivieren
* Wert 0 um Filter-Ansicht zu deaktivieren
* welche Inhalte der Filter hat, wird im Parameter "filter" festgelegt

### filter

* nur für Listen-Ansicht verfügbar
* legt fest, welche Filter angezeigt werden
* verfügbare Werte werden kommagetrennt angegeben:
  * office
  * recruitingCategory => für die Kategorie
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
  * linklist_without_categories (nur in Pro)

### templates

* legt den Aufbau des Templates für eine Stelle fest
* verfügbare Werte werden kommagetrennt angegeben:
  * title => für den Titel der Stelle
  * excerpt => für die Liste der Kategorien denen die Stelle zugeordnet ist
  * content => für den kompletten Beschreibungstext zur Stelle
  * content_part => füe einen Teil des Beschreibungstextes der Stelle (nur in Verbindung mit _jobdescription_part_)
  * formular => für den Link zum Bewerbungsformular
  * meta => nur in Pro-Version verfügbar, wenn SEO aktiviert ist

### excerpt

* legt fest welche Kategorien an der Stelle als Ausschnitt angezeigt werden
* verfügbare Werte werden kommagetrennt angegeben:
  * office
  * recruitingCategory => für die Kategorie
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * schedule
  * experience

### jobdescription_template

* legt das Template für die Stellen-Beschreibung fest
* siehe [Beschreibungstemplate](description-template_de.md)

### jobdescription_part

* legt fest welcher Teil des Beschreibungstextes von dem Template _content_part_ verwendet werden soll
* Zahl zwischen 0 und max. der Beschreibungsteile

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

### groupby

* nur für Listen-Ansicht verfügbar
* gruppiert die Liste von Stellen nach einer der Eigenschaften
* verfügbare Werte sind:
  * office
  * recruitingCategory => für die Kategorie
  * occupationCategory
  * department
  * employmenttype
  * seniority
  * schedule
  * experience

### limit

* nur für Listen-Ansicht verfügbar
* beschränkt die Liste auf die angegebene Zahl

### nur Stellen mit spezifischen Eigenschaften aufzulisten

* nur für Listen-Ansicht verfügbar
* Anwendungsfälle:
  * Stellen nur aus einer Abteilung anzuzeigen
  * nur Vollzeit-Stellen anzeigen
* die Eigenschaft muss (englischsprachig) als Parameter angegeben werden, als Wert dazu die Datenbank-ID des gewünschten Wertes
* Beispiel:
  `[personioPositions department="42"]`
* die Datenbank-ID kann man auf folgendem Weg ermitteln:
  1. Richten Sie eine Liste mit Filter ein.
  2. Am Filter ergänzen Sie die Eigenschaft, die Sie suchen, z.B. "Abteilung".
  3. Schauen Sie sich die Liste im Browser an und filtern Sie nach der gesuchten Abteilung.
  4. In der URL ist daraufhin sowohl der Parameter als auch der Wert zu sehen, Beispiel: `?personiofilter[department]=42`
  5. Kopieren Sie das in Klammern stehende sowie die Zahl dahinter um diese im Shortcode wie in o.g. Beispiel zu sehen zusammen zu setzen.

## Beispiele

### Liste ohne Filter & mit Titel & Beschreibungstext pro Stelle

`[personioPositions templates="title,content"]`

### Liste mit Filter & Titel & Auszug pro Stelle

`[personioPositions showfilter="1" filter="recruitingCategory,schedule" templates="title,excerpt"]`

### Einzelansicht einer Stelle mit Titel, Inhalt & Bewerbungslink

`[personioPosition personioid="42" templates="title,content,formular"]`
