<?php

// Getters

// Get Taxonomies
function contentlist_get_taxonomies($post_type) {

    $taxonomies = array();

    if($post_type){
        $taxes = get_object_taxonomies($post_type, 'objects');
        if( is_array($taxes) ) {
            foreach( $taxes as $tax ) {
                $taxonomies[ $tax->name ] = $tax->label;
            }
        }
    }

    return $taxonomies;
}

// Get Taxonomy Terms
function contentlist_get_terms($taxonomy) {

    $terms = array();

    $trms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));

    if(!empty($trms) && is_array($trms)){
        foreach($trms as $t){
            $terms[ $t->slug ] = $t->name;
        }
    }

    return $terms;
}

// Load Post Types
function contentlist_load_post_types( $field ) {

    $field['choices'] = array();

    $post_types = get_post_types(array(
        'public' => true
    ), 'objects');

    if( is_array($post_types) ) {
        $post_types = array_filter($post_types, function($pt){ return $pt->name != 'attachment'; });
        foreach( $post_types as $pt ) {
            $field['choices'][ $pt->name ] = $pt->label;
        }
    }

    return $field;
}
add_filter('acf/load_field/key=field_632b2cf65729b', 'contentlist_load_post_types');

// Load Taxonomies
function contentlist_load_taxonomies( $field ) {
    $post_type = get_field('contentlist_query_post_type');
    if($post_type){
        $field['choices'] = contentlist_get_taxonomies($post_type);
    }
    return $field;
}
add_filter('acf/load_field/key=field_632b30575729c', 'contentlist_load_taxonomies');

// Load Terms
function contentlist_load_terms( $field ) {
    $taxonomy = get_field('contentlist_query_taxonomies');
    if($taxonomy){
        $field['choices'] = contentlist_get_terms($taxonomy);
    }
    return $field;
}
add_filter('acf/load_field/key=field_632b307f5729d', 'contentlist_load_terms');

// Ajax Field Populator
function contentlist_field_populator()
{
    $nonce   = !empty($_POST['nonce'])   ? $_POST['nonce']   : false;
    $command = !empty($_POST['command']) ? $_POST['command'] : false;
    $options = !empty($_POST['options']) ? $_POST['options'] : [];
    
    if(wp_verify_nonce($nonce, 'contentlist_query')){

        switch($command){

            case 'get_taxonomies':
                if(empty($options['post_type'])){
                    wp_send_json_error('missing_post_type_option');
                }
                $post_type = $options['post_type'];
                $data = contentlist_get_taxonomies($post_type);
                wp_send_json_success($data);
            break;

            case 'get_terms':
                if(empty($options['taxonomy'])){
                    wp_send_json_error('missing_taxonomy_option');
                }
                $taxonomy = $options['taxonomy'];
                $data = contentlist_get_terms($taxonomy);
                wp_send_json_success($data);
            break;

            default:
                wp_send_json_error('invalid_command');
            break;
        }

    } else {
        wp_send_json_error('nonce_failure');
    }
}
add_action("wp_ajax_contentlist_query", 'contentlist_field_populator');


// Perform Parameterized Query
function contentlist_query($post_type, $taxonomy, $term, $post_count, $word_count)
{
    $items = [];

    $args = [
        'post_status' => 'publish'
    ];

    if($post_type){
        $args['post_type'] = $post_type;
    }

    if($taxonomy && $term){ 
        $args['tax_query'] = [ [
            'taxonomy' => $taxonomy,
            'field' => 'slug',
            'terms' => [ $term ]
        ] ];
    }

    if($post_count){
        $args['posts_per_page'] = $post_count;
    }

    $excerpt_word_count_adjuster = function ($length) use ($word_count) { return $word_count; };
    $excerpt_clean_more_link     = function ($more) { return '.'; };

    add_filter('excerpt_more', $excerpt_clean_more_link, 999);
    if($word_count){ add_filter( 'excerpt_length', $excerpt_word_count_adjuster, 999 ); }

    $query = new \WP_Query($args);

    if($word_count){ remove_filter('excerpt_length', $excerpt_word_count_adjuster); }
    remove_filter('excerpt_more', $excerpt_clean_more_link);

    $items = $query->posts;

    return $items;

}

function output_article_meta($readTime = "", $categories = "", $author = "", $seperator = " | "){
    
    $meta_array = array($readTime, $categories, $author);
    $meta_array = array_filter($meta_array);
    $meta_array = apply_filters('braftonium_list_article_meta_array', $meta_array, $seperator );
    return join($seperator, $meta_array);
}