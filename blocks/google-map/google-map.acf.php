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
        'name'			    => 'google-map',
        'title'			    => __('Google Map'),
        'description'       => __('Google Map block'),        
        'enqueue_assets'    => function (){
            wp_enqueue_style('google-map-css', plugin_dir_url(__FILE__).'/google-map.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'preview',  
        'render_callback'   => 'braftonium_blocks_template',//this will add your example.html.php file or look for the template override
        'supports'		    => [//Options - https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
            'anchor'		    => true,                            //anchor - ID
            'customClassName'	=> true,                            //custom class
            'align'			    => [ 'left', 'right', 'full' ],     //alignment            
            'jsx' 			    => true,                            //Enable JSX
            'color'             => [                                //COLORS
                    'background'            => true,          
                    'gradients'             => true,
            ],
            'spacing'           => [                                //Spacing
                    'margin'                => ['top','bottom'],
                    'padding'               => ['top','bottom'],
            ],
            'typography'        => [                                //Typography
                    'fontSize'              => true,                //Used for title text
                    'lineHeight'            => true,
            ],
            'html'              => false,                           //Disable HTML editing
        ],  
    ));
    /*Add Google API Key to both front and back-end */
    if(get_field('google-api-key','option')) : 
        function braftonium_acf_google_map_api( $api ){
            $api['key'] = get_field('google-api-key','option');
            return $api;
        }
        add_filter('acf/fields/google_map/api', 'braftonium_acf_google_map_api');
    endif;
?>