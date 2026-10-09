<?php
/**
 * Template for output the content of a single position.
 *
 * @param array     $personio_attributes List of settings.
 * @param Position $position       The position as an object.
 *
 * @package personio-integration-light
 * @version: 6.0.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

/**
 * Filter the list of allowed template parts which could be used via the "templates" attribute.
 *
 * @since 6.0.0 Available since 6.0.0.
 * @param array<int,string> $allowed_template_parts List of allowed template parts.
 */
$personio_integration_allowed_template_parts = (array) apply_filters( 'personio_integration_light_allowed_template_parts', array_keys( \PersonioIntegrationLight\Plugin\Templates::get_instance()->get_template_labels() ) );

?>
	<article id="post-<?php echo absint( $position->get_id() ); ?>" class="site-main entry inside-article container site-content site-container content-bg content-area ht-container <?php echo esc_attr( apply_filters( 'personio_integration_light_position_get_classes', $position ) ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Position', 'personio-integration-light' ); ?>">
		<?php
		foreach ( $personio_attributes['templates'] as $personio_integration_template ) {
			// bail if this template part is not allowed.
			if ( ! is_string( $personio_integration_template ) || ! in_array( $personio_integration_template, $personio_integration_allowed_template_parts, true ) ) {
				continue;
			}
			do_action( 'personio_integration_get_' . $personio_integration_template, $position, $personio_attributes );
		}
		?>
	</article>
<?php
