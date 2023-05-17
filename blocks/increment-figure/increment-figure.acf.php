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
        'icon'              => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M6.5 8a1.5 1.5 0 103 0 1.5 1.5 0 00-3 0zM8 5a3 3 0 100 6 3 3 0 000-6zm6.5 11a1.5 1.5 0 103 0 1.5 1.5 0 00-3 0zm1.5-3a3 3 0 100 6 3 3 0 000-6zM5.47 17.41a.75.75 0 001.06 1.06L18.47 6.53a.75.75 0 10-1.06-1.06L5.47 17.41z" clip-rule="evenodd"></path></svg>',
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