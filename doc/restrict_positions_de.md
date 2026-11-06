# Einschränken der Ausgabe von Stellen im Frontend

## Methoden

Es gibt mehrere Wege um die Stellen im Frontend einzuschränken.

### Methode 1

Diese Konfiguration wirkt sich global auf alle Listen und Detail-Darstellungen von Stellen aus.

1. Im WordPress-Backend unter Stellen > Einstellungen > Templates das Häkchen "Beschränken auf Taxonomie" setzen.
2. Danach darunter die gewünschten Einschränkungen auswählen. Pro Taxonomie können ein oder mehrere Einträge gewählt
   werden. Angezeigt werden dann Stellen, die mindestens einem der gewählten Einträge zugeordnet sind. Mehrere
   Taxonomien wirken zusammen: eine Stelle muss dann zu jeder eingeschränkten Taxonomie passen.
3. Speichern, fertig.

### Methode 2

Je nach eingesetztem PageBuilder liefert das Personio Integration Plugin auch hier Möglichkeiten zur Einschränkung der
Liste mit. Auch hier können pro Taxonomie ein oder mehrere Einträge gewählt werden (Ausnahme: das klassische Divi 4
erlaubt nur einen Eintrag pro Taxonomie).

Im Shortcode werden mehrere Einträge durch Komma getrennt angegeben, z.B.:
`[personioPositions office="12,34"]`

Ein Filter im Frontend für dieselbe Taxonomie ersetzt die Einschränkung dieser Taxonomie, solange der Besucher
filtert.

### Methode 3

Mit individuellem Code kann man ebenfalls die Liste beschränken.

Vorteil: hierüber kann man beliebige eigene Bedingungen ergänzen.

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

### Was passiert mit bestehenden Einschränkungen nach dem Update?

Sie bleiben erhalten. Einschränkungen auf einen einzelnen Eintrag werden automatisch als Liste mit diesem einen Eintrag
übernommen.
