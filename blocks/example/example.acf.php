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
        'name'			    => 'example',
        'title'			    => __('Example'),
        'description'       => __('Custom example block'),        
        'enqueue_assets'    => function (){
            wp_enqueue_style('braftonium-example-css', plugin_dir_url(__FILE__).'/example.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'preview',  
        'render_callback'   => 'braftonium_blocks_example_template',//each block needs its own callback function
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

    //Call back function with template override in the child theme
    //No need to edit this, just copy and past to new block folder and rename
    //You can ofcourse disable theme override or do other stuff if you need
    function braftonium_blocks_example_template($block, $content = '', $is_preview = false, $post_id = 0){
        $baseName=str_replace('acf/','',$block['name']);             

        //template override location    - themes/active-theme/braftonium/blocks/example.html.php
            $themeOverride=get_template_directory().'/braftonium/blocks/'.$baseName.'.html.php';            
        //default template              - plugins/braftonium/blocks/example/example.html.php
            $defaultTemplate=dirname(__FILE__).'/'.$baseName.'.html.php';
        
        //include template
            include(is_file($themeOverride) ? $themeOverride : $defaultTemplate);
    }
?>