# Templates

The following text describes how to overload templates of this plugin as well as of its Pro version to make adjustments to the generated HTML code.

## Requirements

* a child theme, see: https://developer.wordpress.org/themes/advanced-topics/child-themes/
* knowledge in HTML and at least basic PHP knowledge

## Procedure

1. Within the child theme directory create a new subdirectory named _personio-integration-light_ (pay attention to exact spelling).
2. In the plugin directory under /wp-content/plugins/personio-integration-light/ there is a directory "templates". All files in this directory can be copied to the directory created in 1. to customize them there.
3. Copy one of the template files into the directory you created in step 1.
4. Adjust the source code in the copied file as desired.

## Directory structure

As a result, the structure should look like this, for example:

`/wp-content/themes/your-child-theme/personio-integration-light/`.

And in it should be the template files, for example _single-personioposition-shortcode.php_.