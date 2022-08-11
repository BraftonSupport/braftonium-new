<?php 
    //Include all blocks - .acf.php
        add_action('acf/init', 'start_blocks');
        function start_blocks(){
            $files = glob(dirname(__FILE__)."/**/*.acf.php");
            foreach($files as $file){
                if(is_file($file)){
                    require_once $file;
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

    //Auto enqueue assets IF -auto is in the filename
    //This is an optional function which can be used
        function braftonium_enqueue_assets($file){
            $dir=dirname($file);
            $url=plugin_dir_url($file);
            $assets = array_merge(glob("$dir/*-auto.js"),glob("$dir/*-auto.css"));
            foreach($assets as $asset){
                if(is_file($asset)){                        
                    $fileName=basename($asset);
                    if(strpos($fileName,'.css')!=false) {//css
                        wp_enqueue_style(str_replace(".css","",$fileName).'-css', $url.$fileName);
                    } elseif(strpos($fileName,'.js')!=false){//js
                        wp_enqueue_script(str_replace(".js","",$fileName).'-js', $url.$fileName);
                    }
                }
            }
        }

    //Check for template in theme(if found override plugin template)
        add_filter('acf/register_braftonium_block_type_args', 'my_acf_register_block_type_args');
        function register_braftonium_block_type_args( $args ){
            $args['render_template']=is_file(str_replace('acf/','/',get_template_directory().$args['name'].'.php')) ? get_template_directory().$args['name'].'.php' : $args['render_template'];
            return $args;
        }
?>