<?php

    // List Item Configuration

    //inline styles - block
    $inlineStyles = array(); 

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block','braftonium-custom-list-item');

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

    //Starter Content
    $template = array(
        array( 'core/image', array() ),
        array( 'core/heading', array( 'content' => '<strong>Heading</strong>', "level" => 4 ) ),
        array( 'core/paragraph', array( 'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer molestie orci massa, eu fringilla nunc sollicitudin ut. Proin quis mi ultrices, malesuada felis non, tempus lorem.' ) )
    );

?>
<div id="<?php echo esc_attr($blockId); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    <?php if($inlineStyles){ ?> style="<?php echo implode('',$inlineStyles); ?>" <?php } ?> >
    <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>"/>
</div>