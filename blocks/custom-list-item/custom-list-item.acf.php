<?php

    acf_register_block_type(array(
        'name'                => 'custom-list-item',
        'title'               => __('Custom List Item'),
        'description'         => __('Custom List Item for use by Braftonium Custom List'),

        'parent'              => ['acf/custom-list'],

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