<?php
/**
 * Digital wine menu structured like the restaurant PDF catalog.
 *
 * @package CBCWineMenu
 * @var array<string, mixed> $session
 * @var array<int, \WP_Post> $wines
 * @var string               $lang
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CBCWineMenu\Admin\WineMetaBox;
use CBCWineMenu\Config;
use CBCWineMenu\PublicFacing\MenuLanguage;

$table_id   = isset( $session['table_id'] ) ? (string) $session['table_id'] : '';
$lang       = isset( $lang ) ? (string) $lang : 'it';
$is_english = ( 'en' === $lang );

$category_titles = array(
	'sparkling-wines' => array(
		'it' => 'Bollicine',
		'en' => 'Sparkling',
	),
	'white-wines'     => array(
		'it' => 'Vini bianchi',
		'en' => 'White wines',
	),
	'rose-wines'      => array(
		'it' => 'Vini rosati',
		'en' => 'Rosé wines',
	),
	'red-wines'       => array(
		'it' => 'Vini rossi',
		'en' => 'Red wines',
	),
);

$labels = $is_english
	? array(
		'menu'             => 'Wine menu',
		'table'            => 'Table %s',
		'grapes'           => 'Grape varieties',
		'region'           => 'Production area',
		'vinification'     => 'Vinification',
		'characteristics'  => 'Characteristics',
		'service'          => 'Service temperature',
		'analytical'       => 'Analytical data',
		'unavailable'      => 'Unavailable',
		'ask_vintage'      => 'Please ask the waiting staff for the vintage',
		'empty'            => 'No wines available yet.',
		'other'            => 'Other',
		'photo_alt'        => 'Wine photo',
	)
	: array(
		'menu'             => 'Menù vini',
		'table'            => 'Tavolo %s',
		'grapes'           => 'Vitigni',
		'region'           => 'Zona di produzione',
		'vinification'     => 'Vinificazione',
		'characteristics'  => 'Caratteristiche',
		'service'          => 'Temperatura di servizio',
		'analytical'       => 'Dati analitici',
		'unavailable'      => 'Non disponibile',
		'ask_vintage'      => 'Per le annate rivolgersi al personale di sala',
		'empty'            => 'Nessun vino disponibile al momento.',
		'other'            => 'Altro',
		'photo_alt'        => 'Foto del vino',
	);

$order = array( 'sparkling-wines', 'white-wines', 'rose-wines', 'red-wines' );
$grouped = array();

foreach ( $wines as $wine ) {
	$terms = get_the_terms( $wine->ID, 'wine_category' );
	$slug  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : 'other';

	if ( ! isset( $grouped[ $slug ] ) ) {
		$grouped[ $slug ] = array();
	}

	$grouped[ $slug ][] = $wine;
}

foreach ( $grouped as $slug => $category_wines ) {
	usort(
		$category_wines,
		static function ( $a, $b ): int {
			$region_a = (string) get_post_meta( $a->ID, WineMetaBox::meta_key( 'region' ), true );
			$region_b = (string) get_post_meta( $b->ID, WineMetaBox::meta_key( 'region' ), true );

			$region_cmp = strcasecmp( $region_a, $region_b );

			if ( 0 !== $region_cmp ) {
				return $region_cmp;
			}

			return strcasecmp( get_the_title( $a ), get_the_title( $b ) );
		}
	);

	$grouped[ $slug ] = $category_wines;
}

$sorted_groups = array();

foreach ( $order as $slug ) {
	if ( ! empty( $grouped[ $slug ] ) ) {
		$sorted_groups[ $slug ] = $grouped[ $slug ];
		unset( $grouped[ $slug ] );
	}
}

foreach ( $grouped as $slug => $items ) {
	$sorted_groups[ $slug ] = $items;
}

$html_lang = $is_english ? 'en' : 'it';
?><!DOCTYPE html>
<html lang="<?php echo esc_attr( $html_lang ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html( $labels['menu'] ); ?></title>
	<style>
		:root {
			--cbc-bg: #f4efe8;
			--cbc-paper: #fbf7f1;
			--cbc-text: #1c1714;
			--cbc-muted: #6a5d55;
			--cbc-line: #ddd3c8;
			--cbc-accent: #7a1f2b;
		}
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: "Iowan Old Style", "Palatino Linotype", Palatino, Georgia, serif;
			background:
				radial-gradient(circle at top, rgba(122, 31, 43, 0.06), transparent 40%),
				linear-gradient(180deg, #f7f2eb 0%, #efe6db 100%);
			color: var(--cbc-text);
			line-height: 1.45;
		}
		.wrap {
			max-width: 42rem;
			margin: 0 auto;
			padding: 1rem 1rem 3rem;
		}
		.topbar {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 1rem;
			margin-bottom: 1.25rem;
		}
		.lang {
			display: inline-flex;
			border: 1px solid var(--cbc-line);
			background: rgba(255,255,255,0.55);
			overflow: hidden;
			font-family: system-ui, sans-serif;
			font-size: 0.78rem;
			letter-spacing: 0.04em;
		}
		.lang a {
			color: var(--cbc-muted);
			text-decoration: none;
			padding: 0.45rem 0.7rem;
		}
		.lang a.is-active {
			background: var(--cbc-accent);
			color: #fff;
		}
		.hero {
			text-align: center;
			padding: 1.5rem 1rem 1.25rem;
			margin-bottom: 1.5rem;
			background: var(--cbc-paper);
			border: 1px solid var(--cbc-line);
		}
		.eyebrow {
			margin: 0 0 0.4rem;
			font-family: system-ui, sans-serif;
			font-size: 0.72rem;
			letter-spacing: 0.16em;
			text-transform: uppercase;
			color: var(--cbc-muted);
		}
		h1 {
			margin: 0;
			font-size: clamp(2rem, 6vw, 2.6rem);
			font-weight: 700;
			letter-spacing: 0.04em;
			text-transform: uppercase;
		}
		.table-meta {
			margin: 0.65rem 0 0;
			font-family: system-ui, sans-serif;
			font-size: 0.85rem;
			color: var(--cbc-muted);
		}
		.category-block {
			margin: 2rem 0 1rem;
		}
		.category-title {
			margin: 0 0 1rem;
			padding: 1.4rem 1rem;
			text-align: center;
			background: var(--cbc-paper);
			border: 1px solid var(--cbc-line);
		}
		.category-title span {
			display: block;
			font-size: clamp(1.5rem, 5vw, 2rem);
			letter-spacing: 0.12em;
			text-transform: uppercase;
			color: var(--cbc-accent);
		}
		.wine {
			padding: 1.25rem 0 1.4rem;
			border-top: 1px solid var(--cbc-line);
		}
		.wine.unavailable { opacity: 0.55; }
		.wine-head {
			display: flex;
			justify-content: space-between;
			gap: 1rem;
			align-items: flex-start;
			margin-bottom: 0.75rem;
		}
		.wine-title {
			margin: 0;
			font-size: 1.15rem;
			line-height: 1.25;
			text-transform: uppercase;
			letter-spacing: 0.02em;
		}
		.price {
			flex: 0 0 auto;
			font-family: system-ui, sans-serif;
			font-size: 1.05rem;
			font-weight: 700;
			color: var(--cbc-accent);
			white-space: nowrap;
		}
		.winery {
			margin: 0 0 0.85rem;
			font-style: italic;
			color: var(--cbc-muted);
		}
		.media {
			margin: 0 0 0.9rem;
		}
		.media img {
			display: block;
			width: 100%;
			max-height: 14rem;
			object-fit: cover;
			border: 1px solid var(--cbc-line);
		}
		.field {
			margin: 0 0 0.7rem;
		}
		.field-label {
			display: block;
			margin-bottom: 0.15rem;
			font-family: system-ui, sans-serif;
			font-size: 0.72rem;
			letter-spacing: 0.06em;
			text-transform: uppercase;
			color: var(--cbc-accent);
		}
		.field-value {
			margin: 0;
			font-size: 0.95rem;
			white-space: pre-line;
		}
		.footer-meta {
			margin-top: 0.85rem;
			font-family: system-ui, sans-serif;
			font-size: 0.82rem;
			color: var(--cbc-muted);
		}
		.badge {
			display: inline-block;
			margin-top: 0.35rem;
			padding: 0.15rem 0.45rem;
			border: 1px solid var(--cbc-line);
			font-family: system-ui, sans-serif;
			font-size: 0.68rem;
			letter-spacing: 0.05em;
			text-transform: uppercase;
			color: var(--cbc-accent);
		}
		.empty {
			text-align: center;
			color: var(--cbc-muted);
			font-family: system-ui, sans-serif;
		}
		.simple-item {
			display: flex;
			justify-content: space-between;
			gap: 1rem;
			padding: 0.85rem 0;
			border-top: 1px solid var(--cbc-line);
		}
		.simple-item.unavailable { opacity: 0.55; }
		.simple-item h3 {
			margin: 0 0 0.25rem;
			font-size: 1.05rem;
			text-transform: uppercase;
			letter-spacing: 0.02em;
		}
		.simple-item .note {
			margin: 0;
			font-family: system-ui, sans-serif;
			font-size: 0.82rem;
			color: var(--cbc-muted);
			white-space: pre-line;
		}
		.menu-footer {
			margin-top: 2.5rem;
			padding: 1.5rem 1rem;
			text-align: center;
			background: var(--cbc-paper);
			border: 1px solid var(--cbc-line);
		}
		.menu-footer .tagline {
			margin: 0 0 1rem;
			font-size: 1.05rem;
			font-style: italic;
		}
		.social {
			display: flex;
			justify-content: center;
			align-items: center;
			gap: 0.75rem;
			margin: 0 0 1rem;
		}
		.social a,
		.credit-social a {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 2.25rem;
			height: 2.25rem;
			color: var(--cbc-accent);
			text-decoration: none;
			border: 1px solid var(--cbc-line);
			border-radius: 999px;
			transition: background 0.15s ease, color 0.15s ease;
		}
		.social a:hover,
		.social a:focus-visible,
		.credit-social a:hover,
		.credit-social a:focus-visible {
			background: var(--cbc-accent);
			color: #fff;
			outline: none;
		}
		.social a svg,
		.credit-social a svg {
			width: 1.05rem;
			height: 1.05rem;
			fill: currentColor;
			display: block;
		}
		.cover-charge,
		.credit {
			margin: 0.35rem 0 0;
			font-family: system-ui, sans-serif;
			font-size: 0.8rem;
			color: var(--cbc-muted);
		}
		.credit {
			display: grid;
			grid-template-columns: 1fr auto 1fr;
			align-items: center;
			column-gap: 0.25rem;
		}
		.credit > span:first-child {
			grid-column: 2;
			text-align: center;
		}
		.credit-social {
			grid-column: 3;
			justify-self: start;
			display: inline-flex;
			align-items: center;
			gap: 0.35rem;
		}
		.credit-social a {
			width: 1.75rem;
			height: 1.75rem;
		}
		.credit-social a svg {
			width: 0.85rem;
			height: 0.85rem;
			/* Optical centering: Instagram glyph reads heavy on the right. */
			transform: translateX(-0.5px);
		}
	</style>
