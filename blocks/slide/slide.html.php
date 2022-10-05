<?php

    // Slider Slide Configuration

    //inline styles - block
    $inlineStyles = array(); 

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block','slide');

    // Custom Class
    if(!empty($block['className'])){
        array_push($classes, $block['className']);
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

<div><?php // slick slider wants these div tags for itself (aka it removes attributes) ?>
    <?php if(is_admin()){ ?>
        <style>
            <?php echo "#{$blockId}"; ?> {
                min-height: 150px;
                padding:12px;
                border: 1px solid rgba(200, 200, 200, 0.25);
            }
            <?php echo "#{$blockId}:hover"; ?> {
                border: 1px solid #aaa;
            }
            <?php echo "#{$blockId}::before"; ?> {
                content: 'SINGLE SLIDE';
                position: absolute;
                top: 0;
                right: 0;
                padding: 2px 4px;
                background-color: #aaa;
                font-size: 8px;
                line-height:1;
                color: white;
                opacity: 0.25;
                pointer-events:none;
            }
            <?php echo "#{$blockId}:hover:before"; ?> {
                opacity:1;
            }
            <?php echo "#{$blockId} .wrap"; ?> {
                margin: 0;
            }
        </style>
    <?php } ?>
    <div id="<?php echo esc_attr($blockId); ?>"
         class="<?php echo esc_attr(implode(' ', $classes)); ?>"
         <?php if($inlineStyles){ ?> style="<?php echo implode('',$inlineStyles); ?>" <?php } ?> >
        <InnerBlocks/>
    </div>
</div><?php // slick slider wants these div tags for itself (aka it removes attributes) ?>