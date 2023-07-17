# Bewerbungsformulare

## Hinweis

Für den schnellen Einstieg bitte [Quickstart](quickstart_de.md) beachten. Im Dokument hier findet sich die ausführliche Beschreibung zum Umgang mit Bewerbungen in diesem Plugin.

## Voraussetzungen

* Die beiden Plugins Personio Integration Light und Personio Integration müssen installiert und aktiviert sein.
* Es muss eine gültige Lizenz für die Pro-Version hinterlegt sein.
* Es muss eine Personio URL angegeben und offene Stellen sollten importiert sein.

## Ablauf einer Bewerbung

1. Interessent findet auf der Webseite eine Stelle, für die er sich bewerben will.
2. Interessent füllt das Bewerbungsformular aus und schickt es ab.
3. Das Plugin speichert die Bewerbung in der lokalen Wordpress-Datenbank
   * Hochgeladene Unterlagen (Dateien) werden in einem nicht öffentlich erreichbaren Verzeichnis im Wordpress-Hosting gespeichert.
4. Das Plugin übermittelt die neu eingegangene Bewerbung in einem konfigurierten Interval an Personio.
5. Sobald die Personio API dazu die Rückmeldung gibt, dass die Bewerbung dort erfolgreich gespeichert wurde, löscht das Plugin die Bewerbung aus der lokalen Wordpress-Datenbank.
   * Dieses Verhalten ist konfigurierbar.
   * Mit dem Löschen der Daten werden auch dazu hochgeladene Dokumente im Wordpress-Hosting gelöscht.
6. Alles weitere zur Bewerbung wird innerhalb von Personio geregelt.
    
## Hinweise

In der Standard-Einstellung werden Bewerbungsdaten nur so lange in Wordpress gespeichert wie unbedingt notwendig. Sobald eine Bewerbung erfolgreich übermittelt wurde, werden ihre Daten aus der lokalen Datenbank entfernt. Das betrifft ebenfalls hochgeladene Dokumente.

## Konfiguration

### Obligatorische Einstellungen

Im Wordpress-Backend unter Stellen > Einstellungen > Export-Einstellungen müssen folgende Felder ausgefüllt werden:

* _Deine Company-ID_
* _Access Token_

Beide Angaben sind notwendig um Bewerbungen an Personio zu übermitteln. Die Angaben zu beiden finden Sie in Ihrem Personio-Konto unter Einstellungen > Integrationen > API Credentials.

Im Wordpress-Backend unter Stellen > Einstellungen > Bewerbungen muss bei einem der Felder "_Bewerbung in Listen-Ansicht_" bzw. "_Bewerbung in Detail-Ansicht_" der Wert "_Bewerbungsformular anzeigen_" ausgewählt werden.

### Optionales

Im Wordpress-Backend unter Stellen > Einstellungen > Export-Einstellungen gibt es folgende weitere Felder:

* Recruiting Channel ID (optional)
  * Nach Angabe dieser ID können Sie die eingehende Bewerbung in Personio einem Kanal Ihrer Wahl zuweisen. Die einzufügende ID finden Sie in Ihrem Personio Account unter Einstellungen > Channels unter dem Namen „Recruiting API Channel ID“.
* Recruiting phase (optional)
  * Definiert die Phase, der eine neue Anwendung anfänglich zugeordnet wird. Die möglichen Phasen finden Sie in Ihrem Personio-Account unter Einstellungen > Phasen. Dort müssen Sie hier den Eintrag aus dem Feld API ID eingeben.
* Automatischen Export aktivieren
  * Wenn diese Option aktiviert ist, werden neue Bewerbungen in dem unten angegebenen Intervall übertragen.
  * Wenn sie deaktiviert ist, werden keinerlei Bewerbungen an Personio übertragen.
* Intervall für den Export festlegen
  * Legt das Zeitintervall fest, in dem neue Anträge an Personio übertragen werden.
  * Vorauswahl ist "Einmal stündlich".
* Löschung exportierter Bewerbungsdaten
  * Legt fest, wie lange Bewerbungsdaten in deinem Wordpress-Hosting gespeichert werden.
  * Vorauswahl ist "Unmittelbar nach erfolgreicher Übertragung".

Im Wordpress-Backend unter Stellen > Einstellungen > Bewerbungen gibt es folgende weitere Felder:

* _Aktivieren um den Link in einem neuen Fenster zu öffnen_
  * ist nur relevant bei Verwendung der Links zu Personio, nicht bei Verwendung des Bewerbungsformulars
* _Wähle einen Formular-Generator_
  * Auswahl des für das Bewerbungsformular zu verwendenden Formular-Generators.
  * Stand Januar 2023 liefert das Plugin lediglich seinen eigenen Generator mit (siehe [hier](personioformulare_de.md)), weshalb das Feld ausgegraut ist.

## Export von Bewerbungen an Personio

Es gibt 4 Wege, um Bewerbungen an Personio zu übermitteln.

### Automatischer Export

Wenn konfiguriert, läuft der automatische Export im hinterlegten Interval.

### Manueller Export aller Bewerbungen

Unter Stellen > Bewerbungen kann man oben auf den Button "jetzt exportieren" klicken. Dadurch werden alle aktuell zu übertragenden Bewerbungen in diesem Moment übermittelt.

### Manueller Export einer einzelnen Bewerbung

Unter Stellen > Bewerbungen kann man bei jeder Bewerbung die keinen Error-Status hat auf "jetzt zu Personio übermitteln" klicken. Sie wird in genau diesem Moment übertragen.

### per WP-CLI

An der Konsole gibt es für die Übertragung aller Bewerbungen dieses Kommando:

`wp personio sendApplications`

## Fehlermeldungen beim Export an Personio

Treten beim Export Fehlermeldungen auf, sind diese im Log unter Stellen > Einstellungen > Logs zu finden. Die Meldungen entsprechen 1:1 der Rückgabe der Personio API. Diese sind hier dokumentiert:
https://developer.personio.de/reference/post_v1-recruiting-applications