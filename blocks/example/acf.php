<?php
    acf_register_block_type(array(
        'name'			    => 'example',//slug
        'title'			    => __('Example'),//title
        'description'       => __('Custom example block'),
        'render_template'	=> dirname(__FILE__).'/html.php',
        'enqueue_assets'    => function (){
            wp_enqueue_style(basename(dirname(__FILE__)).'-css', plugin_dir_url(__FILE__).'/example.css');
        },
        'category'          => 'braftonium',
        'mode'			    => 'auto',      
    ));
?>