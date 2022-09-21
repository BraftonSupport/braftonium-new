<?php

    acf_register_block_type(array(
        'name'                => 'slider',
        'title'               => __('Slider'),
        'description'         => __('Content Slider'),

        'enqueue_assets'      => function (){
            wp_enqueue_style('slick-slider-css', plugin_dir_url(__FILE__).'slick/slick.css');
            wp_enqueue_style('slick-slider-theme-css', plugin_dir_url(__FILE__).'slick/slick-theme.css');
            wp_enqueue_style('braftonium-slider-css', plugin_dir_url(__FILE__).'slider.css');
            wp_enqueue_script('slick-slider-js', plugin_dir_url(__FILE__).'slick/slick.min.js', ['jquery']);
        },

        'category'            => 'braftonium',
        'mode'                => 'preview',  
        'render_callback'     => 'braftonium_blocks_template',

        'supports'            => [
            'anchor'          => true,
            'customClassName' => true,
            'align'           => [ 'left', 'center', 'right', 'full' ],
            'align_content'   => [ 'top', 'center', 'bottom' ],
            'jsx'             => true,
            'color'           => [
                'text'        => true
            ],
            'spacing'         => [
                'margin'      => ['top','bottom'],
                'padding'     => ['top','bottom']
            ],
            'html'            => false
        ]
    ));
?>