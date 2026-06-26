<?php

    // Block Base Configuration

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id']; 

    // Inline Styles
    $inlineStyles = array(); 

    // Classes
    $classes = array('braftonium-block', 'braftonium-swiper');

    // Custom Class
    if(!empty($block['className'])){
        array_push($classes, $block['className']);
    }

    // Horizontal Alignment
    if(!empty($block['align'])){
        array_push($classes, 'align' . $block['align']);
    }

    //Block Styles
    if(!empty($block['style'])){
        $styles = $block['style'];

        //Margins & Padding
        if(!empty($styles['spacing'])){
            foreach($styles['spacing'] as $type => $values){
                foreach($values as $key => $value){
                    $val = $value;
                    // Why this is necessary I don't know.
                    if(strpos($value, "var:") !== false){
                        $val = str_replace("var:", "", $val);
                        $val = "var(--wp--" . implode("--", explode("|", $val)) . ")";
                    }
                    array_push($inlineStyles, $type.'-'.$key.':'.$val);
                }
            }
        }
    }

    // Slider Configuration

    // Preview Mode
    $preview_mode = get_field('preview_mode');

    // Dots
    $dots_visibility     = get_field('dots_visibility');
    $dots = "<div class='swiper-pagination'></div>";
    if(empty($dots_visibility)){
        $dots_visibility = 'visible';
    }
    
    $dots_placement      = get_field('dots_placement');
    if($dots_placement == 'top'){
        $dots_placement = '--swiper-pagination-bottom:auto; --swiper-pagination-top:0px;';
    } else {
        $dots_placement = '--swiper-pagination-bottom:0px; --swiper-pagination-top:auto';
    }

    $dots_active_color   = get_field('dots_active_color');
    if($dots_active_color === null){
        $dots_active_color = 'rgba(255, 255, 255, 1)';
    }
    $dots_inactive_color = get_field('dots_inactive_color');
    if($dots_inactive_color === null){
        $dots_inactive_color = 'rgba(0, 0, 0, 0.25)';
    }

    // Arrows
    $arrows_visibility   = get_field('arrows_visibility');
    $arrows_type         = get_field('arrows_type');

    // Arow Color
    $arrows_color   = get_field('arrows_color');
    if($arrows_color === null){
        $arrows_color = 'rgba(255, 255, 255, 1)';
    }

    // Image Arrows
    $arrows_left_image   = get_field('arrows_left_image');
    $arrows_right_image  = get_field('arrows_right_image');

    // Text Arrows
    $arrows_left_text    = get_field('arrows_left_text');
    $arrows_right_text   = get_field('arrows_right_text');

    $prev_arrow = "";
    $next_arrow = "";

    $prev_arrow_class = "";
    $next_arrow_class = "";

    if($arrows_visibility == 'visible'){

        // image arrows
        if($arrows_type == 'image' && $arrows_left_image && $arrows_right_image){

            $prev_arrow = "<img src='$arrows_left_image' class='swiper-image-prev'>";
            $next_arrow = "<img src='$arrows_right_image' class='swiper-image-next'>";
            $prev_arrow_class = '.swiper-image-prev';
            $next_arrow_class = '.swiper-image-next';

        // text arrows
        } else if($arrows_type == 'text' && $arrows_left_text && $arrows_right_text){

            $prev_arrow = "<span class='swiper-text-prev'>{$arrows_left_text}</span>";
            $next_arrow = "<span class='swiper-text-next'>{$arrows_right_text}</span>";
            $prev_arrow_class = '.swiper-text-prev';
            $next_arrow_class = '.swiper-text-next';

        } else {
            $prev_arrow = "<div class='swiper-button-prev'></div>";
            $next_arrow = "<div class='swiper-button-next'></div>";
            $prev_arrow_class = ".swiper-button-prev";
            $next_arrow_class = ".swiper-button-next";
        }
    }

    // Playback
    $playback_autoplay_speed = get_field('playback_autoplay_speed');
    if($playback_autoplay_speed === null){ $playback_autoplay_speed = 3000; }
    
    $playback_slide_speed    = get_field('playback_slide_speed');
    if($playback_slide_speed === null){ $playback_slide_speed = 3000; }

    // Presentation
    $presentation_slides_to_show   = get_field('presentation_slides_to_show');
    if($presentation_slides_to_show === null){ $presentation_slides_to_show = 1; }

    $presentation_slides_to_scroll = get_field('presentation_slides_to_scroll');
    if($presentation_slides_to_scroll === null){ $presentation_slides_to_scroll = 1; }

    $presentation_infinite = get_field('presentation_infinite'); 
    if($presentation_infinite === null){ $presentation_infinite = false; }

    // CSS Vars

    // Pagination
    array_push($inlineStyles, "--swiper-pagination-color:{$dots_active_color}");
    array_push($inlineStyles, "--swiper-pagination-bullet-inactive-color:{$dots_inactive_color}");
    array_push($inlineStyles, "--swiper-pagination-bullet-width:15px");
    array_push($inlineStyles, "--swiper-pagination-bullet-height:15px");
    array_push($inlineStyles, "--swiper-pagination-bullet-horizontal-gap:7px");
    array_push($inlineStyles, "--swiper-pagination-bullet-inactive-opacity:0.9");
    
    array_push($inlineStyles, $dots_placement);

    // Arrows
    array_push($inlineStyles, "--swiper-navigation-color:{$arrows_color}");
    

    // Only allow Slides
    $allowed_blocks = [
        'acf/swiper-slide'
    ];

    // Template with Columns
    $template = [
        [ 'acf/swiper-slide' ],
        [ 'acf/swiper-slide' ],
        [ 'acf/swiper-slide' ],
        [ 'acf/swiper-slide' ]
    ];

