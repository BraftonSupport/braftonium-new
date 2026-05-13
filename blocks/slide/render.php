<?php
/**
 * Render: braftonium/slide
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-slide',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>
