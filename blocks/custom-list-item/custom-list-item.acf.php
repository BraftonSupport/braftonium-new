<?php

    acf_register_block_type(array(
        'name'                => 'custom-list-item',
        'title'               => __('Custom List Item'),
        'description'         => __('Custom List Item for use by Braftonium Custom List'),

        'parent'              => ['acf/custom-list'],

        'category'            => 'braftonium',
        'mode'                => 'preview',  
        'render_callback'     => 'braftonium_blocks_template',

        'enqueue_assets'    => function (){
            wp_enqueue_style('braftonium-custom-list-item-css', plugin_dir_url(__FILE__).'custom-list-item.css');
        },

        'supports'            => [
            'anchor'          => true,
            'customClassName' => true,
            'jsx'             => true,
            'spacing'         => [
                'margin'      => ['top','bottom','left','right'],
                'padding'     => ['top','bottom','left','right']
            ],
            'html'            => false
        ]
    ));

?>