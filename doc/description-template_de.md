# Templates für Stellenbeschreibungen

Die Ausgabe des Texts der Stellenbeschreibung kann mittels eigener Templates individuell angepasst werden. Standardmäßig werden deren maximal 3 Text-Bestandteile durch Personio Integration mit Überschriften untereinander ausgegeben. Das kann wie im folgenden Beschrieben angepasst werden.

## Voraussetzungen

* Personio Integration Light in Version 2.5.5 oder neuer
* minimale PHP-Kenntnisse
* ein Child-Theme

## Vorgehen

1. Erstellen Sie innerhalb des Child-Theme-Verzeichnisses ein neues Unterverzeichnis mit dem Namen _personio-integration-light_ (auf exakte Schreibweise achten).
2. Im Plugin-Verzeichnis unter /wp-content/plugins/personio-integration-light/ gibt es ein Verzeichnis "templates", darin wiederum "parts/jobdescription". Kopieren Sie die Datei default.php von dort an die gleiche Stelle in Ihrem Child-Theme und geben sie ihr einen anderen Namen, z.B. "tab_navigation.php".
   Beispiel: /wp-content/themes/your-child-theme/personio-integration-light/parts/jobdescription/tab_navigation.php
3. Ergänzen Sie in der functions.php Ihres Child-Themes folgendes:

```add_filter( 'personio_integration_rest_templates_jobdescription', function( $templates ) {
 $templates[] = array(
  'id' => count($templates),
  'label' => 'Tab Navigation',
  'value' => 'tab_navigation',
 ),
 return $templates;
});

add_filter( 'personio_integration_templates_jobdescription', function( $templates ) {
 $templates['tab_navigaton'] = 'Tab Navigation';
 return $templates;
});
```

4. Gehen Sie im Backend unter Stellen > Einstellungen > Templates und wählen Sie dort Ihr neues Template für die Ausgabe der Stellendetails aus.

## Hinweise

Wenn Sie einen PageBuilder wie den Block Editor oder Elementor nutzen, müssen Sie Schritt 4 in deren Template für die Single-Ansicht vornehmen.
