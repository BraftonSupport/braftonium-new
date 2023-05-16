<?php

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block', 'braftonium-swiper-slide');

    // Style Class
    if(!empty($block['className'])){
        $classes[] = $block['className'];
    }

    // Background Color
    if(!empty($block['backgroundColor'])){
        array_push($classes, 'has-'.$block['backgroundColor'].'-background-color');
    }

    // Text Color
    if(!empty($block['textColor'])){
        array_push($classes, 'has-'.$block['textColor'].'-color');
    }

    // Template with Columns
    $template = [
        [
            'core/paragraph',
            []
        ],
    ];

?>
<div 
    id="<?php echo $blockId;?>" 
    class="<?php echo implode(' ', $classes); ?>">
    <InnerBlocks class="braftonium-swiper-slide-inner" template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>" />
</div>