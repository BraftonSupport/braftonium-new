<?php

    acf_register_block_type(array(
        'name'                => 'slide',
        'title'               => __('Single Slide'),
        'description'         => __('Single Slide for the Content Slider'),

        'parent'              => ['acf/slider'],

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