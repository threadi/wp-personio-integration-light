## Verwendung der WP CLI

# Überblick

Die WP Cli führt Wordpress-Kommandos an der Konsole aus. Diese sollte nur mit entsprechendem Vorwissen ausgeführt werden. Das Plugin stellt eine ganze Reihe an Kommandos für die schnelle Bearbeitung von offenen Stellen in der Datenbank bereit.

# Main command

Liste der verfügbaren Befehle für dieses Plugin anzeigen:

`wp personio`

# Kommandos

`wp personio delete_all`
=> alle aktuell importierten Stellen und alle Werte in den Taxonomien

`wp personio delete_positions`
=> alle aktuell importierten Stellen löschen
=> zusätzlich importierte Daten wie Taxonomien bleiben erhalten

`wp personio get_positions`
=> aktuelle Stellen von Personio holen
=> erfordert gültige PersonioURL in den Einstellungen
=> kann verwendet werden, um Stellen über einen System-Cronjob zu importieren

`wp personio reset_plugin`
=> setzt das Plugin komplett zurück
=> löscht alle Daten
=> initiiert das Plugin, als ob es neu installiert worden wäre

# weitere Kommandos im Pro-Plugin

`wp personio delete_partials`
`wp personio run_partial_import`

# Hint

Abhängig von Ihrem Hosting-System müssen diese Befehle im Benutzerkontext Ihrer Website ausgeführt werden.
