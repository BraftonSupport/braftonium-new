<?php
/**
 * Render: braftonium/slider
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$dots_visible     = ( $attributes['dotsVisibility']   ?? 'visible' ) === 'visible';
$arrows_visible   = ( $attributes['arrowsVisibility'] ?? 'visible' ) === 'visible';
$dots_placement   = $attributes['dotsPlacement']   ?? 'bottom';
$arrows_type      = $attributes['arrowsType']      ?? 'text';
$arrows_left_img  = $attributes['arrowsLeftImage']  ?? '';
$arrows_right_img = $attributes['arrowsRightImage'] ?? '';
$arrows_left_txt  = $attributes['arrowsLeftText']   ?? 'Previous';
$arrows_right_txt = $attributes['arrowsRightText']  ?? 'Next';
$bg_color         = $attributes['bgColor'] ?? array( 'r' => 0, 'g' => 0, 'b' => 0, 'a' => 0 );

// Responsive presentation tiers (desktop / tablet / cell). Merge over defaults.
$resp_defaults = array(
    'desktop' => array( 'breakpoint' => 1920, 'slidesToShow' => 3, 'slidesToScroll' => 1 ),
    'tablet'  => array( 'breakpoint' => 1024, 'slidesToShow' => 2, 'slidesToScroll' => 1 ),
    'cell'    => array( 'breakpoint' => 600,  'slidesToShow' => 1, 'slidesToScroll' => 1 ),
);
$resp = $attributes['responsive'] ?? array();
foreach ( $resp_defaults as $k => $vals ) {
    $resp[ $k ] = array_merge( $vals, (array) ( $resp[ $k ] ?? array() ) );
}

$tier_settings = function ( array $t ) {
    return array(
        'slidesToShow'   => max( 1, (int) ( $t['slidesToShow'] ?? 1 ) ),
        'slidesToScroll' => max( 1, (int) ( $t['slidesToScroll'] ?? 1 ) ),
    );
};

$slick_config = array(
    'dots'           => $dots_visible,
    'arrows'         => $arrows_visible,
    'autoplay'       => true,
    'autoplaySpeed'  => (int) ( $attributes['playbackAutoplaySpeed'] ?? 3000 ),
    'speed'          => (int) ( $attributes['playbackSlideSpeed']    ?? 300 ),
    'infinite'       => ! empty( $attributes['presentationInfinite'] ),
    // Base = desktop tier (applies to the widest screens).
    'slidesToShow'   => $tier_settings( $resp['desktop'] )['slidesToShow'],
    'slidesToScroll' => $tier_settings( $resp['desktop'] )['slidesToScroll'],
    // Each tier activates at its breakpoint (screen width) and below.
    'responsive'     => array(
        array( 'breakpoint' => (int) $resp['desktop']['breakpoint'], 'settings' => $tier_settings( $resp['desktop'] ) ),
        array( 'breakpoint' => (int) $resp['tablet']['breakpoint'],  'settings' => $tier_settings( $resp['tablet'] ) ),
        array( 'breakpoint' => (int) $resp['cell']['breakpoint'],    'settings' => $tier_settings( $resp['cell'] ) ),
    ),
);

if ( $arrows_visible ) {
    if ( $arrows_type === 'image' ) {
        if ( $arrows_left_img ) {
            $slick_config['prevArrow'] = '<button type="button" class="slick-prev"><img src="' . esc_url( $arrows_left_img ) . '" alt="" /></button>';
        }
        if ( $arrows_right_img ) {
            $slick_config['nextArrow'] = '<button type="button" class="slick-next"><img src="' . esc_url( $arrows_right_img ) . '" alt="" /></button>';
        }
    } else {
        $slick_config['prevArrow'] = '<button type="button" class="slick-prev">' . esc_html( $arrows_left_txt ) . '</button>';
        $slick_config['nextArrow'] = '<button type="button" class="slick-next">' . esc_html( $arrows_right_txt ) . '</button>';
    }
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

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class'             => 'braftonium-slider slick-dots-' . $dots_placement,
    'style'             => $wrapper_style,
    'data-slick-config' => wp_json_encode( $slick_config ),
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>
