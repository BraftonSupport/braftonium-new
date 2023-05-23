<?php
    acf_register_block_type(array(
        'name'			    => 'increment-figure',
        'title'			    => __('Increment Figure'),
        'description'       => __('Figure that iterates from start number to finish number. Presently in Beta mode!'),        
        'enqueue_assets'    => function (){
            wp_enqueue_style('figures', plugin_dir_url(__FILE__).'/css/figures.css');
            wp_enqueue_script('figures',plugin_dir_url(__FILE__).'js/figures.js',NULL, NULL, false);
        },
        'category'          => 'braftonium',
        'mode'			    => 'preview',  
        'icon'              => array(
            'src'        => 'clock',
            'foreground' => '#8cc040',
            'background' => '#dceeef'
        ),
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