# Restricting the output of positions in the frontend

## Methods

There are several ways to restrict the positions in the frontend.

### Method 1

This configuration has a global effect on all lists and detailed representations of positions.

1. In the WordPress backend under Positions > Settings > Templates, tick the box "Restrict to taxonomy".
2. Then select the desired restrictions below.
3. Save, done.

### Method 2

Depending on the PageBuilder used, the Personio Integration Plugin also provides options for restricting the list here.

### Method 3

You can also restrict the list with individual code.

Advantage: this also allows you to restrict globally to several properties.

Example:
`add_filter( 'personio_integration_pro_restrict_taxonomy', function( $tax_query ) {
    $tax_query[] = array(
        'taxonomy' => 'personioSubcompany',
        'field' => 'term_id',
        'terms' => array( 8163, 8143 ),
    );
    return $tax_query;
});`
This must be stored in the functions.php of the child theme or via a code snippet plugin in the project.

## FAQ

### Why can you only restrict to several properties using code?

According to previous feedback, this is an edge case that is very rarely needed. Therefore, we did not want to
burden the interface in the backend with additional options.
