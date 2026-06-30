<?php
/**
 * Front-end render for braftonium/nav-dropdown.
 *
 * Outputs a navigation item (core nav-item classes so it sits in a core/navigation
 * menu) with an editable label/link and an InnerBlocks dropdown panel ($content).
 * Behaviour + layout come from the block's own stylesheet (style-index.css) — no
 * theme styling required.
 *
 * @var array    $attributes
 * @var string   $content    Rendered inner blocks (the editable panel content).
 * @var WP_Block $block
 *
 * ───────────────────────────────────────────────────────────────────────────
 * AI / REUSE FRONTAGE — block usage metadata, NOT parsed by WordPress.
 * @intent       A top-level nav item whose label/link reveals an editable dropdown panel
 *               (a heading + any inner blocks); four layout modes.
 * @options      label (RichText menu label); url; linkTarget (_blank toggle); layout
 *               dropdown|fixed|grid|full; panelWidth px (fixed, 200–900); columns (grid/full, 1–6);
 *               rows (grid, 0=auto, column-major fill).
 * @parent       core/navigation (added to its allowed_blocks server-side in blocks/blocks.php).
 * @innerblocks  Panel = InnerBlocks; template = core/heading, then any blocks. reusable: false.
 * @render       li.wp-block-navigation-item.braftonium-nav-dropdown › a.wp-block-navigation-item__content
 *               (label) + div.braftonium-nav-dropdown__panel.braftonium-nav-dropdown__panel--{layout}
 *               (CSS vars --panel-width/--panel-cols/--panel-rows). Adds current-menu-item on URL match.
 * @classes      .braftonium-nav-dropdown .braftonium-nav-dropdown__panel(--dropdown|--fixed|--grid|--full) .has-rows
 * @responsive   Desktop (≥782px): absolute hover/focus-revealed dropdown. ≤781px: panel sits in flow.
 * @selfcontained Ships its own behaviour CSS (style.scss → style-index.css) — no theme CSS required.
 * @usewhen      Building a site nav with dropdown/mega menus inside a core/navigation block.
 * @avoidwhen    A plain link (use a core nav link); content not navigation-related.
 * ───────────────────────────────────────────────────────────────────────────
 */

$label  = ( isset( $attributes['label'] ) && '' !== $attributes['label'] ) ? $attributes['label'] : __( 'Menu', 'braftonium' );
$url    = ! empty( $attributes['url'] ) ? $attributes['url'] : '#';
$target = ( isset( $attributes['linkTarget'] ) && '_blank' === $attributes['linkTarget'] ) ? '_blank' : '';

$classes = array( 'wp-block-navigation-item', 'braftonium-nav-dropdown' );
if ( ! empty( $attributes['className'] ) ) {
	$classes[] = $attributes['className'];
}

// Active when the current URL matches this dropdown's link (or a child of it),
// e.g. /products/ stays active on /products/gate-valves/.
$is_current = false;
$link_path  = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
if ( '' !== $link_path ) {
	$current_path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' );
	if ( $current_path === $link_path || 0 === strpos( $current_path . '/', $link_path . '/' ) ) {
		$is_current = true;
	}
}
if ( $is_current ) {
	$classes[] = 'current-menu-item';
}

$anchor = ! empty( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '';

// Panel layout: dropdown | fixed | grid | full.
$layout      = ! empty( $attributes['layout'] ) ? sanitize_html_class( $attributes['layout'] ) : 'dropdown';
$panel_width = isset( $attributes['panelWidth'] ) ? (int) $attributes['panelWidth'] : 400;
$columns     = isset( $attributes['columns'] ) ? max( 1, (int) $attributes['columns'] ) : 3;
$rows        = isset( $attributes['rows'] ) ? max( 0, (int) $attributes['rows'] ) : 0;

$panel_classes = 'braftonium-nav-dropdown__panel braftonium-nav-dropdown__panel--' . $layout;
if ( 'grid' === $layout && $rows > 0 ) {
	$panel_classes .= ' has-rows';
}
$panel_style = sprintf( '--panel-width:%dpx;--panel-cols:%d;--panel-rows:%d;', $panel_width, $columns, $rows );
?>
<li<?php echo $anchor; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<a class="wp-block-navigation-item__content" href="<?php echo esc_url( $url ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?><?php echo $target ? ' target="_blank" rel="noreferrer noopener"' : ''; ?>>
		<span class="wp-block-navigation-item__label"><?php echo esc_html( $label ); ?></span>
	</a>
	<div class="<?php echo esc_attr( $panel_classes ); ?>" style="<?php echo esc_attr( $panel_style ); ?>">
		<?php echo $content; // phpcs:ignore — inner blocks, already escaped by their own render ?>
	</div>
</li>
