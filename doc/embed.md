# Embed Personio positions

## Scenario

You want to embed positions from your own WordPress-website A in another WordPress-website B.

## Solution

Add the following code snippet in website B (where not our plugin runs) e.g. via functions.php or Code Snippet plugin:

```
function custom_add_personio_integration() {
    wp_oembed_add_provider(
        'https://example.com/*',
        'https://example.com/wp-json/oembed/1.0/embed'
    );
}
add_action( 'init', 'custom_add_personio_integration' );
```

Adjust the URLs in this code snippet to the URLs of your website A.

## Alternatives

* Use WordPress-plugins like [oEmbed Manager](https://wordpress.org/plugins/oembed-manager/)
