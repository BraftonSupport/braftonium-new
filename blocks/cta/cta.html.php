<!-- Main block layout - Must be have the name.html.php (example.html.php) -->
<?php  
    $classes                = array('braftonium-cta');   //use your own general class name
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

    //Background Image - With Overlay
        $bannerImage       = get_field('cta_background_image');
        $bg_style = "";
        if($bannerImage){
            $background_position  = get_field('cta_background_image_position');
            if($background_position){
                $bg_style.='object-position:';   
                foreach($background_position as $key => $value){
                    if($value){
                        $bg_style.=' '.$key.' '.$value.'px';
                    }
                }
                $bg_style.=";";
            }
        }
    //CTA Heading
        $ctaHeading = get_field('cta_heading');
    //CTA Text
        $ctaText = get_field('cta_text');
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
                array_push($inlineStyles, sprintf("background-color: %s;", $styles['color']['background']) );
            }
        } 

    //Alignment
    $full_width = get_field('cta_full_width');

        if($full_width){
            array_push($classes,'full-width');
        }
       
        $allowed_blocks = array( 'core/heading', 'core/paragraph', 'core/button' );
?>

<div <?php echo $blockId;?> class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">
    <?php if($bannerImage){
        printf('<img src="%s" class="background-image" loading="lazy" style="%s">', $bannerImage['url'], $bg_style);
    }?>    
    <div class="cta-rows cta-left wrap">
        <div class="cta-row">
                <InnerBlocks allowedBlocks="<?php esc_attr( wp_json_encode( $allowed_blocks ) ) ?>"  ?>" />
        </div>
    </div>

</div>