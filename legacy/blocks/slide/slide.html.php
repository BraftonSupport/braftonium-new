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
    if(array_key_exists('backgroundColor',$block)){
        array_push($classes,'has-'.$block['backgroundColor'].'-background-color');
    }
    if(array_key_exists('gradient', $block)){
        $gradient = $block['gradient'];
        array_push($inlineStyles, sprintf("background: %s", 'var(--wp--preset--gradient--'.$gradient));
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
        if(array_key_exists('color', $styles)){
                
            if(array_key_exists('background', $styles['color'])){
                array_push($inlineStyles, sprintf("background-color: %s;", $styles['color']['background']) );
            }
            if(array_key_exists('gradient', $styles['color'])){
                array_push($inlineStyles, sprintf("background: %s;", $styles['color']['gradient']) );
            }
        }
    }

?>

<div id="<?php echo esc_attr($blockId); ?>"
        class="<?php echo esc_attr(implode(' ', $classes)); ?>"
        <?php if($inlineStyles){ ?> style="<?php echo implode('', $inlineStyles); ?>" <?php } ?> >
    <InnerBlocks/>
</div>
