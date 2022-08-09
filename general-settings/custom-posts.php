<?php
    //Setup fields for backend options
        add_action('acf/init', 'braftonium_custom_posts');
        function braftonium_custom_posts() {
            acf_add_options_page(array(
                'page_title' 	=> 'Custom Posts',
                'menu_title'	=> 'Custom Posts',
                'menu_slug' 	=> 'custom-posts',
                'capability'	=> 'edit_posts',
                'redirect'		=> false,
                'parent_slug'   => 'braftonium-settings'
                )
            );
            acf_add_local_field_group(array(
                'key' => 'group_5z4z8d955ca61',
                'title' => 'Braftonium Plugin Options New',
                'fields' => array(
                    array(
                        'key' => 'field_custom_post_type_new',
                        'label' => __( "Custom Post Types", "braftonium" ),
                        'name' => 'custom_post_types_new',
                        'type' => 'repeater',
                        'instructions' => __( 'Adding post types will create the file tree. You can delete the files afterwards.', 'braftonium' ),
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'custom-posts',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));
            acf_add_local_field( array (
                'key'            => 'post_type_title_key',
                'label'          => 'Title',
                'name'           => 'post_type_title',
                'parent'         => 'field_custom_post_type_new',
                'type'           => 'text',
                'required'       => 1,
            ));
            acf_add_local_field( array (
                'key'            => 'post_type_options_key',
                'label'          => 'Options',
                'name'           => 'post_type_options',
                'parent'         => 'field_custom_post_type_new',
                'type'           => 'checkbox',
                'required'       => 0,
                'choices' => array(
                    'category' => __('Categories','braftonium'),
                    'post_tag' => __('Tags','braftonium'),
                ),
                'allow_custom' => true,
                'save_custom' => true,
            ));
            create_custom_post_types();
        }
    
    //If any custom post type exist, loop through them and create them
        function create_custom_post_types(){

            //get custom post options and start the loop
            $custom_post_types = get_field('custom_post_types_new', 'option');
            if(is_array($custom_post_types)){
                $postTypeSlugs = array();  
                
                if( $custom_post_types ):
                    foreach( $custom_post_types as $custom_post_type_item ):

                        //clean up data
                        $custom_post_type=$custom_post_type_item['post_type_title'];
                        $custom_post_slug = sanitize_html_class(strtolower(str_replace(' ', '-', $custom_post_type)));
                        $custom_post_santype = ucwords(str_replace('-', ' ', $custom_post_slug));
                        $custom_post_santype = ucwords(str_replace('_', ' ', $custom_post_santype));

                        //setup standard info
                        $posttypes_labels = array(
                            'name'				=> $custom_post_santype,
                            'singular_name'		=> $custom_post_santype,
                            'menu_name'			=> $custom_post_santype,
                            'add_new_item'		=> __( 'Add New', 'braftonium' ).' '.$custom_post_santype,
                        );

                        $posttypes_args = array(
                            'labels'			=> $posttypes_labels,
                            'menu_icon'			=> 'dashicons-star-filled',
                            'public'			=> true,
                            'capability_type'	=> 'page',
                            'has_archive'		=> true,
                            'show_in_rest'		=> true,
                            'rewrite'			=> array(
                                'with_front'	=> false
                            ),
                            'show_in_nav_menus' => true,
                            'hierarchical'		=> true,
                            'publicly_queryable'=> true,
                            'supports'			=> array('title', 'excerpt', 'editor', 'thumbnail', 'revisions', ),
                        );

                        //Add categories/tags if selected				
                        $taxonomies=array();
                        $customTaxonomies=array();
                        
                        if($custom_post_type_item['post_type_options']){
                            foreach($custom_post_type_item['post_type_options'] as $option){
                                if($option=='category' || $option=='post_tag'){
                                    array_push($taxonomies,$option);
                                } else {
                                    array_push($customTaxonomies,$option);
                                }
                            }				
                        }

                        if(!empty($taxonomies)){
                            $posttypes_args['taxonomies']=$taxonomies;
                        }

                        //register custom post type
                        $posttypes_args = apply_filters('braftonium_modify_custom_post_type',$posttypes_args, $custom_post_type);
                        register_post_type($custom_post_slug, $posttypes_args);

                        if(count($customTaxonomies)>0){
                            foreach($customTaxonomies as $tax){
                                $labels = array(
                                    'name' => _x( $tax.'s', 'taxonomy general name' ),
                                    'singular_name' => _x( $tax, 'taxonomy singular name' ),
                                    'search_items' =>  __( 'Search '.$tax ),
                                    'all_items' => __( 'All '.$tax.'s' ),
                                    'parent_item' => __( 'Parent '.$tax ),
                                    'parent_item_colon' => __( 'Parent '.$tax.':' ),
                                    'edit_item' => __( 'Edit '.$tax ), 
                                    'update_item' => __( 'Update '.$tax ),
                                    'add_new_item' => __( 'Add New '.$tax ),
                                    'new_item_name' => __( 'New '.$tax.' Name' ),
                                    'menu_name' => __( $tax.'s' ),
                                ); 	
                                
                                register_taxonomy(strtolower($tax), array($custom_post_slug), array(
                                    'hierarchical' => true,
                                    'labels' => $labels,
                                    'show_ui' => true,
                                    'show_admin_column' => true,
                                    'query_var' => true,
                                    'rewrite' => array( 'slug' => strtolower($tax) ),
                                ));
                            }				
                        }
                    endforeach;
                    flush_rewrite_rules();
                endif;
            }    
        }
?>