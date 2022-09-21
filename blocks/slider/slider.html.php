<?php

    // Slider Configuration

    //inline styles - block
    $inlineStyles = array(); 

    // Dots
    $dots_visibility    = get_field('dots_visibility');
    $dots_placement     = get_field('dots_placement');
    $dots_color         = get_field('dots_color');
    $dots_color_active  = get_field('dots_color_active');

    // Arrows
    $arrows_visibility  = get_field('arrows_visibility');
    $arrows_type        = get_field('arrows_type');
    $arrows_use_images  = get_field('arrows_use_images');
    $arrows_left_image  = get_field('arrows_left_image');
    $arrows_right_image = get_field('arrows_right_image');
    $arrows_use_text    = get_field('arrows_use_text');
    $arrows_left_text   = get_field('arrows_left_text');
    $arrows_right_text  = get_field('arrows_right_text');
    $arrows_text_color  = get_field('arrows_text_color');

    $arrows_text_color  = $arrows_text_color ? $arrows_text_color : '#000000';

    $prev_arrow = "";
    $next_arrow = "";

    if($arrows_visibility == 'visible'){

        // default
        $prev_arrow = "<span class='slick-prev'>&lArr;</span>";
        $next_arrow = "<span class='slick-next'>&rArr;</span>";

        // image arrows
        if($arrows_type == 'image' && $arrows_left_image && $arrows_right_image){

            $prev_arrow = "<img src='$arrows_left_image' class='slick-prev'>";
            $next_arrow = "<img src='$arrows_right_image' class='slick-next'>";

        // text arrows
        } else if($arrows_type == 'text' && $arrows_left_text && $arrows_right_text){

            $prev_arrow = "<span class='slick-prev text-prev'>$arrows_left_text</span>";
            $next_arrow = "<span class='slick-next text-next'>$arrows_right_text</span>";

        }
    }

    // Playback
    $playback_autoplay_speed = get_field('playback_autoplay_speed');
    if(!$playback_autoplay_speed){ $playback_autoplay_speed = 0; }
    
    $playback_slide_speed    = get_field('playback_slide_speed');
    if(!$playback_slide_speed){ $playback_slide_speed = 3000; }

    // Presentation
    $presentation_slides_to_show   = get_field('presentation_slides_to_show');
    if(!$presentation_slides_to_show){ $presentation_slides_to_show = 1; }

    $presentation_slides_to_scroll = get_field('presentation_slides_to_scroll');
    if(!$presentation_slides_to_scroll){ $presentation_slides_to_scroll = 1; }

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block','slider');

    // Custom Class
    if(!empty($block['className'])){
        array_push($classes, $block['className']);
    }

    // Horizontal Alignment
    if(!empty($block['align'])){
        array_push($classes, 'align' . $block['align']);
    }

    // Vertical Alignment
    if(!empty($block['align_content'])){
        array_push($classes, 'valign');
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

<div 
    id="<?php echo esc_attr($blockId); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    <?php if($inlineStyles){ ?> style="<?php echo implode('',$inlineStyles); ?>" <?php } ?> >
    <div class="wrap">
        <div class='brafton_slider'>
            <InnerBlocks/>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($){

            var selector = "<?php echo "#{$blockId} .brafton_slider"; ?>";
            var is_editor = document.body.classList.contains( 'block-editor-page' );

            var slider = $(selector).not('.slick-initialized').slick({
                dots: <?php echo $dots_visibility == 'visible' ? 'true' : 'false'; ?>,
                arrows: <?php echo $arrows_visibility == 'visible' ? 'true' : 'false'; ?>,
                prevArrow: "<?php echo $prev_arrow; ?>",
                nextArrow: "<?php echo $next_arrow; ?>",
                speed: <?php echo $playback_slide_speed; ?>,
                autoplay: <?php echo $playback_autoplay_speed ? 'true' : 'false'; ?>,
                autoplaySpeed: <?php echo $playback_autoplay_speed; ?>,
                swipeToSlide: true,
                infinite: is_editor ? false : true,
                waitForAnimate: false,
                slidesToShow: <?php echo $presentation_slides_to_show; ?>,
                slidesToScroll: <?php echo $presentation_slides_to_scroll; ?>
            });

        });
    </script>
    <style>
        <?php if($dots_placement == 'top'){ ?>
            <?php echo "#{$blockId} .brafton_slider .slick-dots"; ?> {
                top:-50px;
            }
        <?php } ?>
        <?php if($dots_color) { ?>
            <?php echo "#{$blockId} .brafton_slider .slick-dots li button::before"; ?> {
                color: <?php echo $dots_color; ?>;
            }
        <?php } ?>
        <?php if($dots_color_active) { ?>
            <?php echo "#{$blockId} .brafton_slider .slick-dots li.slick-active button::before"; ?> {
                color: <?php echo $dots_color_active; ?>;
            }
        <?php } ?>
        <?php if($arrows_visibility == 'visible' && $arrows_type == 'text'){ ?>
            <?php echo "#{$blockId} .brafton_slider .slick-next.text-next"; ?>,
            <?php echo "#{$blockId} .brafton_slider .slick-prev.text-prev"; ?> {
                width:auto;
                font-size:initial;
                color: <?php echo $arrows_text_color; ?>;
            }
            <?php echo "#{$blockId} .brafton_slider .slick-next.text-next"; ?> {
                transform:translateX(100%);
            }
            <?php echo "#{$blockId} .brafton_slider .slick-prev.text-prev"; ?> {
                transform:translateX(-100%);
            }
            <?php echo "#{$blockId} .brafton_slider .text-next::before"; ?>,
            <?php echo "#{$blockId} .brafton_slider .text-prev::before"; ?> {
                display:none;
            }
        <?php } ?>
        <?php if(!empty($block['align_content'])){ 
            $valign = '';
            if($block['align_content'] == 'top'){
                $valign = 'start';
            } else if($block['align_content'] == 'center'){
                $valign = 'center';
            } else if($block['align_content'] == 'bottom'){
                $valign = 'end';
            } else {
                $valign = 'start';
            }
        ?>
            <?php echo "#{$blockId}.valign .brafton_slider .slick-track "; ?> {
                display: flex;
                flex-flow: row wrap;
                align-items: <?php echo $valign; ?>;
            }
        <?php } ?>
    </style>
    <!-- <?php if(!empty($block['align_content'])){ echo $block['align_content']; } ?> -->
</div>