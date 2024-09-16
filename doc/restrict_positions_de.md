# Einschränken der Ausgabe von Stellen im Frontend

## Methoden

Es gibt mehrere Wege um die Stellen im Frontend einzuschränken.

### Methode 1

Diese Konfiguration wirkt sich global auf alle Listen und Detail-Darstellungen von Stellen aus.

1. Im WordPress-Backend unter Stellen > Einstellungen > Templates das Häkchen "Beschränken auf Taxonomie" setzen.
2. Danach darunter die gewünschten Einschränkungen auswählen.
3. Speichern, fertig.

### Methode 2

Je nach eingesetztem PageBuilder liefert das Personio Integration Plugin auch hier Möglichkeiten zur Einschränkung der
Liste mit.

### Methode 3

Mit individuellem Code kann man ebenfalls die Liste beschränken.

Vorteil: hierüber kann man global auch auf mehrere Eigenschaften beschränken.

Beispiel:
`add_filter( 'personio_integration_pro_restrict_taxonomy', function( $tax_query ) {
    $tax_query[] = array(
        'taxonomy' => 'personioSubcompany',
        'field'    => 'term_id',
        'terms'    => array( 8163, 8143 ),
    );
    return $tax_query;
});`

Dieser muss in der functions.php des Child-Themes oder per Code Snippet Plugin im Projekt hinterlegt werden.

## FAQ

### Wieso kann man nur per Code auf mehrere Eigenschaften beschränken?

Nach bisherigen Rückmeldungen ist das ein Edge Case, der sehr selten benötigt wird. Daher haben wir die Oberfläche
im Backend nicht mit zusätzlichen Möglichkeiten belasten wollen.
