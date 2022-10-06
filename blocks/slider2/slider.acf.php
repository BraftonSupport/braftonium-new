<?php 
add_action('acf/init', 'slider_block');
function slider_block(){
    $blocks = array();
    $blocks[] = array( 
        'name'              => 'slider-block',
        'title'             => __('Slider'),
        'description'       => __('Slider. Each element you add is a piece of the slider. Be sure to group your content.'),
        'render_template'   => BRAFTON_BLOCK_PATH.'/blocks/slider/slider.html.php',
        'category'          => 'brafton-plugin-blocks',
        'icon'              => 'admin-page',
        'keywords'          => array(  ),
        'supports'  => array('jsx' => true),
        'enqueue_assets'    => function(){
            wp_enqueue_style( 'slickjs-theme', BRAFTON_BLOCK_URL.'blocks/css/slider/slick-theme.css');
            wp_enqueue_style( 'slickjs', BRAFTON_BLOCK_URL.'blocks/css/slider/slick.css');
            wp_enqueue_style( 'brafton-slider', BRAFTON_BLOCK_URL.'blocks/css/slider/slider.css');
            wp_enqueue_script( 'slickjs', BRAFTON_BLOCK_URL.'blocks/slider/js/slick.js', array('jquery'),false, true );
        }
    );
    foreach($blocks as $block){
        acf_register_block_type($block);
    }

}
add_action('acf/init', 'slider_block_fields');
function slider_block_fields(){
    acf_add_local_field_group(array(
        'key' => 'group_61e1b4d73020bslider',
        'title' => 'Slider Block',
        'fields' => array(
            array(
                'key' => 'field_61f4755fcf883',
                'label' => 'Slides in view',
                'name' => 'show',
                'type' => 'number',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 5,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '',
                'max' => '',
                'step' => '',
            ),
            array(
                'key' => 'field_61f47595cf884',
                'label' => 'Slides to Scroll',
                'name' => 'scroll',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '',
                'max' => '',
                'step' => '',
            ),
            array(
                'key' => 'field_61f475b0cf885',
                'label' => 'Auto Scroll',
                'name' => 'auto',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 0,
                'ui' => 1,
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
            array(
                'key' => 'field_61f475b0cf885dots',
                'label' => 'Show Navigation Dots',
                'name' => 'dots',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 0,
                'ui' => 1,
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
            array(
                'key' => 'field_61f475dacf886',
                'label' => 'Preview Slider',
                'name' => 'test-slide',
                'type' => 'true_false',
                'instructions' => 'To see your slider in the dashboard you must mark this true. to enter new slides you must turn this off.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 0,
                'ui' => 1,
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
            array(
                'key' => 'field_61dc5f1727a23slider',
                'label' => 'Predefined Class',
                'name' => 'predefined_class',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'single-column' => 'Force Form to be a single column.',
                    'over-white'    => 'adjust colors to display properly over white background',
                    'gray-background'   => 'add Gray background and adjust colors',
                    'top-margin'    => 'increase top margin above form'
                ),
                'default_value' => array(
                ),
                'allow_null' => 0,
                'multiple' => 1,
                'ui' => 1,
                'ajax' => 0,
                'return_format' => 'value',
                'wpml_cf_preferences' => 3,
                'placeholder' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/slider-block',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}