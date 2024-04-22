<?php
/**
 * Template for output of a list of positions as archive of our custom post type.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Themes;

defined( 'ABSPATH' ) || exit;

get_header();

?><div class="<?php echo esc_attr( Themes::get_instance()->get_theme_wrapper_classes() ); ?>">
						<?php

							$description = get_the_archive_description();

						?>
<header class="site-main page-header alignwide">
	<?php the_archive_title( '<h1 class="page-title site-container">', '</h1>' ); ?>
	<?php if ( $description ) : ?>
		<div class="archive-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
	<?php endif; ?>
	</header>
<?php

// use shortcode-functions to display the list.
echo wp_kses_post( PersonioPosition::get_instance()->shortcode_archive() );

?>
</div>
<?php

get_footer();
