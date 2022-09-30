<?php

    // Content List Configuration
    // ==========================

    // Query
    // -----

    // Post Type
    $post_type = get_field('contentlist_query_post_type');
    $pt_field_id = 'field_632b2cf65729b';

    // Taxonomy
    $taxonomy = get_field('contentlist_query_taxonomies');
    $tax_field_id = 'field_632b30575729c';

    // Terms
    $term = get_field('contentlist_query_term');
    $term_field_id = 'field_632b307f5729d';

    // Count
    $count = get_field('contentlist_query_count');

    // Layout
    // ------

    $columns_mobile  = get_field('contentlist_layout_columns_mobile');
    $columns_pct_mobile = 100;
    if($columns_mobile > 0){
        $columns_pct_mobile = round(100 / $columns_mobile, 2);
    }

    $columns_tablet  = get_field('contentlist_layout_columns_tablet');
    $columns_pct_tablet = 100;
    if($columns_tablet > 0){
        $columns_pct_tablet = round(100 / $columns_tablet, 2);
    }

    $columns_desktop = get_field('contentlist_layout_columns_desktop');
    $columns_pct_desktop = 100;
    if($columns_desktop > 0){
        $columns_pct_desktop = round(100 / $columns_desktop, 2);
    }

    $gap = (int) get_field('contentlist_layout_gap');

    // Item
    // ----

    $border_width   = get_field('contentlist_item_border_width');
    $border_color   = get_field('contentlist_item_border_color');
    $border_radius  = get_field('contentlist_item_border_radius');

    $image_height   = get_field('contentlist_item_image_height');

    $vertical_align = get_field('contentlist_item_vertical_alignment');
    $content_align  = get_field('contentlist_item_content_alignment');
    $item_bg_color  = get_field('contentlist_item_background_color');

    $button_text    = get_field('contentlist_item_button_text');


    // Common Block Settings
    // ---------------------

    //inline styles - block
    $inlineStyles = array(); 

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block','brafton_contentlist');

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
        <div class='brafton_contentlist'>
            <?php

                $items = contentlist_query($post_type, $taxonomy, $term, $count);

                if($items){
                    foreach($items as $item){ 
                        $post_id = $item->ID;
                        $title   = $item->post_title;
                        $excerpt = get_the_excerpt($post_id);
                        $link    = is_admin() ? '#' : get_the_permalink($post_id);
                        $image   = get_the_post_thumbnail_url($post_id, 'full');
                        $readingTime = '';
                        if(function_exists('readingTime')){
                            $readingTime = readingTime($post_id);
                        }
                    ?>
                    <div class='list-item'>
                        <?php if($image){ ?>
                        <div class='list-item-image'>
                            <a href='<?php echo $link; ?>'>
                                <img src='<?php echo $image; ?>' loading="lazy">
                            </a>
                        </div>
                        <?php } ?>
                        <h3 class='list-item-title'><a href='<?php echo $link; ?>'><?php echo $title; ?></a></h3>
                        <div class='list-item-meta'>
                            <?php echo $readingTime; ?>
                        </div>
                        <div class='list-item-content'>
                            <?php echo $excerpt; ?>
                        </div>
                        <?php if($button_text){ ?>
                            <a class='list-item-btn' href='<?php echo $link; ?>'>
                                <?php echo $button_text; ?>
                            </a>
                        <?php } ?>
                    </div>
                    <?php
                    }
                }
            ?>
        </div>
    </div>
    <style>
        <?php echo "#{$blockId} .brafton_contentlist"; ?>,
        <?php echo "#{$blockId} .brafton_contentlist"; ?> * {
            box-sizing: border-box;
        }

        <?php echo "#{$blockId} .brafton_contentlist"; ?> {
            display: flex;
            flex-flow: <?php echo $columns_mobile > 1 ? 'row wrap' : 'column'; ?>;
            gap: <?php echo $gap; ?>px;
        }

        @media(min-width:768px){
            <?php echo "#{$blockId} .brafton_contentlist"; ?> {
                display: flex;
                flex-flow: row wrap;
                <?php if($vertical_align){ 
                    $valign = '';
                    if($vertical_align == 'top'){
                        $valign = 'start';
                    } else if($vertical_align == 'center'){
                        $valign = 'center';
                    } else if($vertical_align == 'bottom'){
                        $valign = 'end';
                    } else if($vertical_align == 'stretch'){
                        $valign = 'stretch';
                    } else {
                        $valign = 'start';
                    }
                ?>
                align-items: <?php echo $valign; ?>;
                <?php } ?>
            }
        }

        <?php echo "#{$blockId} .brafton_contentlist .list-item"; ?> {
            display: flex;
            flex-flow: column;
            flex-basis: calc(<?php echo $columns_pct_mobile; ?>% - <?php echo $gap; ?>px);
            <?php if($border_width){ ?>
                border-style: solid;
                border-width: <?php echo $border_width; ?>px;
            <?php } ?>
            <?php if($border_color){ ?>
                border-color: <?php echo $border_color; ?>;
            <?php } ?>
            <?php if($border_radius){ ?>
                border-radius: <?php echo $border_radius; ?>px;
            <?php } ?>
            background-color: <?php echo $item_bg_color; ?>;
        }

        @media(min-width:768px){
            <?php echo "#{$blockId} .brafton_contentlist .list-item"; ?> {
                flex-basis: calc(<?php echo $columns_pct_tablet; ?>% - <?php echo $gap; ?>px);
            }
        }

        @media(min-width:1024px){
            <?php echo "#{$blockId} .brafton_contentlist .list-item"; ?> {
                flex-basis: calc(<?php echo $columns_pct_desktop; ?>% - <?php echo $gap; ?>px);
            }
        }

        <?php if($image_height) { ?>
            <?php echo "#{$blockId} .brafton_contentlist .list-item .list-item-image"; ?> {
                padding-bottom: <?php echo $image_height; ?>%; 
            }
        <?php } ?>
        <?php if($content_align) { ?>
            <?php echo "#{$blockId} .brafton_contentlist .list-item .list-item-title"; ?>,
            <?php echo "#{$blockId} .brafton_contentlist .list-item .list-item-meta"; ?>,
            <?php echo "#{$blockId} .brafton_contentlist .list-item .list-item-content"; ?>,
            <?php echo "#{$blockId} .brafton_contentlist .list-item .list-item-btn"; ?> {
                text-align: <?php echo $content_align; ?>
            }
        <?php } ?>

    </style>
    <?php if(is_admin()){ ?>
    <script>

        jQuery(document).ready(function($){
            if(acf){

                $.extend({
                    sendAdminAJAXCommand: function(command, options) {
                        var action = 'contentlist_query';
                        var nonce = '<?php echo wp_create_nonce('contentlist_query'); ?>';
                        var resp = null;
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: action,
                                nonce: nonce,
                                command: command,
                                options: options
                            },
                            async: false,
                            success: function(respData) {
                                resp = respData;
                            }
                        });
                        return resp;
                    }
                });

                var pt_select_field = null;
                var tax_select_field = null;
                var term_select_field = null;

                // Post Type Select Field
                acf.addAction('new_field/key=<?php echo $pt_field_id; ?>', function(f){
                    pt_select_field = f.$el.find('select');

                    $(pt_select_field).on('change', function(e){

                        var resp = $.sendAdminAJAXCommand(
                            "get_taxonomies", 
                            { 
                                "post_type": e.target.value 
                            }
                        );

                        if(resp && resp.success && resp.data){
                            
                            $(tax_select_field).find('option').remove();
                            $(tax_select_field).append($("<option selected/>").val('').text('- Select -'));

                            $(term_select_field).find('option').remove();
                            $(term_select_field).append($("<option selected/>").val('').text('- Select -'));

                            $.each(resp.data, function(key, val) {
                                tax_select_field.append($("<option />").val(key).text(val));
                            });
                        }
                    });
                });

                // Taxonomy Select Field
                acf.addAction('new_field/key=<?php echo $tax_field_id; ?>', function(f){

                    tax_select_field = f.$el.find('select');
                    
                    $(tax_select_field).on('change', function(e){

                        var resp = $.sendAdminAJAXCommand(
                            "get_terms", 
                            { 
                                "taxonomy": e.target.value 
                            }
                        );

                        if(resp && resp.success && resp.data){

                            $(term_select_field).find('option').remove();
                            $(term_select_field).append($("<option selected/>").val('').text('- Select -'));

                            $.each(resp.data, function(key, val) {
                                term_select_field.append($("<option />").val(key).text(val));
                            });
                        }
                    });
                });

                // Term Select Field
                acf.addAction('new_field/key=<?php echo $term_field_id; ?>', function(f){
                    term_select_field = f.$el.find('select');
                });

            }
        });
    </script>
    <?php } ?>
</div>