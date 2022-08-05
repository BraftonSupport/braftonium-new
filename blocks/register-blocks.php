<?php
    /* This will loop through all folders inside /blocks/ then will include the create-block.php 
     * It is imperative that each block has a create-block.php file as the initializer for the block.
     * 
     * Sample Schema for block titled example
     * 1. Create a folder in /blocks/example
     * 2. Create file /blocks/example/create-block.php (This will initialize the block
     * 3. All dependancy files example.scss, example.js, example.php 
    */

    /*Once acf is initialized the code will run through all blocks and look for the block-settings.php file, 
    * if the file is found it will run create_block for each folder*/
    add_action('acf/init', 'create_all_blocks');    
    function create_all_blocks(){
        if( ! function_exists( 'acf_register_block_type' ) )
		return;

        $Iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__)));
        foreach ($Iterator as $info) {
            if($info->getfileName()=='block-settings.php') {
                $temp=explode('/',str_replace('/block-settings.php','',$info->getPathname()));
                $blockSlug=$temp[count($temp)-1];
                create_block($blockSlug);
            }
        }
    }

    //This is where each specific block will be initialized, most things are automated. 
    //You must make sure block-settings has: Fields, Block Title and Description.
    function create_block($slug){

        //this is the main folder name for the block and will be used to:
        // 1. Block Name
        // 2. Enqueueing JS/CSS (if the files exist)
        // 3. ACF Fields Group Naming
        // 4. Run an initialization function, if it is ever need (and if it exists). slug_init/example_init/etc
            $GLOBALS['braftonium_slug']=$slug;
            $slugWithoutDash=str_replace('_','',$slug);

        //Don't include example block
            if( $slug=='example' )
		    return;

        //include settings file for block
            include($slug.'/block-settings.php');

        //grab settings from block-settings.php
            $settings=$GLOBALS[$slugWithoutDash.'_settings'];

        //- register block - Title & description are from block-settings.php file, 
        //  the rest of the structure is created using the folder names
        //- assets be enqueued if they exist - they must have the same name as the folder "example"
            acf_register_block_type(array(
                'name'			=> $slug,
                'title'			=> $settings['name'],   
                'description'   => $settings['description'],
                'render_template'	=> plugin_dir_path(__DIR__).'blocks/'.$slug.'/'.$slug.".php",
                'mode'			=> 'preview',
                'supports'		=> [
                    'align'			=> false,
                    'anchor'		=> true,
                    'customClassName'	=> true,
                    'jsx' 			=> true,
                ],
                'enqueue_assets'    => 'braftonium_block_assets',
            )); 

        //create field group using unique identifier using settings from block-settings.php Name and Fields
            acf_add_local_field_group(array (
                'key' => 'group_braftonium_'.$slugWithoutDash,
                'title' => $settings['name'],
                'fields' => $settings['fields'],
                'location' => array (
                    array (
                        array (
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/'.$slugWithoutDash,
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
            )); 
        
        //IF the block needs to run initial php for some reason, this can be done by creating a function using the block name with "_" instead of "-"
        // eg: example_init / new_block_init
            $functionRun=$slugWithoutDash.'_init';
            if(function_exists($functionRun)){
                $functionRun();
            }
    }

    //Check the current block being setup and enqueue JS/CSS if they exist. Files must have the same name as the folder Ie. example.js/example.css
        function braftonium_block_assets(){
            global $braftonium_slug;
            $generalPath='blocks/'.$braftonium_slug.'/'.$braftonium_slug;

            if(is_file(plugin_dir_path(__DIR__).$generalPath.'.css')){
                wp_enqueue_style($braftonium_slug.'-styles', plugin_dir_url(__DIR__).$generalPath.'.css');
            }

            if(is_file(plugin_dir_path(__DIR__).$generalPath.'.js')){
                wp_enqueue_script($braftonium_slug.'-scripts', plugin_dir_path(__DIR__).$generalPath.'.js');
            }
        }

        function brafton_field_name($slug){
            return 'field_braftonium_'.str_replace("_", "-", $slug).'_';
        }
?>