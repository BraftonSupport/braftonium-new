<?php
    /*
        All blocks must have the following files
        1. acf.php      -> Register the block
        2. html.php     -> Output the block
        3. fields.json  -> Fields exported from ACF (Adding fields is automated in blocks.php)

        NOTE
        You can add a template to /themes/child-theme/templates/blocks/ using the folder name for your block ie. example.html.php
    */
    acf_register_block_type(array(
        'name'			    => 'example',
        'title'			    => __('Example'),
        'description'       => __('Custom example block'),        
        'enqueue_assets'    => function (){
            wp_enqueue_style('braftonium-example-css', plugin_dir_url(__FILE__).'/example.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'preview',  
        'render_callback'   => 'braftonium_blocks_example_template',
        'supports'		    => [//Options - https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
            'anchor'		    => true,                            //anchor - ID
            'customClassName'	=> true,                            //custom class
            'align'			    => [ 'left', 'right', 'full' ],     //alignment            
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
            'typography'        => [                                //Typography
                    'fontSize'              => true,                //Used for title text
                    'lineHeight'            => true,
            ],
            'html'              => false,                           //Disable HTML editing
        ],  
    ));

    //Check for a override template
    //themes/active-theme/braftonium/blocks/example.html.php will override
    //the default /blocks/example/html.php
    function braftonium_blocks_example_template($block, $content = '', $is_preview = false, $post_id = 0){
        $baseName=str_replace('acf/','',$block['name']);             
        $themeOverride=get_template_directory().'/braftonium/blocks/'.$baseName.'.html.php';
        $defaultTemplate=dirname(__FILE__).'/'.$baseName.'.html.php';
        include(is_file($themeOverride) ? $themeOverride : $defaultTemplate);
    }
?>