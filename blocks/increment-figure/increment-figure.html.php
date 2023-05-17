<?php

$classes                = array('increment-figure','figure-increment');

//Block ID
$blockId                = array_key_exists('anchor',$block) ? 'id="'.$block['anchor'].'"' : '';

//Classes
if(array_key_exists('className',$block)){           //Custom class from user input
    array_push($classes,$block['className']);
}

$inlineStyles           = array();                  //inline styles - block
$titleStyles            = '';                       //title styles  - title h2
$titleClasses           = array();                  //title classes


//Background Color
if(array_key_exists('backgroundColor',$block)){
    array_push($classes,'has-'.$block['backgroundColor'].'-background-color');
}

//Block Styles
if(array_key_exists('style',$block)){
    $styles=$block['style'];

    //Margins & Padding
    if(array_key_exists('spacing',$styles)){
        foreach($styles['spacing'] as $type => $values){
            foreach($values as $key => $value){
                array_push($inlineStyles,$type.'-'.$key.':'.$value.';');
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
<div <?php echo $blockId;?> class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">
<?php $dataset = get_field('increment_range'); 
?>
<script src="http://localhost/wp6/wp-content/plugins/braftonium-new/blocks/increment-figure/js/figures.js"></script>

<span class="figure-placeholder" data-speed="<?php echo $dataset['speed']; ?>" data-start="<?php echo $dataset['start']; ?>" data-end="<?php echo $dataset['end']; ?>"></span>
<span><?php echo $dataset['after_figure'] ?></span>
</div>