<?php
/**
 * Render: braftonium/custom-list-item
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-custom-list-item',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>
