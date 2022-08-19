<?php
    /*
        All blocks must have the following files
        1. acf.php -> Register the block
        2. html.php -> Output the block
        3. fields.json -> Fields exported from ACF (Creating fields is automated in blocks.php)

        NOTE
        You can add a template to /themes/child-theme/templates/blocks/ using the folder name for your block ie. example.html.php
    */
    acf_register_block_type(array(
        'name'			    => 'example',
        'title'			    => __('Example'),
        'description'       => __('Custom example block'),        
        'enqueue_assets'    => function (){
            $folderUrl=plugin_dir_url(__FILE__);//Base url for this folder
            $idBase=basename(dirname(__FILE__));//Name for current folder 

            //add -css/-js/-other make sure all no ids are the same
            wp_enqueue_style($idBase.'-css', $folderUrl.'example.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'auto',  
        'render_callback'   => 'braftonium_blocks_template',//in blocks.php file    
    ));
?>