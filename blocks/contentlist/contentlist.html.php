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

    // Post Count
    $post_count = get_field('contentlist_query_count');

    // Layout
    // ------

    $columns_mobile  = get_field('contentlist_layout_columns_mobile');
    if($columns_mobile === null){ $columns_mobile = 1; }
    $col_class_mobile = "col-sm-{$columns_mobile}";

    $columns_tablet  = get_field('contentlist_layout_columns_tablet');
    if($columns_tablet === null){ $columns_tablet = 1; }
    $col_class_tablet = "col-md-{$columns_tablet}";

    $columns_desktop = get_field('contentlist_layout_columns_desktop');
    if($columns_desktop === null){ $columns_desktop = 1; }
    $col_class_desktop = "col-lg-{$columns_desktop}";

    $col_classes = "{$col_class_mobile} {$col_class_tablet} {$col_class_desktop}";

    $column_gap     = get_field('contentlist_layout_column_gap');
    if($column_gap === null){ $column_gap = 10; }
    $row_gap        = get_field('contentlist_layout_row_gap');
    if($row_gap === null){ $row_gap = 10; }

    // Item
    // ----

    $image_height   = get_field('contentlist_item_image_height');
    if($image_height === null){
        $image_height = 75;
    }

    $content_align   = get_field('contentlist_item_content_alignment');
    $content_padding = get_field('contentlist_item_content_padding');
    if($content_padding === null){ $content_padding = 10; }

    $item_bg_color   = get_field('contentlist_item_background_color');
    $button_text     = get_field('contentlist_item_button_text');

    // List Item inline Styling
    $itemInlineStyles  = "align-items: $content_align;";
    $itemInlineStyles .= "text-align: " . ($content_align == 'start' ? 
        'left' : ($content_align == 'end' ? 'right' : 'center')) . ';';
    $itemInlineStyles .= "background-color: {$item_bg_color};";

    // Word Count
    $word_count = get_field('contentlist_item_word_count');

    // Common Block Settings
    // ---------------------

    //inline styles - block
    $blockInlineStyles = array(); 

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block', 'braftonium-contentlist');

    // Custom Class
    if(!empty($block['className'])){
        array_push($classes, $block['className']);
    }

    // Horizontal Alignment
    if(!empty($block['align'])){
        array_push($classes, 'align' . $block['align']);
    }

    // Block Styles
    if(!empty($block['style'])){
        $styles = $block['style'];

        // Text Color
        if(!empty($styles['color'])){
            foreach($styles['color'] as $type => $values){
                // Hex color
                if($type == 'text'){
                    $itemInlineStyles .= "color: " . $values . ";";
                } else {
                    // Theme based colors
                    if(is_array($values)){
                        foreach($values as $key => $value){
                            array_push($blockInlineStyles, $type.'-'.$key.':'.$value.';');
                        }
                    }
                }
            }
        }
    }

    array_push($blockInlineStyles, "gap: {$row_gap}px {$column_gap}px; --col-gap:{$column_gap}px;");

    // Text Color
    $textColorClass = '';
    if(array_key_exists('textColor',$block)){
        $textColorClass = 'has-'.$block['textColor'].'-color';
    }

?>
<div 
    id="<?php echo esc_attr($blockId); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    <?php if($blockInlineStyles){ ?> style="<?php echo implode('',$blockInlineStyles); ?>" <?php } ?> >

        <?php
            $items = contentlist_query($post_type, $taxonomy, $term, $post_count, $word_count);

            if($items){
                foreach($items as $item){ 
                    $post_id = $item->ID;
                    $title   = $item->post_title;
                    $excerpt = get_the_excerpt($post_id);
                    $link    = $is_preview ? '#' : get_the_permalink($post_id);
                    $image   = get_the_post_thumbnail_url($post_id, 'full');
                    $readingTime = '';
                    if(function_exists('readingTime')){
                        $readingTime = readingTime($post_id);
                    }
                ?>

                <div class='list-item <?php echo $col_classes; ?>' style="<?php echo $itemInlineStyles; ?>">
                    <?php if($image){ ?>
                    <div class='list-item-image' style='padding-bottom: <?php echo $image_height; ?>%'>
                        <a href='<?php echo $link; ?>'>
                            <img src='<?php echo $image; ?>' loading="lazy">
                        </a>
                    </div>
                    <?php } ?>
                    <div class='list-item-content' style='padding: <?php echo $content_padding; ?>px'>
                        <h3 class='list-item-title'>
                            <a <?php if($textColorClass){ echo "class='$textColorClass' "; } ?> href='<?php echo $link; ?>'><?php echo $title; ?></a>
                        </h3>
                        <div class='list-item-meta <?php echo $textColorClass; ?>'>
                            <?php echo $readingTime; ?>
                        </div>
                        <div class='list-item-excerpt <?php echo $textColorClass; ?>'>
                            <?php echo $excerpt; ?>
                        </div>
                        <?php if($button_text){ ?>
                            <a class='list-item-btn' href='<?php echo $link; ?>'>
                                <?php echo $button_text; ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <?php
                }
            }
        ?>
</div>
<?php if($is_preview){ ?>
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

            setTimeout(function(){ 
                if($(tax_select_field).val() == ''){
                    $(pt_select_field).trigger('change'); 
                }
            }, 1000);

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