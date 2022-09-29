<?php 
    //Please review example files(all have comments) & Readme file
    //SET includeExample = true to add example to back end

    //Include all blocks - .acf.php
        add_action('acf/init', 'start_blocks');
        function start_blocks(){
            //Loop through block folders and include all .acf.php files
            $files = glob(dirname(__FILE__)."/**/*.acf.php"); 
            foreach($files as $file){  
                if(is_file($file) && basename($file)!='example.acf.php'){
                    require_once $file;//include block setup file

                    //include ACF fields
                    $json=dirname($file).'/'.str_replace('.acf.php','-fields.json',basename($file));
                    if(is_file($json)){        
                        $jsonObject=json_decode(file_get_contents($json));
                        foreach($jsonObject as $group){
                            $jsonArray=json_decode(json_encode($group), true);
                            acf_add_local_field_group($jsonArray);
                        }                        
                    }                    
                }
            }            
        }

    //Create category for blocks
        add_filter( 'block_categories_all' , function( $categories ) {
            $categories[] = array(
                'slug'  => 'braftonium',
                'title' => 'Braftonium'
            );        
            return $categories;
        } );

    //Call back function with template override in the child theme
    //No need to edit this, just copy and past to new block folder and rename
    //You can ofcourse disable theme override or do other stuff if you need
    function braftonium_blocks_template($block, $content = '', $is_preview = false, $post_id = 0){
        $baseName=str_replace('acf/','',$block['name']);             

        //template override location    - themes/active-theme/braftonium/blocks/example.html.php
            $themeOverride=get_template_directory().'/braftonium/blocks/'.$baseName.'.html.php';            
        //default template              - plugins/braftonium/blocks/example/example.html.php
            $defaultTemplate=dirname(__FILE__).'/'.$baseName.'/'.$baseName.'.html.php';
        
        //include template
            include(is_file($themeOverride) ? $themeOverride : $defaultTemplate);
    }
?>