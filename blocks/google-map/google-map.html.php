<!-- Main block layout - Must be have the name.html.php (example.html.php) -->
<?php  
    $classes                = array('google-map');   //use your own general class name

    /*
        This example uses both block support attributes and ACF. Example features include:
        1. Background Image/Color Options
        2. Background Image Overlay (Including opacity/alpha with RGBA)
        3. Alignment, Padding & Margin options
        4. Custom ID and Class
        5. Title Input (optional) - Font-size, line-height, color
        6. Innerblocks - So you can add any other blocks inside        
    */

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

            //Typography            
            if(array_key_exists('typography',$styles)){
                $typography = $styles['typography'];
                $titleStyles=array_key_exists('fontSize',$typography) ? 'font-size:'.$typography['fontSize'].';' : '';
                $titleStyles.=array_key_exists('lineHeight',$typography) ? 'line-height:'.$typography['lineHeight'].'px;' : '';
            }
        }

    //Title Font Size
        if(array_key_exists('fontSize',$block)){
            array_push($titleClasses,'has-'.$block['fontSize'].'-font-size');
        }    
    
    //Title Color
        if(array_key_exists('textColor',$block)){
            array_push($titleClasses,'has-'.$block['textColor'].'-color');
        }

    //Alignment
        if(array_key_exists('align',$block)){
            array_push($classes,'align'.$block['align']);
        }
?>

<div <?php echo $blockId;?> class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">
    <div class="wrap">    
        <InnerBlocks />
    </div>
</div>