<?php 
    //Please review example files(all have comments) & Readme file
    //SET includeExample = true to add example to back end

    //Include all blocks - .acf.php
        add_action('acf/init', 'start_blocks');
        function start_blocks(){
            $includeExample = true; //Set this to true to view example block in the backend

            //Loop through block folders and include all .acf.php files
            $files = glob(dirname(__FILE__)."/**/*.acf.php"); 
            foreach($files as $file){  
                if(is_file($file) && (basename($file)!='example.acf.php' || $includeExample)){
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
?>