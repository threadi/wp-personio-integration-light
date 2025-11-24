# Admin-Notizen

Im WordPress Backend kann man Admin-Notizen verwenden, um Hinweise und Meldungen anzuzeigen. Das Plugin "Personio Integration Light"
verwendet diese, um den Nutzer auf Probleme oder notwendige Aktionen hinzuweisen.

Uns ist bewusst, dass die Admin-Notizen teilweise störend sind – gerade in großen Mengen verstopfen sie das Backend.
Aus dem Grund verwenden wir eine Methode, um unsere eigenen Notizen nur als Gruppe anzuzeigen. Dies wird mit dem Composer
Package "Easy Transients for WordPress" realisiert. Informationen dazu findet man [hier](https://github.com/threadi/easy-transients-for-wordpress).

## Notizen ausblenden

Es ist möglich alle oder einzelne Admin Notizen des Plugins auszublenden. Das geht auf folgenden Wegen.

### Durch Klick auf "Ausblenden"

Viele Admin-Notizen haben oben rechts einen Knopf "Ausblenden". Dieser klickt den Notizblock aus und wird anschließend
für eine begrenzte Zeit nicht mehr angezeigt. Die Zeit richtet sich nach dem Thema der Notiz und beträgt zwischen
wenigen Tagen bis zu einem Jahr.

### Einzelne Notiz per PHP-Code ausblenden

Mit folgendem Code kann eine einzelne Notiz des Plugins ausgeblendet werden:

```
add_filter( 'etfw_pi_transients', function( array $transients ) {
 if( isset( $transients['pi']['personio_integration_limit_hint'] ) ) {
  unset( $transients['pi']['personio_integration_limit_hint'] );
 }
 return $transients;
} );
```

In dem Beispiel wird der Hinweis auf die Limitierung der Light-Version auf max. 10 Stellen pro Liste ausgeblendet. Diese
hat den Namen "personio_integration_limit_hint".

Um andere Notizen des Plugins auszublenden, muss man deren Namen ermitteln. Diese stehen im HTML-Code bei der Anzeige
der Notizen.

### Alle Notizen per PHP-Code ausblenden

Es ist auch möglich alle Notizen des Plugins auszublenden. **Beachte dabei, dass du dann auch keinerlei OK-Meldungen
oder Fehlermeldungen mehr sehen wirst.**

```
add_filter( 'etfw_pi_transients', function( array $transients ) {
 if( isset( $transients['pi'] ) ) {
  unset( $transients['pi'] );
 }
 return $transients;
} );
```
