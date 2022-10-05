<?php

    // List Item Configuration

    //inline styles - block
    $inlineStyles = array(); 

    // Block ID
    $blockId = !empty($block['anchor']) ? $block['anchor'] : $block['id'];

    // Classes
    $classes = array('braftonium-block','custom-list-item');

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
<?php if(is_admin()){ ?>
<style>
    <?php // Helper Overlay
        echo "#{$blockId}"; ?> {
        min-height: 150px;
        outline: 1px solid rgba(200, 200, 200, 0.25);
        height: calc(100% - 24px);
        padding:12px;
    }

    <?php echo "#{$blockId}:hover"; ?> {
        outline: 1px solid #aaa;
        z-index:99;
    }
    <?php echo "#{$blockId}::before"; ?> {
        content: 'CUSTOM LIST ITEM';
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
        z-index:99;
    }
    <?php echo "#{$blockId}:hover:before"; ?> {
        opacity:1;
    }
    <?php echo "#{$blockId} .wrap"; ?> {
        margin: 0;
    }
</style>
<?php } ?>
