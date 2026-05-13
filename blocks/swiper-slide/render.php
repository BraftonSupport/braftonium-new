<?php
/**
 * Render: braftonium/swiper-slide
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'swiper-slide braftonium-swiper-slide',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>
