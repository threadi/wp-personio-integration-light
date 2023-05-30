# Templates

Der folgende Text beschreibt wie man Templates dieses Plugins wie auch von deren Pro-Version überladen kann um Anpassungen am erzeugten HTML-Code vorzunehmen.

## Voraussetzungen

* ein Child-Theme, siehe: https://developer.wordpress.org/themes/advanced-topics/child-themes/
* Kenntnisse in HTML und zumindest PHP-Grundlagen-Wissen

## Vorgehen

1. Erstellen Sie innerhalb des Child-Theme-Verzeichnisses ein neues Unterverzeichnis mit dem Namen _personio-integration-light_ (auf exakte Schreibweise achten).
2. Im Plugin-Verzeichnis unter /wp-content/plugins/personio-integration-light/ gibt es ein Verzeichnis "templates". Alle Dateien in diesem Verzeichnis können in das in 1. erstellte Verzeichnis kopiert werden um sie dort anzupassen.
3. Kopieren Sie eine der Template-Dateien in Ihr in 1. erstelltes Verzeichnis.
4. Passen Sie in der kopierten Datei den Quellcode wie gewünscht an.

## Verzeichnisstruktur

Im Ergebnis sollte die Struktur bspw. so aussehen:

`/wp-content/themes/your-child-theme/personio-integration-light/`

Und darin sollten die Template-Dateien liegen, z.B. _single-personioposition-shortcode.php_. 