<?php

    // Slider Slide Configuration

    //inline styles - block
    $inlineStyles = array(); 

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block', 'braftonium-slide');

    // Custom Class
    if(!empty($block['className'])){
        array_push($classes, $block['className']);
    }

    //Block Styles
    if(!empty($block['style'])){
        $styles = $block['style'];

        //Margins & Padding
        if(!empty($styles['spacing'])){
            foreach($styles['spacing'] as $type => $values){
                foreach($values as $key => $value){
                    array_push($inlineStyles, $type.'-'.$key.':'.$value.';');
                }
            }
        }
    }

?>

<div id="<?php echo esc_attr($blockId); ?>"
        class="<?php echo esc_attr(implode(' ', $classes)); ?>"
        <?php if($inlineStyles){ ?> style="<?php echo implode('', $inlineStyles); ?>" <?php } ?> >
    <InnerBlocks/>
</div>
