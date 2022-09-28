<?php
    acf_register_block_type(array(
        'name'			    => 'custom-row',
        'title'			    => __('Custom Row'),
        'description'       => __('Row meant to wrap elements and allow for common options including full with.'),        
        'enqueue_assets'    => function (){
            wp_enqueue_style('braftonium-custom-row-css', plugin_dir_url(__FILE__).'/custom-row.css');
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
                    'text'                  => true,             
                    'gradients'             => true,
            ],
            'spacing'           => [                                //Spacing
                    'margin'                => ['top','bottom'],
                    'padding'               => ['top','bottom'],
            ],
            'html'              => false,                           //Disable HTML editing
        ],  
    ));
?>