?>

<div 
    id="<?php echo esc_attr($blockId); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    <?php if($inlineStyles){ ?> style="<?php echo implode(';', $inlineStyles); ?>" <?php } ?> >
    <div id="<?php echo esc_attr($blockId); ?>-swiper-inner">
        <?php if($preview_mode == 'true'){ echo "<!-- -->"; } ?>
        <InnerBlocks 
            class="swiper-wrapper <?php echo $preview_mode == 'true' ? 'horizontal' : 'vertical'; ?>"
            allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) );?>" 
            template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>"
            orientation="<?php echo $preview_mode == 'true' ? 'horizontal' : 'vertical'; ?>"
        />
    </div>
    <?php if($dots_visibility == 'visible'){ echo $dots; } ?>
    <?php if($prev_arrow){ echo $prev_arrow; } ?>
    <?php if($next_arrow){ echo $next_arrow; } ?>
</div>
<script>
    (function(){
        function swiper_setup(slideClass)
        {
            return new Swiper("#<?php echo $blockId; ?>-swiper-inner", {
                autoplay: {
                    enabled: <?php echo $playback_autoplay_speed && !$is_preview ? 'true' : 'false'; ?>,
                    delay: <?php echo $playback_autoplay_speed; ?>,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    enabled: <?php echo $dots_visibility == 'visible' ? 'true' : 'false'; ?>,
                    el: '.swiper-pagination',
                    type: 'bullets',
                    clickable: true
                },
                navigation: {
                    enabled: <?php echo $arrows_visibility == 'visible' ? 'true' : 'false'; ?>,
                    prevEl: "<?php echo $prev_arrow ? $prev_arrow_class : ''; ?>",
                    nextEl: "<?php echo $next_arrow ? $next_arrow_class : ''; ?>",
                },
                loop: <?php echo $presentation_infinite && !$is_preview ? 'true' : 'false'; ?>,
                speed: <?php echo $playback_slide_speed; ?>,
                slideClass: slideClass,
                spaceBetween: slideClass == 'braftonium-swiper-slide' ? 0 : 16,
                slidesPerView: <?php echo $presentation_slides_to_show; ?>,
                slidesPerGroup: <?php echo $presentation_slides_to_scroll; ?>,
                momentumRatio: 2,
                momentumVelocityRatio: 2,
                watchSlidesProgress: true,
                setWrapperSize: true,
                scrollbar: false,
                focusableElements: "input, select, option, textarea, button, video, label, img, div"
            });
        }

        if(document.body.classList.contains("block-editor-page")){
            <?php if($preview_mode == 'true'){ ?>
            setTimeout(function(){
                _swiper = swiper_setup("wp-block-acf-swiper-slide");
            }, 500);
            <?php } ?>
        } else {
            document.addEventListener("DOMContentLoaded", function(e) {
                swiper_setup("braftonium-swiper-slide");
            });
        }

    })();

</script>