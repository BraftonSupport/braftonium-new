<?php

include "include/contentlist_controller.php";

acf_register_block_type(array(
    'name'                => 'contentlist',
    'title'               => __('Content List'),
    'description'         => __('Displays a List of Queried Items'),

    'enqueue_assets'      => function (){
        wp_enqueue_style('contentlist', plugin_dir_url(__FILE__).'contentlist.css');
    },

    'category'            => 'braftonium',
    'mode'                => 'preview',  
    'render_callback'     => 'braftonium_blocks_template',

    'supports'            => [
        'anchor'          => true,
        'customClassName' => true,
        'align'           => [ 'left', 'center', 'right', 'full' ],
        'jsx'             => true,
        'color'           => [
            'text'        => true,
            'background'  => false
        ],
        'html'            => false
    ]
));

?>