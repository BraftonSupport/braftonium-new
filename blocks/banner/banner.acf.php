<?php
    /*
        All blocks must have the following files (All based on this example)
        1. example.acf.php      -> Register the block
        2. example.html.php     -> Output the block
        3. example-fields.json  -> Fields exported from ACF (Adding fields is automated in blocks.php)

        NOTE
        You can add a template to /themes/child-theme/braftonium/blocks/ using the same template name for your block ie. example.html.php
    */
    acf_register_block_type(array(
        'name'			    => 'banner',
        'title'			    => __('Banner Block'),
        'description'       => __('Banner for Website header'),        
        'enqueue_assets'    => function (){
            wp_enqueue_style('braftonium-banner-css', plugin_dir_url(__FILE__).'/banner.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'preview',  
        'render_callback'   => 'braftonium_blocks_template',//this will add your example.html.php file or look for the template override
        'supports'		    => [//Options - https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
            'anchor'		    => true,                            //anchor - ID
            'customClassName'	=> true,                            //custom class          
            'jsx' 			    => true,                            //Enable JSX
            'spacing'           => [                                //Spacing
                    'margin'                => ['top','bottom'],
                    'padding'               => ['top','bottom'],
            ],
            'html'              => false,                           //Disable HTML editing
        ],  
    ));
?>