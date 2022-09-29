<!-- Main block layout - Must be have the name.html.php (example.html.php) -->
<?php  
        $classes                = array('brafton-banner');   //use your own general class name

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
            $alignImage     = get_field('align_image');
            if(isset($alignImage)){
                $img=' style="object-position:0 '.$alignImage.'px;"';
            }
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

        $alignText = get_field('align_content');
        $alignText = isset($alignText) ? 'text-align:'.$alignText.';' : '';

        $contentMaxWidth = get_field('content_wrap');
        $contentMaxWidth = isset($contentMaxWidth) ? 'max-width:'.$contentMaxWidth.';' : '';
?>

<div <?php echo $blockId;?> class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">
    <?php echo $img; ?>
    <div class="wrap" style="<?php echo $alignText.$contentMaxWidth; ?>">    
        <InnerBlocks />
    </div>
</div>