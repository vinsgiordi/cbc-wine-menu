<?php
/**
 * Expired or missing session template.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html__( 'Session expired', 'cbc-wine-menu' ); ?></title>
	<style>
		body {
			margin: 0;
			min-height: 100vh;
			display: grid;
			place-items: center;
			font-family: system-ui, sans-serif;
			background: #f7f3ee;
			color: #1f1a17;
			padding: 1.5rem;
		}
		.card {
			max-width: 28rem;
			text-align: center;
		}
		h1 { font-size: 1.5rem; margin: 0 0 0.75rem; }
		p { color: #6b5e57; line-height: 1.5; }
	</style>
</head>
<body>
	<main class="card">
		<h1><?php echo esc_html__( 'Your session has expired.', 'cbc-wine-menu' ); ?></h1>
		<p>
			<?php echo esc_html__( 'Please scan the QR code at your table again to access the wine menu.', 'cbc-wine-menu' ); ?>
		</p>
	</main>
</body>
</html>
