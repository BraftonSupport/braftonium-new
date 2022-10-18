<?php  
    $classes = array('braftonium-block', 'braftonium-custom-list');

    //Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    //Classes
        if(array_key_exists('className',$block)){           //Custom class from user input
            array_push($classes, $block['className']);
        }

        $blockInlineStyles = array();                  //inline styles - block
        $titleStyles       = '';                       //title styles  - title h2
        $titleClasses      = array();                  //title classes

    //Background Color
        if(array_key_exists('backgroundColor',$block)){
            array_push($classes,'has-'.$block['backgroundColor'].'-background-color');
        }

    //Background Image - With Overlay
    $bannerImage       = get_field('background_image');
    $bg_style = "";
    if($bannerImage){
        $background_position  = get_field('background_image_position');
      
        
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

    //Block Styles
        if(array_key_exists('style',$block)){
            $styles=$block['style'];

            //Margins & Padding
            if(array_key_exists('spacing',$styles)){
                foreach($styles['spacing'] as $type => $values){
                    foreach($values as $key => $value){
                        array_push($blockInlineStyles, $type.'-'.$key.':'.$value.';');
                    }
                }
            }
            if(array_key_exists('color', $styles)){
                array_push($blockInlineStyles, sprintf("background-color: %s;", $styles['color']['background']) );
            }
        } 

    //Alignment
        $full_width = get_field('full_width');

        if($full_width){
            array_push($classes,'full-width');
        }

    // Grid Settings
    // -------------

        // Max amount of columns on Desktop
        $list_grid_cols_desktop  = get_field('list_grid_columns_desktop');
        if($list_grid_cols_desktop === null){ $list_grid_cols_desktop = 3; }

        // Max amount of columns on Tablet
        $list_grid_cols_tablet   = get_field('list_grid_columns_tablet');
        if($list_grid_cols_tablet === null){ $list_grid_cols_tablet = 2; }

        // Max amount of columns on Mobile
        $list_grid_cols_mobile   = get_field('list_grid_columns_mobile');
        if($list_grid_cols_mobile === null){ $list_grid_cols_mobile = 1; }

        $col_vars  = "--cols-sm:{$list_grid_cols_mobile};";
        $col_vars .= "--cols-md:{$list_grid_cols_tablet};";
        $col_vars .= "--cols-lg:{$list_grid_cols_desktop};";

        array_push($blockInlineStyles, $col_vars);

        $list_grid_row_gap       = get_field('list_grid_row_gap');
        if($list_grid_row_gap === null){ $list_grid_row_gap = 10; }

        $list_grid_column_gap    = get_field('list_grid_column_gap');
        if($list_grid_column_gap === null){ $list_grid_column_gap = 10; }

        $gap_vars  = "--row-gap:{$list_grid_row_gap}px;";
        $gap_vars .= "--col-gap:{$list_grid_column_gap}px;";

        array_push($blockInlineStyles, $gap_vars);

    // List Item
    // ---------

    // Item Background Color
    $item_bg_color = get_field('list_item_background_color');
    array_push($blockInlineStyles, "--item-bg:{$item_bg_color};");

    // Content Alignment
        $content_alignment = get_field('list_item_content_alignment');
        array_push($blockInlineStyles, "text-align:{$content_alignment};");

    // Item Layout
        $growth_selector = get_field('list_item_vertical_alignment');
        if($growth_selector === null){ $growth_selector = 1; }
        array_push($classes, "grow-child-{$growth_selector}");

?>
<div 
    id="<?php echo $blockId;?>" 
    class="<?php echo implode(' ', $classes); ?>" style="<?php echo implode('', $blockInlineStyles); ?>">
    <?php if($bannerImage){
    printf('<img src="%s" class="background-image" loading="lazy" style="%s">', $bannerImage['url'], $bg_style);
}?>
    <div class="wrap">
        <InnerBlocks />
    </div>
</div>