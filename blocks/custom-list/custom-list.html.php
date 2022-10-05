<?php  
    $classes = array('custom-list');   //use your own general class name

    //Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

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
        $bannerImage       = get_field('background_image');
        if($bannerImage){
            $background_position  = get_field('background_image_position');
            $img ='background-image: ';
            
            $img.='url('.$bannerImage['url'].');';
            if($background_position){
                $img.='background-position:';   
                foreach($background_position as $key => $value){
                    if($value){
                        $img.=' '.$key.' '.$value.'px';
                    }
                }
                $img.=";";
            }
            array_push($inlineStyles,$img);
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
            if(array_key_exists('color', $styles)){
                array_push($inlineStyles, sprintf("background-color: %s;", $styles['color']['background']) );
            }
        } 

    //Alignment
        $full_width = get_field('full_width');

        if($full_width){
            array_push($classes,'full-width');
        }
    

    // Grid Settings
    // -------------

        $list_grid_cols_desktop  = get_field('list_grid_columns_desktop');
        $list_grid_cols_tablet   = get_field('list_grid_columns_tablet');
        $list_grid_cols_mobile   = get_field('list_grid_columns_mobile');
        $list_grid_row_gap       = get_field('list_grid_row_gap');
        $list_grid_column_gap    = get_field('list_grid_column_gap');

    // Item
    // ----

    // Border

        // Thickness
            $list_item_border_thickness = get_field('list_item_border_thickness');

        // Color
            $list_item_border_color = get_field('list_item_border_color');

        // Radius
            $list_item_border_radius_tl = get_field('list_item_border_radius_tl');
            $list_item_border_radius_tr = get_field('list_item_border_radius_tr');
            $list_item_border_radius_br = get_field('list_item_border_radius_bl');
            $list_item_border_radius_bl = get_field('list_item_border_radius_br');

        // Shadow
            $list_item_shadow_h      = get_field('list_item_box_shadow_horizontal_offset');
            $list_item_shadow_v      = get_field('list_item_box_shadow_vertical_offset');
            $list_item_shadow_blur   = get_field('list_item_box_shadow_blur_amount');
            $list_item_shadow_spread = get_field('list_item_box_shadow_spread_amount');
            $list_item_shadow_color  = get_field('list_item_box_shadow_spread_color');

    // Item Background
        $item_bg_color = get_field('list_item_background_color');

    // Content Alignment
        $alignment_options = get_field('list_item_alignment');

    // Growth Selector
        $child_selector = get_field('list_item_vertical_alignment_child_selector');

?>

<div 
    id="<?php echo $blockId;?>" 
    class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">
    <?php if($bannerImage){
        printf('<img src="%s" class="background-image" loading="lazy">', $bannerImage['url']);
    }?>
    <div class="wrap">
        <InnerBlocks />
    </div>
    <style>
        <?php // Helper Overlay
              if(is_admin()){ ?>
            <?php echo "#{$blockId}"; ?> {
                min-height: 150px;
                padding:12px;
                border: 1px solid rgba(200, 200, 200, 0.25);
            }
            <?php echo "#{$blockId}:hover"; ?> {
                border: 1px solid #aaa;
            }
            <?php echo "#{$blockId}::before"; ?> {
                content: 'CUSTOM LIST';
                position: absolute;
                top: 0;
                right: 0;
                padding: 2px 4px;
                background-color: #aaa;
                font-size: 8px;
                line-height: 1;
                color: white;
                opacity: 0.25;
                pointer-events: none;
            }
            <?php echo "#{$blockId}:hover:before"; ?> {
                opacity: 1;
            }
            .wp-block-acf-custom-row:has(.full-width) {
                width:100vw;
                max-width:100vw;
                margin-left:calc(50% - 50vw) !important;
            }
        <?php } ?>

        <?php 
            if(is_admin()){
                $grid_container = "#{$blockId} .wrap > .block-editor-inner-blocks > .block-editor-block-list__layout";
            } else {
                $grid_container = "#{$blockId} .wrap";
            }
        ?>

        <?php echo $grid_container; ?> {
            display: grid;
            grid-template: auto / <?php echo str_repeat('1fr ', $list_grid_cols_mobile); ?>;
            grid-auto-rows: 1fr;
            row-gap: <?php echo $list_grid_row_gap; ?>px;
            column-gap: <?php echo $list_grid_column_gap; ?>px;
            align-items: stretch;
            text-align: <?php echo $alignment_options; ?>;
            height: 100%;
        }

        @media(min-width:768px){
            <?php echo $grid_container; ?> {
                grid-template: auto / <?php echo str_repeat('1fr ', $list_grid_cols_tablet); ?>;
            }
        }

        @media(min-width:1024px){
            <?php echo $grid_container; ?> {
                grid-template: auto / <?php echo str_repeat('1fr ', $list_grid_cols_desktop); ?>;
            }
        }

        <?php if(is_admin()){ ?>
            <?php echo $grid_container; ?> .block-editor-inner-blocks,
            <?php echo $grid_container; ?> .wp-block-acf-custom-list-item .acf-block-body,
            <?php echo $grid_container; ?> .wp-block-acf-custom-list-item .acf-block-body > div,
            <?php echo $grid_container; ?> .wp-block-acf-custom-list-item .acf-block-body > div > .acf-block-preview {
                width: 100%;
                height: 100%;
            }
        <?php } ?>

        <?php 
            $list_item_container = "{$grid_container} " . 
                (is_admin() ? 
                 ".wp-block-acf-custom-list-item .block-editor-block-list__layout" : 
                 '.custom-list-item'
                );
        ?>

        <?php echo $list_item_container; ?> {
            position: relative;
            display: flex;
            flex-flow: column;
            overflow: hidden;
            height:100%;

            <?php // Item Background Color
                  if($item_bg_color){ ?>
                background-color: <?php echo $item_bg_color; ?>;
            <?php } ?>

            <?php // Border Thickness
                  if($list_item_border_thickness){ ?>
                border: <?php echo $list_item_border_thickness; ?>px solid <?php echo $list_item_border_color; ?>;
            <?php } ?>

            <?php // Border Radius
                  if(
                    $list_item_border_radius_tl || $list_item_border_radius_tr || 
                    $list_item_border_radius_bl || $list_item_border_radius_br
                ){ ?>
                border-radius: <?php echo "{$list_item_border_radius_tl}px {$list_item_border_radius_tr}px  {$list_item_border_radius_bl}px {$list_item_border_radius_br}px"; ?>;
            <?php } ?>

            <?php // Box Shadow
                  if($list_item_shadow_blur){ ?>
                box-shadow: <?php echo "{$list_item_shadow_h}px {$list_item_shadow_v}px {$list_item_shadow_blur}px  {$list_item_shadow_spread}px {$list_item_shadow_color}"; ?>;
            <?php } ?>
        }

        <?php if($child_selector){ ?>
            <?php echo "{$list_item_container} :nth-child($child_selector)"; ?> {
                flex: 1;
            }
        <?php } ?>
    </style>
</div>