# Individuelle Erweiterung erstellen

## Zielsetzung

Diese Dokumentation beschreibt wie man selbst eine Erweiterung für Personio Integration entwickeln kann. Dadurch kann
man die Möglichkeiten, die das Plugin hinsichtlich Daten von Stellen bietet, um individuelle Ergänzungen erweitern.

Bei dieser Erweiterung geht es nicht darum Stile oder das Verhalten des Plugins zu beeinflussen. Für letztes kann man
auf die [Hooks](hooks.md) zurückgreifen.

## Warum eine Erweiterung

Erweiterungen haben den Vorteil, dass der Redakteur sie jederzeit selbst aktivieren und deaktivieren kann. Zudem stehen
innerhalb von Erweiterungen alle Funktionen des Personio Integration Light Plugins zur Verfügung.

### Hinweis zum Pro Plugin

Natürlich kann man, wenn das Pro Plugin vorhanden und lizensiert ist, auch Funktionen von diesem in der eigenen
Erweiterung ansprechen.

## Voraussetzungen

* Kenntnisse in PHP notwendig
* Kenntnisse beim Schreiben von WordPress-Plugins empfehlenswert

## Vorbereitungen

* Prüfe, ob deine Anforderung nicht bereits durch das Plugin oder die Erweiterungen anderer erfüllt wird
* Prüfe, ob deine Anforderung sich auf Stellen von Personio bezieht

## Vorgehen

1. Erstelle zunächst ein eigenes WordPress-Plugin, siehe dazu: https://developer.wordpress.org/plugins/
2. Erstelle darin eine Datei deren Klasse als Object für deine Erweiterung dient. Eine Vorlage dazu findest Du hier:
3. Ergänze per Hook 'personio_integration_extend_position_object' deine Extension zur Liste der verfügbaren Erweiterungen.

Nach diesen Schritten ist deine Erweiterung in der Liste unter Stellen > Erweiterungen zu sehen.

## Funktionalität abbilden

Innerhalb deiner eigenen Klasse fügst du in der init()-Funktion deine individuellen Ergänzungen hinzu. Verwende dazu am
besten Hooks von Personio Integration Light oder WordPress-eigene Hooks.
