# Shortcodes

Die folgenden Shortcodes können verwendet werden, um die in das WordPress-Projekt importierten offenen Stellen und
deren Daten in der Webseite auszugeben.

Shortcodes sollten nur verwendet werden, wenn Ihr Theme oder Ihr PageBuilder nicht vom Plugin unterstützt wird. Mit dem
Block-Editor oder einem von _Personio Integration Pro_ unterstützten PageBuilder können Sie stattdessen die Widgets des
Plugins verwenden.

## Übersicht

| Shortcode                                                                         | Zweck                                                  | Verfügbar in |
|-----------------------------------------------------------------------------------|--------------------------------------------------------|--------------|
| [`[personioPositions]`](#liste-von-stellen-personiopositions)                     | Liste von Stellen inkl. Filter                         | Light & Pro  |
| [`[personioPosition]`](#einzelne-stelle-personioposition)                         | Einzelansicht einer Stelle                             | Light & Pro  |
| [`[personio_integration_widget_application_button]`](#bewerbungs-button)          | Button zur Bewerbung auf eine Stelle                   | Light & Pro  |
| [`[personio_integration_widget_description]`](#beschreibung)                      | Beschreibung einer Stelle                              | Light & Pro  |
| [`[personio_integration_widget_details]`](#details)                               | Details (z.B. Standort, Abteilung) einer Stelle        | Light & Pro  |
| [`[personio_integration_widget_description_part]`](#teil-der-beschreibung)        | ein einzelner Teil der Beschreibung einer Stelle       | Pro          |
| [`[personio_integration_widget_field]`](#feld)                                    | ein einzelnes Feld einer Stelle                        | Pro          |
| [`[personio_integration_widget_salary]`](#gehalt)                                 | Gehalt einer Stelle                                    | Pro          |
| [`[personio_integration_widget_files]`](#dateien)                                 | Dateien einer Stelle                                   | Pro          |
| [`[personio_integration_widget_videos]`](#videos)                                 | Videos einer Stelle                                    | Pro          |
| [`[personio_integration_widget_contact_person]`](#ansprechperson)                 | Ansprechperson einer Stelle                            | Pro          |
| [`[personio_integration_widget_map]`](#karte)                                     | Karte mit den Standorten der Stellen                   | Pro          |
| [`[personio_integration_widget_position_count]`](#anzahl-der-stellen)             | Anzahl der Stellen                                     | Pro          |
| [`[personio_integration_widget_term_count]`](#anzahl-der-eintraege-einer-taxonomie) | Anzahl der Einträge einer Taxonomie                  | Pro          |
| [`[personio_integration_widget_general_application_form]`](#allgemeines-bewerbungsformular) | allgemeines Bewerbungsformular, unabhängig von einer Stelle | Pro |
| [`[personio_integration_gravityform]`](#formular-von-gravity-forms)               | Formular von Gravity Forms für eine Bewerbung          | Pro          |

`[personio_integration_widget_archive]` ist ein Alias von `[personioPositions]`, `[personio_integration_widget_single]`
ist ein Alias von `[personioPosition]`. Beide verwenden genau dieselben Attribute.

## Allgemeine Hinweise

* Alle Attribute sind optional, sofern sie nicht als _Pflichtfeld_ gekennzeichnet sind.
* Wird ein Attribut nicht angegeben, gilt die entsprechende Einstellung des Plugins unter _Stellen > Einstellungen > Templates_.
* Listen von Werten werden kommagetrennt angegeben, z.B. `templates="title,excerpt"`.
* Bei den Namen der Attribute wird nicht zwischen Groß- und Kleinschreibung unterschieden. WordPress wandelt sie in
  Kleinbuchstaben um.
* Mit _(Pro)_ gekennzeichnete Attribute funktionieren nur mit aktiver Lizenz von _Personio Integration Pro_.
* Unbekannte Attribute werden ignoriert.
* Die Shortcodes der Pro-Widgets stehen nur zur Verfügung, wenn das jeweilige Widget unter _Stellen > Erweiterungen_
  aktiviert ist.
* _Personio Integration Pro_ enthält einen Shortcode-Generator, der beim Erstellen der Shortcodes mit allen Optionen
  hilft. Sein Tab _Widget Shortcodes_ listet alle Widget-Shortcodes mit den in Ihrem Projekt möglichen Werten auf.

## Liste von Stellen: personioPositions

Gibt eine Liste von Stellen aus. Optional mit einem Filter oberhalb der Liste.

Aufbau des Shortcodes:

`[personioPositions lang="de" showfilter="1" filter="recruitingCategory,schedule" filtertype="linklist" templates="title,excerpt" excerpt="recruitingCategory,schedule" sort="asc" sortby="title"]`

### Hinweis

_Personio Integration Light_ beschränkt die Anzahl der Einträge auf max. 10.
_Personio Integration Pro_ hat diese Beschränkung nicht und unterstützt eine Seitennavigation.

### Attribute für den Inhalt

#### lang

* legt die Ausgabe-Sprache fest
* muss eine vom Plugin unterstützte Sprache sein, ansonsten wird die Fallback-Sprache verwendet
* wird als 2-Zeichen-Wert angegeben, z.B. `de` für Deutsch
* Beispiel: `[personioPositions lang="de"]`

#### templates

* legt fest, welche Teile pro Stelle angezeigt werden und in welcher Reihenfolge
* verfügbare Werte, kommagetrennt:
  * `title` => Titel der Stelle
  * `excerpt` => Details der Stelle (z.B. Standort, Abteilung), siehe Attribut `excerpt`
  * `content` => vollständige Beschreibung der Stelle
  * `formular` => Link oder Button zur Bewerbung
  * `aboutus` => "Über uns"-Text des Unternehmens _(Pro)_
  * `content_part` => ein einzelner Teil der Beschreibung, siehe Attribut `jobdescription_part` _(Pro)_
  * `files` => Dateien der Stelle _(Pro, wenn die Erweiterung für Dateien aktiviert ist)_
  * `videos` => Videos der Stelle _(Pro, wenn die Erweiterung für Videos aktiviert ist)_
  * `comments` => Kommentare zur Stelle _(Pro, wenn die Erweiterung für Kommentare aktiviert ist)_
* Beispiel: `[personioPositions templates="title,excerpt,formular"]`

#### listing_template

* legt das Template für die Liste fest
* verfügbare Werte:
  * `default`
  * `listing`
  * `accordion` _(Pro)_
  * `tile` _(Pro)_
  * `table` _(Pro)_
  * `popup` _(Pro)_
* Beispiel: `[personioPositions listing_template="tile"]`

#### excerpt

* legt fest, welche Details an jeder Stelle angezeigt werden, wenn `excerpt` in `templates` verwendet wird
* verfügbare Werte, kommagetrennt: die Slugs der Taxonomien, siehe [Taxonomien](#taxonomien)
* Beispiel: `[personioPositions templates="title,excerpt" excerpt="office,employmenttype"]`

#### excerpt_template

* legt das Template für die Details fest
* verfügbare Werte:
  * `default`
  * `list`
  * `extended_list` _(Pro)_

#### jobdescription_template

* legt das Template für die Beschreibung fest, wenn `content` in `templates` verwendet wird
* verfügbare Werte:
  * `default`
  * `list`
* mehr Infos hier: [Beschreibungs-Template](description-template_de.md)

#### jobdescription_part

* nur mit _Personio Integration Pro_ verfügbar
* legt fest, welcher Teil der Beschreibung angezeigt wird, wenn `content_part` in `templates` verwendet wird
* der Wert ist die Kennung des Teils (ein Hash-Wert)
* die in Ihrem Projekt möglichen Werte sind im Shortcode-Generator im Tab _Widget Shortcodes_ bei
  `[personio_integration_widget_description_part]` aufgelistet

#### jobdescription_part_template

* nur mit _Personio Integration Pro_ verfügbar
* legt das Template für den Teil der Beschreibung fest
* verfügbare Werte:
  * `default`
  * `h2`
  * `h4`
  * `without_heading`

#### donotlink

* Wert `1` => die Titel der Stellen werden nicht mit ihrer Detailseite verlinkt
* Wert `0` => die Titel werden verlinkt
* Beispiel: `[personioPositions donotlink="1"]`

### Attribute für Auswahl und Reihenfolge

#### ids

* beschränkt die Liste auf die angegebenen PersonioIDs, kommagetrennt
* welche IDs verfügbar sind, ist in der Liste "Stellen" in der Spalte "PersonioID" zu sehen
* Beispiel: `[personioPositions ids="42,43"]`

#### limit

* beschränkt die Liste auf die angegebene Anzahl an Stellen
* `0` für keine Beschränkung (in _Personio Integration Light_ max. 10)
* Beispiel: `[personioPositions limit="5"]`

#### sort

* Sortierreihenfolge der Liste
* verfügbare Werte:
  * `asc` => aufsteigend
  * `desc` => absteigend

#### sortby

* der Wert, nach dem die Liste sortiert wird
* verfügbare Werte:
  * `title` => nach dem Titel der Stelle
  * `date` => nach dem Datum, an dem die Stelle in WordPress importiert wurde

#### groupby

* gruppiert die Liste nach einer Taxonomie
* verfügbare Werte: einer der Slugs der Taxonomien, siehe [Taxonomien](#taxonomien)
* Beispiel: `[personioPositions groupby="office"]`

#### Nur Stellen mit bestimmten Eigenschaften anzeigen

* Anwendungsfälle:
  * nur Stellen aus einer Abteilung anzeigen
  * nur Vollzeit-Stellen anzeigen
* als Name des Attributs wird der Slug der Taxonomie verwendet, als Wert die ID des gewünschten Eintrags
* Beispiel: `[personioPositions department="42"]`
* _(Pro)_ es können mehrere IDs kommagetrennt angegeben werden, z.B. `[personioPositions office="12,34"]`
* mehrere Taxonomien können kombiniert werden, z.B. `[personioPositions department="42" employmenttype="7"]`
* nutzt ein Besucher den Filter für dieselbe Taxonomie, ersetzt der Filter diese Einschränkung
* die ID können Sie auf folgendem Weg ermitteln:
  1. Richten Sie eine Liste mit Filter ein.
  2. Ergänzen Sie am Filter die gesuchte Taxonomie, z.B. "department" für die Abteilung.
  3. Schauen Sie sich die Liste im Browser an und filtern Sie nach dem gesuchten Eintrag.
  4. In der URL sind daraufhin die Taxonomie und die ID zu sehen, Beispiel: `?personiofilter[department]=42`.
  5. Verwenden Sie beides im Shortcode wie im o.g. Beispiel.

### Attribute für den Filter

#### showfilter

* Wert `1` => Filter oberhalb der Liste anzeigen
* Wert `0` => Filter ausblenden
* welche Filter angezeigt werden, wird im Attribut `filter` festgelegt

#### filter

* legt fest, welche Filter angezeigt werden
* verfügbare Werte, kommagetrennt: die Slugs der Taxonomien, siehe [Taxonomien](#taxonomien)
* _(Pro)_ zusätzlich `pi_places`, das Hauptstandort und weitere Standorte in einem Filter zusammenfasst
* _(Pro)_ die Filter werden in der Reihenfolge angezeigt, die in den Einstellungen festgelegt ist
* Beispiel: `[personioPositions showfilter="1" filter="recruitingCategory,office"]`

#### filtertype

* legt fest, wie der Filter dargestellt wird
* nur einer der folgenden Werte darf angegeben werden:
  * `linklist` => Liste von Links, gruppiert nach Taxonomie
  * `select` => Auswahlfelder mit einem Button zum Suchen
  * `linklist_without_categories` => Liste von Links ohne Überschriften _(Pro)_
  * `search` => Suchfeld, das Attribut `filter` wird ignoriert _(Pro)_
  * `circle_search` => Umkreissuche um einen Ort, das Attribut `filter` wird ignoriert _(Pro, erfordert einen
    eingerichteten Geo-Dienst)_
* bei einem ungültigen Wert wird `linklist` verwendet

#### filtercombine

* nur mit _Personio Integration Pro_ verfügbar
* Wert `AND` => die in verschiedenen Filtern gewählten Werte werden kombiniert, z.B. zeigt "Vollzeit" und "Berlin" nur
  Vollzeit-Stellen in Berlin
* Standard: leer
* siehe [Kombination von Filtern](#kombination-von-filtern)
* Beispiel: `[personioPositions showfilter="1" filter="employmenttype,office" filtertype="select" without_reload="1" filtercombine="AND"]`

#### without_reload

* nur mit _Personio Integration Pro_ verfügbar
* Wert `1` => der Filter blendet die Stellen direkt im Browser ein und aus, ohne die Seite neu zu laden
* es werden nur die bereits auf der Seite geladenen Stellen gefiltert
* siehe [Kombination von Filtern](#kombination-von-filtern)
* Beispiel: `[personioPositions showfilter="1" filter="office" without_reload="1"]`

#### Kombination von Filtern

Wie die Werte mehrerer Filter zusammenwirken, hängt vom Filtertyp und vom Attribut `without_reload` ab:

| Filter                                          | Verhalten ohne `filtercombine`                        | Verhalten mit `filtercombine="AND"`                          |
|-------------------------------------------------|-------------------------------------------------------|--------------------------------------------------------------|
| `select` mit Neuladen                           | alle gewählten Werte werden kombiniert                | alle gewählten Werte werden kombiniert                       |
| `linklist` mit Neuladen                         | die Werte der nacheinander geklickten Links werden kombiniert | die Werte der nacheinander geklickten Links werden kombiniert |
| `linklist_without_categories` mit Neuladen _(Pro)_ | nur der zuletzt geklickte Wert wird verwendet      | die Werte der nacheinander geklickten Links werden kombiniert |
| `select` ohne Neuladen _(Pro)_                  | nur der zuletzt gewählte Wert wird verwendet, die anderen Auswahlfelder werden zurückgesetzt | alle gewählten Werte werden kombiniert |
| `linklist` / `linklist_without_categories` ohne Neuladen _(Pro)_ | nur der zuletzt geklickte Wert wird verwendet | ein Wert pro Taxonomie, alle Taxonomien werden kombiniert, ein zweiter Klick auf einen Wert hebt die Auswahl auf |

Hinweise:

* mit Neuladen funktioniert die Kombination nur, wenn alle Filter Teil desselben Shortcodes sind
* kombiniert bedeutet: es werden nur Stellen angezeigt, die zu allen gewählten Werten passen

### Attribute für das Layout

#### anchor

* ID des HTML-Elements des Filters
* wird automatisch erzeugt, wenn nicht angegeben

#### link_to_anchor

* ID eines HTML-Elements auf der Seite, zu dem der Browser nach dem Filtern springt (ohne `#`)
* wird automatisch erzeugt, wenn nicht angegeben
* Beispiel: `[personioPositions showfilter="1" filter="office" link_to_anchor="stellen"]`

#### classes

* zusätzliche CSS-Klassen für die Ausgabe, durch Leerzeichen getrennt

## Einzelne Stelle: personioPosition

Gibt eine einzelne Stelle aus.

Aufbau des Shortcodes:

`[personioPosition lang="de" templates="title,content,formular" personioid="42"]`

### Attribute

#### personioid

* _Pflichtfeld_
* die PersonioID der anzuzeigenden Stelle
* welche IDs verfügbar sind, ist in der Liste "Stellen" in der Spalte "PersonioID" zu sehen

#### lang

* legt die Ausgabe-Sprache fest, siehe [lang](#lang)

#### templates

* legt fest, welche Teile der Stelle angezeigt werden und in welcher Reihenfolge
* verfügbare Werte: siehe [templates](#templates)

#### excerpt

* legt fest, welche Details angezeigt werden, wenn `excerpt` in `templates` verwendet wird
* verfügbare Werte: siehe [excerpt](#excerpt)

#### excerpt_template

* legt das Template für die Details fest, siehe [excerpt_template](#excerpt_template)

#### jobdescription_template

* legt das Template für die Beschreibung fest, siehe [jobdescription_template](#jobdescription_template)

#### jobdescription_part _(Pro)_ und jobdescription_part_template _(Pro)_

* siehe [jobdescription_part](#jobdescription_part) und [jobdescription_part_template](#jobdescription_part_template)

#### donotlink

* Wert `0` => der Titel wird mit der Detailseite der Stelle verlinkt
* Standard: `1` (nicht verlinkt)

#### classes

* zusätzliche CSS-Klassen für die Ausgabe, durch Leerzeichen getrennt

## Shortcodes für Teile einer Stelle

Diese Shortcodes geben einen einzelnen Teil einer Stelle aus. Sie sind für die Detailseite einer Stelle gedacht, z.B. in
einem Template Ihres Themes oder PageBuilders. Dort verwenden sie automatisch die Stelle der Seite.

### Bewerbungs-Button

`[personio_integration_widget_application_button]`

* gibt den Button zur Bewerbung auf die Stelle aus
* Attribute:
  * `personioid` => PersonioID der Stelle, wenn nicht auf der Detailseite einer Stelle verwendet
  * `show_back_to_list` => Wert `0` blendet den Link "zurück zur Liste" auf der Detailseite aus
  * `classes` => zusätzliche CSS-Klassen

### Beschreibung

`[personio_integration_widget_description]`

* gibt die Beschreibung der Stelle aus
* Attribute:
  * `personioid` => PersonioID der Stelle, wenn nicht auf der Detailseite einer Stelle verwendet
  * `template` => Template für die Beschreibung, siehe [jobdescription_template](#jobdescription_template)

### Details

`[personio_integration_widget_details excerpt="office,department"]`

* gibt die Details der Stelle aus, z.B. Standort oder Abteilung
* Attribute:
  * `excerpt` => die anzuzeigenden Details, kommagetrennt, siehe [Taxonomien](#taxonomien); ohne dieses Attribut wird nichts angezeigt
  * `excerpt_template` => Template für die Details, siehe [excerpt_template](#excerpt_template)
  * `separator` => Trennzeichen zwischen mehreren Werten eines Details
  * `colon` => leerer Wert (`colon=""`) blendet den Doppelpunkt nach dem Namen des Details aus
  * `line_break` => leerer Wert (`line_break=""`) zeigt die Details ohne Zeilenumbruch
  * `personioid` => PersonioID der Stelle, wenn nicht auf der Detailseite einer Stelle verwendet
  * `classes` => zusätzliche CSS-Klassen

### Teil der Beschreibung

`[personio_integration_widget_description_part jobdescription_part="..."]` _(Pro)_

* gibt einen einzelnen Teil der Beschreibung der Stelle der aktuellen Seite aus
* Attribute:
  * `jobdescription_part` => Kennung des Teils, siehe [jobdescription_part](#jobdescription_part)
  * `jobdescription_part_template` => Template, siehe [jobdescription_part_template](#jobdescription_part_template)

### Feld

`[personio_integration_widget_field field="title"]` _(Pro)_

* gibt ein einzelnes Feld der Stelle der aktuellen Seite aus
* Attribute:
  * `field` => _Pflichtfeld_, einer dieser Werte:
    * `personioId` => die PersonioID
    * `title` => der Titel
    * `description` => die Beschreibung

### Gehalt

`[personio_integration_widget_salary personioid="42"]` _(Pro)_

* gibt das Gehalt einer Stelle aus, sofern es in Personio hinterlegt ist
* Attribute:
  * `personioid` => _Pflichtfeld_, PersonioID der Stelle
  * `template` => Template für die Ausgabe, Wert: `default`

### Dateien

`[personio_integration_widget_files]` _(Pro)_

* gibt die Dateien der Stelle der aktuellen Seite aus
* erfordert die aktivierte Erweiterung für Dateien
* hat keine Attribute

### Videos

`[personio_integration_widget_videos]` _(Pro)_

* gibt die Videos der Stelle der aktuellen Seite aus
* erfordert die aktivierte Erweiterung für Videos
* hat keine Attribute

### Ansprechperson

`[personio_integration_widget_contact_person]` _(Pro)_

* gibt die Ansprechperson der Stelle der aktuellen Seite aus
* erfordert die aktivierte Erweiterung für Ansprechpersonen
* Attribute:
  * `template` => Template für die Ausgabe, Wert: `default`
  * `classes` => zusätzliche CSS-Klassen

## Weitere Shortcodes

### Karte

`[personio_integration_widget_map]` _(Pro)_

* gibt eine Karte mit den Standorten der Stellen aus
* erfordert einen eingerichteten Geo-Dienst
* Attribute:
  * `zoom` => Zoomstufe als Zahl zwischen 1 und 20, Standard: 6
  * Einschränkung auf Einträge einer Taxonomie: der Slug der Taxonomie als Name des Attributs, die IDs als Wert,
    kommagetrennt, z.B. `[personio_integration_widget_map department="42"]` (nur mit OpenStreetMap)

### Anzahl der Stellen

`[personio_integration_widget_position_count]` _(Pro)_

* gibt die Anzahl der Stellen aus
* hat keine Attribute

### Anzahl der Eintraege einer Taxonomie

`[personio_integration_widget_term_count taxonomy="office"]` _(Pro)_

* gibt die Anzahl der Einträge einer Taxonomie aus, z.B. die Anzahl der Standorte
* Attribute:
  * `taxonomy` => _Pflichtfeld_, Slug der Taxonomie, siehe [Taxonomien](#taxonomien)

### Allgemeines Bewerbungsformular

`[personio_integration_widget_general_application_form]` _(Pro)_

* gibt das Bewerbungsformular für Bewerbungen aus, die sich nicht auf eine bestimmte Stelle beziehen
* erfordert die aktivierte Erweiterung für Bewerbungen
* hat keine Attribute

### Formular von Gravity Forms

`[personio_integration_gravityform id="12"]` _(Pro)_

* gibt ein Formular von Gravity Forms aus und ordnet die Bewerbungen der Stelle der aktuellen Seite zu
* erfordert das Plugin Gravity Forms
* Attribute:
  * `id` => _Pflichtfeld_, ID des Formulars in Gravity Forms

## Taxonomien

Die folgenden Slugs können in den Attributen `filter`, `excerpt` und `groupby`, als Name eines Attributs zur
Einschränkung von Listen und im Attribut `taxonomy` von `[personio_integration_widget_term_count]` verwendet werden:

| Slug                 | Bedeutung                       | Hinweis                                          |
|----------------------|---------------------------------|--------------------------------------------------|
| `recruitingCategory` | Kategorien                      |                                                  |
| `occupation`         | Jobtypen                        |                                                  |
| `occupation_detail`  | Details zu Jobtypen             |                                                  |
| `office`             | Hauptstandorte                  |                                                  |
| `department`         | Abteilungen                     |                                                  |
| `employmenttype`     | Beschäftigungsarten             |                                                  |
| `seniority`          | Erfahrungsstufen                |                                                  |
| `schedule`           | Vertragsarten                   |                                                  |
| `experience`         | Berufserfahrung in Jahren       |                                                  |
| `keyword`            | Schlagworte                     |                                                  |
| `language`           | Sprachen                        | nicht als Filter nutzbar                         |
| `subcompany`         | Gesellschaften                  | _(Pro)_                                          |
| `additionaloffices`  | Weitere Standorte               | _(Pro)_                                          |
| `pi_places`          | Orte                            | _(Pro)_, Haupt- und weitere Standorte, nur als Filter nutzbar |

## Beispiele

### Liste ohne Filter, mit Titel & Beschreibung pro Stelle

`[personioPositions templates="title,content"]`

### Liste mit Filter, Titel & Details pro Stelle

`[personioPositions showfilter="1" filter="recruitingCategory,schedule" templates="title,excerpt"]`

### Liste der Stellen einer Abteilung, gruppiert nach Standort

`[personioPositions department="42" groupby="office" templates="title,excerpt" excerpt="employmenttype"]`

### Liste mit kombiniertem Filter ohne Neuladen _(Pro)_

`[personioPositions listing_template="tile" showfilter="1" filter="recruitingCategory,pi_places" filtertype="select" without_reload="1" filtercombine="AND" templates="title,excerpt"]`

### Einzelansicht einer Stelle mit Titel, Beschreibung & Bewerbungslink

`[personioPosition personioid="42" templates="title,content,formular"]`
