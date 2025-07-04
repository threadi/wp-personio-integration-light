<?php
/**
 * Template for output of a list of positions as archive of our custom post type.
 *
 * @version: 5.0.0
 * @package personio-integration-light
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Themes;

// get the description.
$description = get_the_archive_description();

get_header();

?>
	<div class="<?php echo esc_attr( Themes::get_instance()->get_theme_wrapper_classes() ); ?>">
	<header class="site-main page-header alignwide">
		<?php the_archive_title( '<h1 class="page-title site-container">', '</h1>' ); ?>
		<?php if ( $description ) : ?>
			<div class="archive-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
		<?php endif; ?>
	</header>
	<?php
		echo wp_kses_post( \PersonioIntegrationLight\PersonioIntegration\Widgets\Archive::get_instance()->render( array() ) );
	?>
</div>
<?php

get_footer();
