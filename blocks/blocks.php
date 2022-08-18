<?php 
    //Include all blocks - .acf.php
        add_action('acf/init', 'start_blocks');
        function start_blocks(){
            $files = glob(dirname(__FILE__)."/**/acf.php");
            //loop through all block setup files
            foreach($files as $file){
                if(is_file($file)){
                    require_once $file;//include block setup file

                    //include ACF fields
                    $json=dirname($file).'/fields.json';
                    if(is_file($json)){               
                        foreach(json_decode(file_get_contents($json)) as $group){
                            acf_add_local_field_group(json_decode(json_encode($group), true));
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

    //Check for template in theme(if found override plugin template)
        add_filter('acf/register_braftonium_block_type_args', 'my_acf_register_block_type_args');
        function register_braftonium_block_type_args( $args ){
            $file=str_replace('acf/','/',get_template_directory().$args['name'].'.php');
            if(is_file($file)){
                $args['render_template']=get_template_directory().$args['name'].'.php';
            }
            return $args;
        }
?>