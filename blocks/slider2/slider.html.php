<?php 
$id = $block['id'];
$classes = isset($block['className'])? $block['className'] : '';
$classes = explode(' ', $classes);
$show = get_field('show');
$scroll = get_field('scroll');
$scroll = $scroll? $scroll : 1;
$auto = get_field('auto');
$dots = get_field('dots');
$dots = $dots? 'true': 'false';
if($auto){
    $classes[] = 'autoscroll';
}
if($dots == 'true'){
    $classes[] = 'with-dots';
}
$auto = $auto? 'true': 'false';

$test = get_field('test-slide');
$responsive = [];
if($show > 3){
    $three = new stdClass;
    $three->breakpoint = 768;
    $three->settings = new stdClass;
    $three->settings->slidesToShow = 2;
    $three->settings->slidesToScroll = 1;
    $responsive[] = $three;
}
if($show > 5){
    $five = new stdClass;
    $five->breakpoint = 1030;
    $five->settings = new stdClass;
    $five->settings->slidesToShow = 3;
    $five->settings->slidesToScroll = (int)$scroll;
    $responsive[] = $five;
}
if($show > 7){
    $seven = new stdClass;
    $seven->breakpoint = 1200;
    $seven->settings = new stdClass;
    $seven->settings->slidesToShow = 5;
    $seven->settings->slidesToScroll = (int)$scroll;
    $responsive[] = $seven;
}

?>
<div class="slider-block brafton-blocks <?php echo implode(' ', $classes); ?>" id="<?php echo $id; ?>">
    <InnerBlocks/>
</div>
<script>
    <?php if(!$is_preview){ ?>
    jQuery(document).ready(function(){
        jQuery('#<?php echo $id; ?>').slick({
            dots: <?php echo $dots; ?>,
            slidesToShow: <?php echo $show; ?>,
            slidesToScroll: <?php echo $scroll; ?>,
            autoplay: <?php echo $auto; ?>,
            speed: 1500,
            autoplaySpeed: 1,
            <?php if($responsive){ ?>
            responsive: <?php echo json_encode($responsive); ?>
            <?php } ?>
            
        })
    });
    <?php }else if(($is_preview && $test)){
        ?>
jQuery('#<?php echo $id; ?> > .block-editor-inner-blocks > .block-editor-block-list__layout').slick({
            dots: <?php echo $dots; ?>,
            slidesToShow: <?php echo $show; ?>,
            slidesToScroll: <?php echo $scroll; ?>,
            autoplay: <?php echo $auto; ?>,
            speed: 1500,
            autoplaySpeed: 1,
        })
        <?php
    }else{ ?>
        jQuery('#<?php echo $id; ?> > .block-editor-inner-blocks > .block-editor-block-list__layout').slick('unslick');
        <?php 
    } ?>
</script>