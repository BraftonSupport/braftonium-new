<?php
/**
 * Render: braftonium/custom-list
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bg_url   = $attributes['backgroundImageUrl'] ?? '';
$bg_alt   = $attributes['backgroundImageAlt'] ?? '';
$bg_pos   = $attributes['backgroundPosition'] ?? array( 'top' => 0, 'left' => 0 );
$bg_color = $attributes['bgColor'] ?? array( 'r' => 0, 'g' => 0, 'b' => 0, 'a' => 0 );

// Responsive layout (desktop / tablet / mobile). Merge over defaults so a
// partially-saved attribute can't produce missing keys.
$layout_defaults = array(
    'desktop' => array( 'width' => 1200, 'columns' => 3, 'gap' => 24 ),
    'tablet'  => array( 'width' => 0,    'columns' => 2, 'gap' => 20 ),
    'mobile'  => array( 'width' => 0,    'columns' => 1, 'gap' => 16 ),
);
$layout = $attributes['layout'] ?? array();
foreach ( $layout_defaults as $bp => $vals ) {
    $layout[ $bp ] = array_merge( $vals, (array) ( $layout[ $bp ] ?? array() ) );
}

// Unique class so the scoped responsive CSS only targets this instance.
$uid = wp_unique_id( 'bcl-' );

// Background image position.
$bg_style = '';
if ( $bg_url ) {
    $bg_style = sprintf(
        'top:%dpx;left:%dpx;',
        (int) ( $bg_pos['top'] ?? 0 ),
        (int) ( $bg_pos['left'] ?? 0 )
    );
}

// Wrapper background color (only when an alpha is set).
$wrapper_style = '';
if ( floatval( $bg_color['a'] ?? 0 ) > 0 ) {
    $wrapper_style = sprintf(
        'background-color:rgba(%d,%d,%d,%s);',
        (int) ( $bg_color['r'] ?? 0 ),
        (int) ( $bg_color['g'] ?? 0 ),
        (int) ( $bg_color['b'] ?? 0 ),
        floatval( $bg_color['a'] )
    );
}

/**
 * Build the grid rules for one breakpoint. Width matches the banner/cta
 * content rule (target width, never wider than 90vw, centered). Every property
 * is emitted so mobile-first overrides reset cleanly up the cascade.
 */
$grid_rules = function ( array $cfg ) {
    $cols  = max( 1, (int) ( $cfg['columns'] ?? 1 ) );
    $gap   = max( 0, (int) ( $cfg['gap'] ?? 0 ) );
    $width = (int) ( $cfg['width'] ?? 0 );

    $rules = sprintf( 'grid-template-columns:repeat(%d,minmax(0,1fr));gap:%dpx;', $cols, $gap );

    if ( $width > 0 ) {
        // Same constraint as banner/cta: width capped at 90vw, centered.
        $rules .= sprintf( 'width:%dpx;max-width:90vw;margin-inline:auto;', $width );
    } else {
        // 0 = full width of the block.
        $rules .= 'width:100%;max-width:none;margin-inline:0;';
    }

    return $rules;
};

$sel = '.' . $uid . ' .custom-list-content';

// Mobile-first: base = mobile, then tablet >=768, then desktop >=1024.
$css  = $sel . '{display:grid;' . $grid_rules( $layout['mobile'] ) . '}';
$css .= '@media (min-width:768px){' . $sel . '{' . $grid_rules( $layout['tablet'] ) . '}}';
$css .= '@media (min-width:1024px){' . $sel . '{' . $grid_rules( $layout['desktop'] ) . '}}';

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-custom-list ' . $uid,
    'style' => $wrapper_style,
) );
?>
<style><?php echo $css; ?></style>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_url ) : ?>
        <img src="<?php echo esc_url( $bg_url ); ?>" alt="<?php echo esc_attr( $bg_alt ); ?>" class="background-image" style="<?php echo esc_attr( $bg_style ); ?>" />
    <?php endif; ?>
    <div class="custom-list-content">
        <?php echo $content; ?>
    </div>
</div>
