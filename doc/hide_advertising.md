# Hide advertising

## Requirements

* installed and activated plugin _Personio Integration Light_

## Add snippet

To hide advertising for the Pro version or the rating of the plugin, you can use the following code:

```
add_filter( 'personio_integration_hide_pro_hints', '__return_true' );
```