</head>
<body>
	<main class="wrap">
		<div class="topbar">
			<div class="lang" aria-label="Language">
				<a class="<?php echo $is_english ? '' : 'is-active'; ?>" href="<?php echo esc_url( MenuLanguage::url_for( 'it' ) ); ?>">IT</a>
				<a class="<?php echo $is_english ? 'is-active' : ''; ?>" href="<?php echo esc_url( MenuLanguage::url_for( 'en' ) ); ?>">EN</a>
			</div>
			<p class="table-meta" style="margin:0;">
				<?php echo esc_html( sprintf( $labels['table'], $table_id ) ); ?>
			</p>
		</div>

		<header class="hero">
			<p class="eyebrow">CB Cannavale</p>
			<h1><?php echo esc_html( $labels['menu'] ); ?></h1>
		</header>

		<?php if ( empty( $sorted_groups ) ) : ?>
			<p class="empty"><?php echo esc_html( $labels['empty'] ); ?></p>
		<?php else : ?>
			<?php foreach ( $sorted_groups as $slug => $category_wines ) : ?>
				<?php
				$category_label = $category_titles[ $slug ][ $lang ] ?? ( $category_titles[ $slug ]['it'] ?? $labels['other'] );
				?>
				<section class="category-block">
					<header class="category-title">
						<span><?php echo esc_html( $category_label ); ?></span>
					</header>

					<?php foreach ( $category_wines as $wine ) : ?>
						<?php
						$mk = static function ( string $field ) use ( $wine ): string {
							return (string) get_post_meta( $wine->ID, WineMetaBox::meta_key( $field ), true );
						};

						$available   = ( '' === $mk( 'is_available' ) || '1' === $mk( 'is_available' ) );
						$ask_vintage = ( '1' === $mk( 'ask_for_vintage' ) );
						$winery      = $mk( 'winery' );
						$grapes      = $mk( 'grape_variety' );
						$region      = $mk( 'region' );
						$bottle      = $mk( 'bottle_price' );
						$temp        = $mk( 'service_temperature' );
						$alcohol     = $mk( 'alcohol_percentage' );
						$char        = $is_english ? $mk( 'characteristics_en' ) : $mk( 'characteristics_it' );
						$vinif       = $is_english ? $mk( 'vinification_en' ) : $mk( 'vinification_it' );

						if ( '' === $char ) {
							$char = $mk( 'characteristics_it' );
						}
						if ( '' === $vinif ) {
							$vinif = $mk( 'vinification_it' );
						}

						$thumb = get_the_post_thumbnail(
							$wine,
							'large',
							array(
								'alt' => $labels['photo_alt'],
							)
						);
						?>
						<article class="wine<?php echo $available ? '' : ' unavailable'; ?>">
							<div class="wine-head">
								<div>
									<h2 class="wine-title"><?php echo esc_html( get_the_title( $wine ) ); ?></h2>
									<?php if ( ! $available ) : ?>
										<span class="badge"><?php echo esc_html( $labels['unavailable'] ); ?></span>
									<?php endif; ?>
								</div>
								<?php if ( $bottle ) : ?>
									<div class="price"><?php echo esc_html( '€' . $bottle ); ?></div>
								<?php endif; ?>
							</div>

							<?php if ( $winery ) : ?>
								<p class="winery"><?php echo esc_html( $winery ); ?></p>
							<?php endif; ?>

							<?php if ( $thumb ) : ?>
								<div class="media"><?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
							<?php endif; ?>

							<?php if ( $grapes ) : ?>
								<div class="field">
									<span class="field-label"><?php echo esc_html( $labels['grapes'] ); ?></span>
									<p class="field-value"><?php echo esc_html( $grapes ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( $region ) : ?>
								<div class="field">
									<span class="field-label"><?php echo esc_html( $labels['region'] ); ?></span>
									<p class="field-value"><?php echo esc_html( $region ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( '' !== $vinif ) : ?>
								<div class="field">
									<span class="field-label"><?php echo esc_html( $labels['vinification'] ); ?></span>
									<p class="field-value"><?php echo esc_html( $vinif ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( '' !== $char ) : ?>
								<div class="field">
									<span class="field-label"><?php echo esc_html( $labels['characteristics'] ); ?></span>
									<p class="field-value"><?php echo esc_html( $char ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( $temp || $alcohol || $ask_vintage ) : ?>
								<p class="footer-meta">
									<?php
									$bits = array();
									if ( $temp ) {
										$bits[] = $labels['service'] . ': ' . $temp;
									}
									if ( $alcohol ) {
										$bits[] = $labels['analytical'] . ': ' . $alcohol . '% vol.';
									}
									echo esc_html( implode( ' · ', $bits ) );
									?>
									<?php if ( $ask_vintage ) : ?>
										<br><?php echo esc_html( $labels['ask_vintage'] ); ?>
									<?php endif; ?>
								</p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</section>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php
		$glasses  = isset( $glasses ) && is_array( $glasses ) ? $glasses : array();
		$drinks   = isset( $drinks ) && is_array( $drinks ) ? $drinks : array();
		$beers    = isset( $beers ) && is_array( $beers ) ? $beers : array();
		$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();

		$extra_sections = array(
			array(
				'slug'  => 'glasses',
				'title' => $is_english ? 'Wines by the glass' : 'Vini al calice',
				'items' => $glasses,
			),
			array(
				'slug'  => 'drinks',
				'title' => $is_english ? 'Drinks' : 'Drink',
				'items' => $drinks,
			),
			array(
				'slug'  => 'beers',
				'title' => $is_english ? 'Craft beers' : 'Birre artigianali',
				'items' => $beers,
			),
		);

		$item_meta = static function ( \WP_Post $item, string $field ): string {
			return (string) get_post_meta( $item->ID, \CBCWineMenu\Admin\ExtraItemMetaBox::meta_key( $field ), true );
		};
		?>

		<?php foreach ( $extra_sections as $section ) : ?>
			<?php if ( empty( $section['items'] ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<section class="category-block">
				<header class="category-title">
					<span><?php echo esc_html( $section['title'] ); ?></span>
				</header>
				<?php foreach ( $section['items'] as $item ) : ?>
					<?php
					$available = ( '' === $item_meta( $item, 'is_available' ) || '1' === $item_meta( $item, 'is_available' ) );
					$price     = $item_meta( $item, 'price' );
					$size      = $item_meta( $item, 'size' );
					$desc      = $is_english ? $item_meta( $item, 'description_en' ) : $item_meta( $item, 'description_it' );
					if ( '' === $desc ) {
						$desc = $item_meta( $item, 'description_it' );
					}
					$display_title = get_the_title( $item );
					if ( $is_english && 'glasses' === $section['slug'] && '' !== $item_meta( $item, 'description_en' ) ) {
						$display_title = $item_meta( $item, 'description_en' );
						$desc          = '';
					}
					?>
					<article class="simple-item<?php echo $available ? '' : ' unavailable'; ?>">
						<div>
							<h3><?php echo esc_html( $display_title ); ?></h3>
							<?php if ( $size ) : ?>
								<p class="note"><?php echo esc_html( $size ); ?></p>
							<?php endif; ?>
							<?php if ( $desc ) : ?>
								<p class="note"><?php echo esc_html( $desc ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( $price ) : ?>
							<div class="price"><?php echo esc_html( '€' . $price ); ?></div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</section>
		<?php endforeach; ?>

		<footer class="menu-footer">
			<?php
			$tagline = $is_english
				? (string) ( $settings['footer_text_en'] ?? '' )
				: (string) ( $settings['footer_text_it'] ?? '' );
			$facebook  = (string) ( $settings['facebook_url'] ?? '' );
			$instagram = (string) ( $settings['instagram_url'] ?? '' );
			$cover     = (string) ( $settings['cover_charge'] ?? '' );

			$icon_facebook = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H8v3h3v7h3v-7h3l1-3h-4V9c0-.6.4-1 1-1z"/></svg>';
			$icon_instagram = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 7.2A4.8 4.8 0 1 0 12 16.8 4.8 4.8 0 0 0 12 7.2zm0 7.9A3.1 3.1 0 1 1 12 8.9a3.1 3.1 0 0 1 0 6.2zm6.1-8.2a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0zM12 4.4c-2.1 0-2.4 0-3.2.1-.8 0-1.4.2-1.9.4-.5.2-1 .5-1.4.9-.4.4-.7.9-.9 1.4-.2.5-.3 1.1-.4 1.9 0 .8-.1 1.1-.1 3.2s0 2.4.1 3.2c0 .8.2 1.4.4 1.9.2.5.5 1 .9 1.4.4.4.9.7 1.4.9.5.2 1.1.3 1.9.4.8 0 1.1.1 3.2.1s2.4 0 3.2-.1c.8 0 1.4-.2 1.9-.4.5-.2 1-.5 1.4-.9.4-.4.7-.9.9-1.4.2-.5.3-1.1.4-1.9 0-.8.1-1.1.1-3.2s0-2.4-.1-3.2c0-.8-.2-1.4-.4-1.9-.2-.5-.5-1-.9-1.4-.4-.4-.9-.7-1.4-.9-.5-.2-1.1-.3-1.9-.4-.8 0-1.1-.1-3.2-.1zm0 1.5c2.1 0 2.3 0 3.1.1.8 0 1.2.2 1.5.3.4.1.6.3.9.6.3.3.5.5.6.9.1.3.3.7.3 1.5 0 .8.1 1 .1 3.1s0 2.3-.1 3.1c0 .8-.2 1.2-.3 1.5-.1.4-.3.6-.6.9-.3.3-.5.5-.9.6-.3.1-.7.3-1.5.3-.8 0-1-.1-3.1-.1s-2.3 0-3.1.1c-.8 0-1.2-.2-1.5-.3-.4-.1-.6-.3-.9-.6-.3-.3-.5-.5-.6-.9-.1-.3-.3-.7-.3-1.5 0-.8-.1-1-.1-3.1s0-2.3.1-3.1c0-.8.2-1.2.3-1.5.1-.4.3-.6.6-.9.3-.3.5-.5.9-.6.3-.1.7-.3 1.5-.3.8 0 1-.1 3.1-.1z"/></svg>';
			?>
			<?php if ( $tagline ) : ?>
				<p class="tagline"><?php echo esc_html( $tagline ); ?></p>
			<?php endif; ?>

			<?php if ( $facebook || $instagram ) : ?>
				<nav class="social" aria-label="<?php echo esc_attr( $is_english ? 'Restaurant social links' : 'Social del ristorante' ); ?>">
					<?php if ( $facebook ) : ?>
						<a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
							<?php echo $icon_facebook; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup. ?>
						</a>
					<?php endif; ?>
					<?php if ( $instagram ) : ?>
						<a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
							<?php echo $icon_instagram; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup. ?>
						</a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

			<?php if ( $cover ) : ?>
				<p class="cover-charge">
					<?php
					echo esc_html(
						$is_english
							? sprintf( 'Covered / Cover charge = €%s', $cover )
							: sprintf( 'Coperto = €%s', $cover )
					);
					?>
				</p>
			<?php endif; ?>

			<?php
			$designer_name      = Config::designer_full_name();
			$designer_instagram = ltrim( Config::DESIGNER_INSTAGRAM, '@' );
			$designer_ig_url    = Config::designer_instagram_url();
			?>
			<?php if ( '' !== $designer_name ) : ?>
				<p class="credit">
					<span>
						<?php
						echo esc_html(
							$is_english
								? sprintf( 'Designed by %s', $designer_name )
								: sprintf( 'Progettato e realizzato da %s', $designer_name )
						);
						?>
					</span>
					<?php if ( '' !== $designer_ig_url ) : ?>
						<span class="credit-social">
							<a href="<?php echo esc_url( $designer_ig_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( 'Instagram — @%s', $designer_instagram ) ); ?>">
								<?php echo $icon_instagram; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup. ?>
							</a>
						</span>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</footer>
	</main>
</body>
</html>
