<?php

function load_animation_support() {
    wp_enqueue_style('animation-style', plugin_dir_url( __FILE__ ) .'css/animations.css');
    wp_enqueue_script('animations', plugin_dir_url( __FILE__ ) .'js/animations.js');
}

add_action( 'wp_enqueue_scripts', 'load_animation_support' );

function animation_microstyles($classList, $blockType) {
    if($blockType == 'acf/custom-row') {
        $classList[] = array('label' => 'Columns Fade Infinite', 'value'=> 'columns-fade-infinite');
        $classList[] = array('label' => 'Columns Fade Single', 'value'=> 'columns-fade-single');
        $classList[] = array('label' => 'Columns Slide Right', 'value'=> 'columns-slide-right');
        $classList[] = array('label' => 'Columns Slide Left', 'value'=> 'columns-slide-left');
    }
    if($blockType == 'acf/banner') {
        $classList[] = array('label' => 'Title Slide Right', 'value'=> 'title-slide-right');
        $classList[] = array('label' => 'Title Fade In', 'value'=> 'title-fade-in');
    }
    return $classList;
}

add_filter('braftonium_class_list', 'animation_microstyles', 10, 2);

?>