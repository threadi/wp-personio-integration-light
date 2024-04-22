# Werbeanzeigen verstecken

## Voraussetzungen

* installiertes und aktiviertes Plugin _Personio Integration Light_

Um Werbung für die Pro-Version oder das Bewerten des Plugins zu verstecken, kann man folgenden Code verwenden:

```
add_filter( 'personio_integration_hide_pro_hints', '__return_true' );
```
