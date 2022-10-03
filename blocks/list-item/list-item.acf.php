<?php

    acf_register_block_type(array(
        'name'                => 'list-item',
        'title'               => __('List Item'),
        'description'         => __('List Item for use by List Container'),

        'enqueue_assets'      => function (){
            if(is_admin()){
                wp_enqueue_style('list-item-css', plugin_dir_url(__FILE__).'list-item.css');
            }
        },

        'parent'              => ['acf/list'],

        'category'            => 'braftonium',
        'mode'                => 'preview',  
        'render_callback'     => 'braftonium_blocks_template',

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