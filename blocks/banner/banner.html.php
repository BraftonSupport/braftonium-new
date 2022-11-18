<!-- Main block layout - Must be have the name.html.php (example.html.php) -->
<?php  
        $classes                = array('braftonium-banner');   //use your own general class name

    //Block ID
        $blockId                = array_key_exists('anchor',$block) ? 'id="'.$block['anchor'].'"' : '';

    //Classes
        if(array_key_exists('className',$block)){           //Custom class from user input
            array_push($classes,$block['className']);
        }
    
        $inlineStyles           = array();                  //inline styles - block
        $titleStyles            = '';                       //title styles  - title h2
        $titleClasses           = array();                  //title classes


    //Background Image - With Overlay
        $bannerImage       = get_field('banner_background_image');
        $img               = '';

        if(get_field('banner_background_image')){            
            $img='<img src="'.get_field('banner_background_image')['url'].'"'.$img.' fetchpriority="high"/>';
        }
        $rgba  = get_field('banner_background_overlay');
        if($rgba){
            $img.='<div class="overlay" style="background-color:rgba('.$rgba['red'].', '.$rgba['green'].', '.$rgba['blue'].', '.$rgba['alpha'].');"></div>';
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
        }

        $alignContent = get_field('align_content');
        $alignContent = isset($alignContent) ? 'text-align:'.$alignContent.';' : '';
?>

<div <?php echo $blockId;?> class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">
    <?php echo $img; ?>
    <div class="wrap" style="<?php echo $alignContent; ?>">    
        <InnerBlocks />
    </div>
</div>