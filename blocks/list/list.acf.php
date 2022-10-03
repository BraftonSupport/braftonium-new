<?php
    acf_register_block_type(array(
        'name'			    => 'list',
        'title'			    => __('List'),
        'description'       => __('List Block.'),
        'enqueue_assets'    => function (){
            wp_enqueue_style('braftonium-list-css', plugin_dir_url(__FILE__).'list.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'preview',  
        'render_callback'   => 'braftonium_blocks_template',//this will add your example.html.php file or look for the template override
        'supports'		    => [//Options - https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
            'anchor'		    => true,                            //anchor - ID
            'customClassName'	=> true,                            //custom class    //alignment            
            'jsx' 			    => true,                            //Enable JSX
            'color'             => [                                //COLORS
                    'background'            => true,
                    'text'                  => false,             
                    'gradients'             => true,
            ],
            'spacing'           => [                                //Spacing
                    'margin'                => ['top','bottom'],
                    'padding'               => ['top','bottom', 'left','right'],
            ],
            'html'              => false,                           //Disable HTML editing
        ],  
    ));
?>