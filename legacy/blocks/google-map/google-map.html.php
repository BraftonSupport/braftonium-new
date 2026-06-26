<!-- Main block layout - Must be have the name.html.php (example.html.php) -->
<?php 

    global $post;
    $classes = array('brafton-google-map');  
    $map = get_field('google_map');


    //Block ID
    $blockId                = 'id="'.$block['id'].'"';

    //Classes
        if(array_key_exists('className',$block)){           //Custom class from user input
            array_push($classes,$block['className']);
        }
    
        $inlineStyles           = array();                  //inline styles - block
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
    <style>
        .acf-map { min-height: 275px }
    </style>

    <div <?php echo $blockId;?> class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">    
        <?php   
                if( !empty($map) ) : 
                    ?>
                
                    <div class="brafton-google-map acf-map <?php echo get_field('google-api-key','option') ?>">
                        <div class="marker" data-lat="<?php echo esc_attr($map['lat']); ?>" data-lng="<?php echo esc_attr($map['lng']); ?>"></div>
                    </div>
                    <!-- Only run this script when the final map block on the page has been rendered -->
                    
                        <script defer>
                            (( $ ) => {
                                // Render maps on page load.
                                $(document).ready(function(){
                                    initMap($('#<?php echo $block['id']; ?> .acf-map'));
                                });
                            })(jQuery);                   
                        </script>
                    
                <?php endif; ?>
                
                <div class="client-address-row">
                <?php
                if( have_rows('address') ):                    
                    while ( have_rows('address') ) : the_row();
                            echo '<div class="client-address">';
                            echo sanitize_text_field(get_sub_field('address_line'));
                            echo '</div>';
                    endwhile;
                endif; 

                if( have_rows('phone') ): 
                    while ( have_rows('phone') ) : the_row();
                        echo '<div class="client-phone">';
                        echo sanitize_text_field(get_sub_field('phone_label')).': ';
                        $tel = '';
                        $tel = sanitize_text_field(get_sub_field('phone_number'));
                        printf('<a href="tel:%s">%s</a>', $tel, $tel);
                        echo '</div>';
                    endwhile;
                endif; 
                if( have_rows('email') ): 
                    while ( have_rows('email') ) : the_row();
                        echo '<div class="client-email">';
                        echo sanitize_text_field(get_sub_field('email_label')).': ';
                        echo sanitize_text_field(get_sub_field('email_address'));
                        echo '</div>';
                    endwhile;
                endif;
                ?>
                </div>
    </div>