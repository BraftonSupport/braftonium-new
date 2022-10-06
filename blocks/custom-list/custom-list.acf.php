<?php
    acf_register_block_type(array(
        'name'			    => 'custom-list',
        'title'			    => __('Custom List'),
        'description'       => __('List Block.'),
        'enqueue_assets'    => function (){
            wp_enqueue_style('custom-list-css', plugin_dir_url(__FILE__).'custom-list.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'preview',  
        'render_callback'   => 'braftonium_blocks_template',
        'supports'		    => [
            'anchor'		    => true,                            //anchor - ID
            'customClassName'	=> true,                            //custom class
            'jsx' 			    => true,                            //Enable JSX
            'color'             => [                                //COLORS
                'background'            => true,
                'text'                  => false,
                'gradients'             => true,
            ],
            'spacing'           => [                                //Spacing
                'margin'            => ['top','bottom'],
                'padding'           => ['top','bottom', 'left','right'],
            ],
            'html'              => false,                           //Disable HTML editing
        ],  
    ));
?>