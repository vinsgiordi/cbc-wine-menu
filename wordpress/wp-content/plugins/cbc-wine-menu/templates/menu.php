<?php
/**
 * Minimal protected wine menu template.
 *
 * @package CBCWineMenu
 * @var array<string, mixed> $session
 * @var array<int, \WP_Post> $wines
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$table_id = isset( $session['table_id'] ) ? (string) $session['table_id'] : '';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html__( 'Wine menu', 'cbc-wine-menu' ); ?></title>
	<style>
		:root {
			--cbc-bg: #f7f3ee;
			--cbc-text: #1f1a17;
			--cbc-muted: #6b5e57;
			--cbc-line: #e4ddd5;
			--cbc-accent: #7a1f2b;
		}
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: Georgia, "Times New Roman", serif;
			background: linear-gradient(180deg, #f7f3ee 0%, #efe7de 100%);
			color: var(--cbc-text);
			line-height: 1.45;
		}
		.wrap {
			max-width: 40rem;
			margin: 0 auto;
			padding: 1.25rem 1rem 2.5rem;
		}
		.eyebrow {
			font-family: system-ui, sans-serif;
			font-size: 0.75rem;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: var(--cbc-muted);
			margin: 0 0 0.35rem;
		}
		h1 {
			margin: 0 0 0.5rem;
			font-size: 1.75rem;
			font-weight: 700;
		}
		.meta {
			font-family: system-ui, sans-serif;
			font-size: 0.875rem;
			color: var(--cbc-muted);
			margin-bottom: 1.5rem;
		}
		.wine {
			padding: 1rem 0;
			border-top: 1px solid var(--cbc-line);
		}
		.wine h2 {
			margin: 0 0 0.25rem;
			font-size: 1.15rem;
		}
		.wine .details {
			font-family: system-ui, sans-serif;
			font-size: 0.875rem;
			color: var(--cbc-muted);
		}
		.wine .prices {
			margin-top: 0.4rem;
			font-family: system-ui, sans-serif;
			font-size: 0.9rem;
		}
		.unavailable {
			opacity: 0.55;
		}
		.badge {
			display: inline-block;
			margin-left: 0.4rem;
			padding: 0.1rem 0.4rem;
			border: 1px solid var(--cbc-line);
			border-radius: 0.25rem;
			font-family: system-ui, sans-serif;
			font-size: 0.7rem;
			text-transform: uppercase;
			letter-spacing: 0.04em;
			color: var(--cbc-accent);
		}
		.empty {
			font-family: system-ui, sans-serif;
			color: var(--cbc-muted);
		}
	</style>
</head>
<body>
	<main class="wrap">
		<p class="eyebrow"><?php echo esc_html__( 'CB Cannavale', 'cbc-wine-menu' ); ?></p>
		<h1><?php echo esc_html__( 'Wine menu', 'cbc-wine-menu' ); ?></h1>
		<p class="meta">
			<?php
			printf(
				/* translators: %s: table number */
				esc_html__( 'Table %s', 'cbc-wine-menu' ),
				esc_html( $table_id )
			);
			?>
		</p>

		<?php if ( empty( $wines ) ) : ?>
			<p class="empty"><?php echo esc_html__( 'No wines available yet.', 'cbc-wine-menu' ); ?></p>
		<?php else : ?>
			<?php foreach ( $wines as $wine ) : ?>
				<?php
				$available = get_post_meta( $wine->ID, \CBCWineMenu\Admin\WineMetaBox::meta_key( 'is_available' ), true );
				$available = ( '' === $available || '1' === (string) $available );
				$winery    = (string) get_post_meta( $wine->ID, \CBCWineMenu\Admin\WineMetaBox::meta_key( 'winery' ), true );
				$vintage   = (string) get_post_meta( $wine->ID, \CBCWineMenu\Admin\WineMetaBox::meta_key( 'vintage' ), true );
				$bottle    = (string) get_post_meta( $wine->ID, \CBCWineMenu\Admin\WineMetaBox::meta_key( 'bottle_price' ), true );
				$glass     = (string) get_post_meta( $wine->ID, \CBCWineMenu\Admin\WineMetaBox::meta_key( 'glass_price' ), true );
				?>
				<article class="wine<?php echo $available ? '' : ' unavailable'; ?>">
					<h2>
						<?php echo esc_html( get_the_title( $wine ) ); ?>
						<?php if ( ! $available ) : ?>
							<span class="badge"><?php echo esc_html__( 'Unavailable', 'cbc-wine-menu' ); ?></span>
						<?php endif; ?>
					</h2>
					<p class="details">
						<?php
						$bits = array_filter( array( $winery, $vintage ) );
						echo esc_html( implode( ' · ', $bits ) );
						?>
					</p>
					<?php if ( $bottle || $glass ) : ?>
						<p class="prices">
							<?php if ( $bottle ) : ?>
								<?php
								printf(
									/* translators: %s: bottle price */
									esc_html__( 'Bottle %s', 'cbc-wine-menu' ),
									esc_html( $bottle )
								);
								?>
							<?php endif; ?>
							<?php if ( $bottle && $glass ) : ?>
								<span aria-hidden="true"> · </span>
							<?php endif; ?>
							<?php if ( $glass ) : ?>
								<?php
								printf(
									/* translators: %s: glass price */
									esc_html__( 'Glass %s', 'cbc-wine-menu' ),
									esc_html( $glass )
								);
								?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		<?php endif; ?>
	</main>
</body>
</html>
