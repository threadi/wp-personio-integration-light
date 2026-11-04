# Restricting the output of positions in the frontend

## Methods

There are several ways to restrict the positions in the frontend.

### Method 1

This configuration has a global effect on all lists and detailed representations of positions.

1. In the WordPress backend under Positions > Settings > Templates, tick the box "Restrict to taxonomy".
2. Then select the desired restrictions below. You can choose one or more entries per taxonomy. Positions which are
   assigned to at least one of the chosen entries will be shown. Multiple taxonomies are combined: a position must match
   each restricted taxonomy.
3. Save, done.

### Method 2

Depending on the PageBuilder used, the Personio Integration Plugin also provides options for restricting the list here.
Here you can also choose one or more entries per taxonomy (exception: the classic Divi 4 allows only one entry per
taxonomy).

In the shortcode, separate multiple entries by comma, e.g.:
`[personioPositions office="12,34"]`

A filter in the frontend for the same taxonomy replaces the restriction of this taxonomy while the visitor filters.

### Method 3

You can also restrict the list with individual code.

Advantage: this allows you to add any custom conditions.

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

### What happens to existing restrictions after the update?

They are kept. Restrictions to a single entry are taken over automatically as a list with this one entry.
