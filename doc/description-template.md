# Templates for job descriptions

The output of the job description text can be customised using your own templates. By default, a maximum of 3 text components are output by Personio Integration with headings below each other. This can be customised as described below.

## Requirements

* Personio Integration Light in version 2.5.5 or newer
* minimal PHP knowledge
* a child theme

## Procedure

1. Create a new subdirectory within the child theme directory with the name _personio-integration-light_ (pay attention to the exact spelling).
2. In the plugin directory under /wp-content/plugins/personio-integration-light/ there is a directory "templates", which in turn contains "parts/jobdescription". Copy the default.php file from there to the same location in your child theme and give it a different name, e.g. "tab_navigation.php".
   Example: /wp-content/themes/your-child-theme/personio-integration-light/parts/jobdescription/tab_navigation.php
3. Add the following code to the functions.php of your child theme:

```add_filter( 'personio_integration_rest_templates_jobdescription', function( $templates ) {
 $templates[] = array(
  'id' => count($templates),
  'label' => 'Tab Navigation',
  'value' => 'tab_navigation',
 ),
 return $templates;
});

add_filter( 'personio_integration_templates_jobdescription', function( $templates ) {
 $templates['tab_navigaton'] = 'Tab Navigation';
 return $templates;
});
```
4. Go to Jobs > Settings > Templates in the backend and select your new template for the output of the job details

## Notes

If you are using a PageBuilder such as the Block Editor or Elementor, you must carry out step 4 in their template for the single view.
