<?php
/**
 * HTML-Template for any email this plugin generated.
 *
 * @param string $subject The email subject.
 * @param string $body The email body.
 *
 * @package personio-integration-light
 * @version : 5.0.0
 */
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo esc_html( $subject ); ?></title>
	<style>
	* {
		color: #000000;
		font-family: Arial, sans-serif;
		font-size: 16px;
		line-height: 1.4;
	}
	#wrapper {
		margin: 0 auto;
		width: 480px;
	}
	#signature * {
		color: #999;
	}
	tr { text-align: left }
	</style>
</head>
<body size="16" text="#000000">
	<div id="wrapper" width="480">
		<h1><?php echo esc_html( $subject ); ?></h1>
		<?php echo wp_kses_post( $body ); ?>
	</div>
</body>
</html>
