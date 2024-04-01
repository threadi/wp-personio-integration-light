# Personio Stellen einbetten

## Voraussetzungen

* installiertes und aktiviertes Plugin _Personio Integration Light_
* installiertes und aktiviertes Plugin _Personio Integration Pro_ inklusive gültiger Lizenz
* die Pro-Erweiterung oEmbed aktivieren und in den Einstellungen freischalten

## Szenario

Sie möchten Stellen von Ihrer eigenen WordPress-Webseite A in eine andere WordPress-Webseite B einbetten.

## Lösung

Fügen Sie den folgenden Codeschnipsel in Website B ein (wo nicht unser Plugin läuft), z.B. über functions.php oder Code Snippet Plugin:

```
function custom_add_personio_integration() {
    wp_oembed_add_provider(
        'https://example.com/*',
        'https://example.com/wp-json/oembed/1.0/embed'
    );
}
add_action( 'init', 'custom_add_personio_integration' );
```
Passen Sie die URLs in diesem Codeschnipsel an die URLs Ihrer Website A an.

## Alternativen

* Verwenden Sie WordPress-Plugins wie [oEmbed Manager](https://wordpress.org/plugins/oembed-manager/)
