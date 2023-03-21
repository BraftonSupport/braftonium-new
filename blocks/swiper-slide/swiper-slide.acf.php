<?php

acf_register_block_type([
    'name'			      => 'swiper-slide',
    'title'			      => __('Swiper Slide'),
    'description'         => __('Single Slide for the Brafton Content Swiper'),
    'enqueue_assets'      => function (){
        $plugin_dir = plugin_dir_url(__FILE__);
        wp_enqueue_style('swiper-slide', "{$plugin_dir}swiper-slide.css");
    },
    'parent'              => [ 'acf/swiper' ],
    'category'            => 'braftonium',
    'mode'			      => 'preview',  
    'render_callback'     => 'braftonium_blocks_template',
    'acf_block_version'   => 2,
    'supports'		      => [
        'anchor'		  => true,                            // anchor - ID
        'customClassName' => true,                            // custom class
        'jsx' 			  => true,                            // Enable JSX
        'color'           => [                                // COLORS
            'background'  => true,
            'text'        => true,
            'gradients'   => false
        ],
        'spacing'         => [                                //Spacing
            'margin'      => ['top','bottom'],
            'padding'     => ['top','bottom', 'left','right'],
        ],
        'html'            => false,                           //Disable HTML editing
        'align'           => ['wide', 'full'],
    ]
]);