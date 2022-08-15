<!-- Main block layout - Must be same name as the parent foler: ie. example/example.php -->
<?php 
    //$block = $block ?? [];
    $supports=$block['supports'];
    $text = !empty(get_field('example_text')) ? get_field('example_text') : 'No text';
?>
<div class="example-class">
    <?php echo $text; ?>
</div>