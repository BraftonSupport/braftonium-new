<?php 
        function my_register_block_patterns(){
            if (class_exists('WP_Block_Patterns_Registry')) {

                //Register Braftonium Pattern Category
                register_block_pattern_category('braftonium', ['label' => _x('Braftonium', 'textdomain'),]);

                //Include all patterns in braftonium/patterns folder in the plugin
                $files = glob(dirname(__FILE__)."/*-pattern.php"); 
                foreach($files as $file){  
                    if(basename($file)!='example-pattern.php'){
                        require_once $file;
                    }
                }
            }
        }
        add_action( 'init', 'my_register_block_patterns' );
?>