<?php

    // Block Base Configuration

    // Inline Styles
    $inlineStyles = array(); 

    // Classes
    $classes = array('braftonium-block', 'braftonium-slider');

    // Custom Class
    if (!empty($block['className'])) {
        $classes[] = $block['className'];
    }

    // Horizontal Alignment
    if (!empty($block['align'])) {
        $classes[] = 'align' . $block['align'];
    }

    //Block Styles
    if (!empty($block['style'])) {
        $styles = $block['style'];

        //Margins & Padding
        if (!empty($styles['spacing'])) {
            foreach ($styles['spacing'] as $type => $values) {
                foreach ($values as $key => $value) {
                    $inlineStyles[] = "{$type}-{$key}:{$value};";
                }
            }
        }
    }

    // Slider Configuration

    // Dots
    $dots_visibility = get_field('dots_visibility') ?? 'visible';
    $dots_placement = get_field('dots_placement');
    $classes[] = "slick-dots-{$dots_placement}";

    // Arrows
    $arrows_visibility = get_field('arrows_visibility');
    $arrows_type = get_field('arrows_type');

    // Image Arrows
    $arrows_left_image = get_field('arrows_left_image');
    $arrows_right_image = get_field('arrows_right_image');

    // Text Arrows
    $arrows_left_text = get_field('arrows_left_text');
    $arrows_right_text = get_field('arrows_right_text');

    $prev_arrow = "";
    $next_arrow = "";

    $preview_mode = get_field('preview') ?? false;
    if ($preview_mode) {
        $classes[] = "preview-mode";
    }
    $preview_mode_field_id = 'field_6336abc8de37f';

    if ($arrows_visibility == 'visible') {
        // image arrows
        if ($arrows_type == 'image' && $arrows_left_image && $arrows_right_image) {
            $prev_arrow = "<img src='$arrows_left_image' class='slick-prev'>";
            $next_arrow = "<img src='$arrows_right_image' class='slick-next'>";

        // text arrows
        } else if ($arrows_type == 'text' && $arrows_left_text && $arrows_right_text) {
            $prev_arrow = "<span class='slick-prev text-prev'>{$arrows_left_text}</span>";
            $next_arrow = "<span class='slick-next text-next'>{$arrows_right_text}</span>";
        }
    }

    // Playback
    $playback_autoplay_speed = get_field('playback_autoplay_speed') ?? 3000;
    $playback_slide_speed = get_field('playback_slide_speed') ?? 3000;

    // Presentation
    $presentation_slides_to_show = get_field('presentation_slides_to_show') ?? 1;
    $presentation_slides_to_scroll = get_field('presentation_slides_to_scroll') ?? 1;
    $presentation_infinite = get_field('presentation_infinite') ?? false;

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    $sliderId = str_replace('-','_',$blockId);
    $responsive = array();
    $breakpoint_map = array(
        1 => 540,
        3 => 768,
        5 => 1030,
        7 => 1200
    );
    foreach ($breakpoint_map as $num => $breakpoint) {
        if ($presentation_slides_to_show > $num) {
            $obj = new stdClass;
            $obj->breakpoint = $breakpoint;
            $obj->settings = new stdClass;
            $obj->settings->slidesToShow = $presentation_slides_to_show;
            $obj->settings->slidesToScroll = $presentation_slides_to_scroll;
            $responsive[] = $obj;
        }
    }
?>

<div 
    id="<?php echo esc_attr($blockId); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    <?php if ($inlineStyles) : ?> style="<?php echo implode('', $inlineStyles); ?>" <?php endif; ?> >
    <InnerBlocks/>
</div>
<script>
    var <?php echo "$sliderId"; ?>_slider_args = {
        dots: <?php echo $dots_visibility == 'visible' ? 'true' : 'false'; ?>,
        arrows: <?php echo $arrows_visibility == 'visible' ? 'true' : 'false'; ?>,
        <?php if ($prev_arrow) : ?>prevArrow: "<?php echo $prev_arrow; ?>",<?php endif; ?>
        <?php if ($next_arrow) : ?>nextArrow: "<?php echo $next_arrow; ?>",<?php endif; ?>
        speed: <?php echo $playback_slide_speed; ?>,
        autoplay: <?php echo $playback_autoplay_speed > -1 ? 'true' : 'false'; ?>,
        <?php if ($playback_autoplay_speed > -1) : ?>
            autoplaySpeed: <?php echo $playback_autoplay_speed; ?>,
        <?php endif; ?>
        swipeToSlide: <?php echo $is_preview ? "false" : "true"; ?>,
        infinite: <?php echo $presentation_infinite ? "true" : "false"; ?>,
        slidesToShow: <?php echo $presentation_slides_to_show; ?>,
        slidesToScroll: <?php echo $presentation_slides_to_scroll; ?>,


        <?php if ($playback_autoplay_speed === '0') : ?>
        cssEase: 'linear'
        <?php endif; ?>

        <?php if ($responsive) : ?>
        responsive: <?php echo json_encode($responsive); ?>
        <?php endif; ?>

    };

    jQuery(document).ready(function() {
        <?php 
        // gutenberg editor
        if ($is_preview) : ?>
            // hide fields in side panel on preview
            var preview_mode = false;
            acf.addAction('new_field/key=<?php echo $preview_mode_field_id; ?>', function(f){
                var preview_mode_field = f.$el.find('input[type="checkbox"]');
                jQuery(preview_mode_field).on('change', function(e){
                    preview_mode = !preview_mode;
                    if(preview_mode){
                        jQuery(".acf-block-panel .acf-field:not(.acf-field-6336abc8de37f)").hide();
                    } else {
                        jQuery(".acf-block-panel .acf-field:not(.acf-field-6336abc8de37f)").show();
                    }
                });
            });
            // initialize slick
            var slider = jQuery(
                "<?php echo "#{$blockId} > .block-editor-inner-blocks > .block-editor-block-list__layout"; ?>"
            ).not('.slick-initialized');
            slider.slick(<?php echo "$sliderId"; ?>_slider_args, '.braftonium-block.slide');

            <?php if (!$preview_mode) : ?>
                jQuery(
                    "<?php echo "#{$blockId} > .block-editor-inner-blocks > .block-editor-block-list__layout"; ?>"
                ).slick('unslick');
            <?php endif; ?>
        <?php else : // frontend ?>
            var slider = jQuery("<?php echo "#{$blockId}"; ?>");
            slider.not('.slick-initialized').slick(<?php echo "$sliderId"; ?>_slider_args);
        <?php endif; ?>
    });
</script>