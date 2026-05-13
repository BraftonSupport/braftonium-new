<?php
/**
 * Render: braftonium/slider
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$dots_visible    = ( $attributes['dotsVisibility']   ?? 'visible' ) === 'visible';
$arrows_visible  = ( $attributes['arrowsVisibility'] ?? 'visible' ) === 'visible';
$dots_placement  = $attributes['dotsPlacement']  ?? 'bottom';
$arrows_type     = $attributes['arrowsType']     ?? 'text';
$arrows_left_img = $attributes['arrowsLeftImage']  ?? '';
$arrows_right_img= $attributes['arrowsRightImage'] ?? '';
$arrows_left_txt = $attributes['arrowsLeftText']   ?? 'Previous';
$arrows_right_txt= $attributes['arrowsRightText']  ?? 'Next';

$slick_config = array(
    'dots'           => $dots_visible,
    'arrows'         => $arrows_visible,
    'autoplay'       => true,
    'autoplaySpeed'  => (int) ( $attributes['playbackAutoplaySpeed']    ?? 3000 ),
    'speed'          => (int) ( $attributes['playbackSlideSpeed']        ?? 300 ),
    'slidesToShow'   => (int) ( $attributes['presentationSlidesToShow']  ?? 1 ),
    'slidesToScroll' => (int) ( $attributes['presentationSlidesToScroll']?? 1 ),
    'infinite'       => ! empty( $attributes['presentationInfinite'] ),
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

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class'        => 'braftonium-slider slick-dots-' . $dots_placement,
    'data-slick-config' => wp_json_encode( $slick_config ),
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>
