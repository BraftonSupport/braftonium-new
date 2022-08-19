<?php 
    //Please review example files(all have comments) & Readme file

    //Include all blocks - .acf.php
        add_action('acf/init', 'start_blocks');
        function start_blocks(){
            $files = glob(dirname(__FILE__)."/**/acf.php");            
            foreach($files as $file){
                if(is_file($file)){
                    require_once $file;//include block setup file

                    //include ACF fields
                    $json=dirname($file).'/fields.json';
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

    //Check for a override template in /themes/active-theme/templates/blocks/example.html.php
    //OR use default /blocks/example/html.php
        function braftonium_blocks_template($block, $content = '', $is_preview = false, $post_id = 0){
            $baseName=str_replace('acf/','',$block['name']);             
            $themeOverride=get_template_directory().'/templates/blocks/'.$baseName.'.html.php';            
            $defaultTemplate=dirname(__FILE__).'/'.$baseName.'/html.php';
            include(is_file($themeOverride) ? $themeOverride : $defaultTemplate);
        }
?>