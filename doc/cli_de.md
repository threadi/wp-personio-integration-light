## Verwendung der WP CLI

# Überblick

Die WP Cli führt Wordpress-Kommandos an der Konsole aus. Diese sollte nur mit entsprechendem Vorwissen ausgeführt werden. Das Plugin stellt eine ganze Reihe an Kommandos für die schnelle Bearbeitung von offenen Stellen in der Datenbank bereit.

# Main command

Liste der verfügbaren Befehle für dieses Plugin anzeigen:

`wp personio`

# Kommandos

`wp personio deleteAll`
=> alle aktuell importierten Daten (Stellen und alle Taxonomien) löschen

`wp personio deletePositions`
=> alle aktuell importierten Stellen löschen
=> zusätzliche Importdaten wie Taxonomien bleiben erhalten

`wp personio getPositions`
=> aktuelle Stellen von Personio holen
=> erfordert gültige PersonioURL in den Einstellungen
=> kann verwendet werden, um Stellen über einen System-Cronjob zu importieren

`wp personio resetPlugin`
=> setzt das Plugin komplett zurück
=> löscht alle Daten
=> initiiert das Plugin, als ob es neu installiert worden wäre

# weitere Kommandos in der Pro-Version

`wp personio deletePartials`
`wp personio removeAllDataPro`
`wp personio resetPluginPro`
`wp personio runPartialImport`

# Hint

Abhängig von Ihrem Hosting-System müssen diese Befehle im Benutzerkontext Ihrer Website ausgeführt werden.