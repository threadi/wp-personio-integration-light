# Admin notes

In the WordPress backend, you can use admin notes to display hints and messages. The “Personio Integration Light” plugin
uses these to alert users to problems or necessary actions.

We are aware that admin notes can be disruptive—especially when there are a lot of them, they can clutter up the backend. For this reason, we use a method to display our own
notes only as a group. This is achieved with the Composer package "Easy Transients for WordPress". Information on this can be found [here](https://github.com/threadi/easy-transients-for-wordpress).

## Hide notes

It is possible to hide all or individual admin notes from the plugin. This can be done in the following way.

### By clicking on "Hide"

Many admin notes have a "Hide" button in the upper right corner. This hides the notepad and prevents it from being
displayed for a limited period of time. The duration depends on the subject of the note and ranges from a few days
to a year.

### Hide individual notes with PHP code

The following code can be used to hide a single note from the plugin:

```
add_filter( ‘etfw_pi_transients’, function( array $transients ) {
 if( isset( $transients[‘pi’][‘personio_integration_limit_hint’] ) ) {
  unset( $transients[‘pi’][‘personio_integration_limit_hint’] );
 }
 return $transients;
} );
```

In this example, the note about the light version's limit of 10 positions per list is hidden. This note is named
"personio_integration_limit_hint". To hide other notes from the plugin, you need to find out their names. You can
find these in the HTML code when the notes are displayed.

### Hide all notes with PHP code

It is also possible to hide all notes from the plugin. **Please note that you will then no longer see any OK messages
or error messages.**

```
add_filter( ‘etfw_pi_transients’, function( array $transients ) {
 if( isset( $transients[‘pi’] ) ) {
   unset( $transients[‘pi’] );
 }
 return $transients;} );
 ```
