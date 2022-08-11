<!-- Main block layout - Must be same name as the parent foler: ie. example/example.php -->
<?php 
    $block = $block ?? [];
    $supports=$block['supports'];
    $text = !empty(get_field('example_text')) ? get_field('example_text') : 'No text';

    $classes=array();
    //On pause
    //$classes=$block['backgroundColor'] ? array_merge(array('class'=>'has-'.$block['backgroundColor'])) : array();    
    //$themeJson=is_file(get_template_directory().'/theme.json') ? get_template_directory().'/theme.json' : false;
    //var_dump($themeJson);
?>
<div <?php echo get_block_wrapper_attributes($classes); ?>>
    <?php echo $text; ?>
</div>