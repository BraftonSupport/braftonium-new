<?php 
    //Use ACF fields to create template overrides which can be audience, url and/or template sepecific
    //the class override is automatically added to the template to allow custom styling   
    //NOTE
    //A swopped template will have the same classes as the original template, with 'template-swopped' class added.

    // Outputs an array of all plugins.
        
    //getOverrideOptions();
    function getOverrideOptions($template){
            $allSwopsOptions=array();
        
        //Get all Title Values they have _0_/_1_ values with their fields corresponding
            global $wpdb;
            /*USE COUNT*/
            $swops = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 'options_braftonium_template_page_%_template_override_val'", OBJECT );
        
        foreach($swops as $swop){
            //Clean result to get integer value
                $key=str_replace('_template_override_val','',str_replace('options_braftonium_template_page_','',$swop->option_name));
            
            //Get all values for this (0,1 etc)
                $allFields = $wpdb->get_results( "SELECT option_name,option_value FROM {$wpdb->prefix}options WHERE option_name LIKE 'options_braftonium_template_page_".$key."%'", OBJECT );            
            
            //Loop through fields and clean their keys
                $disable=false;
                $audience=array();
                $targetTemplate='';
                $newTemplate='';

                foreach($allFields as $field){                    
                    //remove unnecessary text from option_name
                    $field->option_name=explode('template_override_',$field->option_name)[1];                    
                    if($field->option_name == 'audience'){
                        $audience=[$field->option_value];
                        if(strpos($audience[0],',')!==false){
                            $audience=explode(',',$audience[0]);
                        }
                    } elseif($field->option_name == 'val'){
                        $targetTemplate=$field->option_value;
                    } elseif($field->option_name == 'template'){
                        $newTemplate=$field->option_value;
                    } elseif($field->option_name == 'disable'){
                        $disable=$field->option_value!='';
                    }
                }

                if(!$disable){
                    $defaultTemplate=checkOverride($targetTemplate, $audience, $newTemplate, $template);
                }
        }
        return $template;
    }
    add_filter('template_include', 'getOverrideOptions',1001);

    function checkOverride($targetTemplate, $audience, $newTemplate, $template){
        //Original template
        $defaultTemplate=basename($template,'.php'); //removing .php
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        //Get current post name, incase that is what was supplied
        global $post;
        $currentPageName=$post->post_name;

        //Get current user to cross check if user should see an overidden template
        global $current_user;
        $currentUser=$current_user->user_login;

        //Set main directory wp-content
        $templateDir=WP_CONTENT_DIR;

        if(($url==$targetTemplate || $currentPageName==$targetTemplate || $defaultTemplate==$targetTemplate)){
            if($audience=='all' || in_array($currentUser,$audienceList)){
                $newTemplate=$templateDir.'/'.$newTemplate;
                
                if(is_file($newTemplate)){
                    $template=$newTemplate;
                    add_filter( 'body_class', 'custom_class' );
                    function custom_class( $classes ) {
                        $classes[] = 'template-swopped';
                        return $classes;
                    }
                }
            }
        }
        return $template;
    }

    //Template Swopper Fields
    add_action('acf/init', 'braftonium_template_overide_init');
    function braftonium_template_overide_init(){
        acf_add_options_page(array(
            'page_title' 	=> 'Template Swopper',
            'menu_title'	=> 'Template Swopper',
            'menu_slug' 	=> 'template-swopper',
            'capability'	=> 'edit_posts',
            'redirect'		=> false,
            'parent_slug'   => 'braftonium-settings'
            )
        );
        acf_add_local_field_group(array(
            'key' => 'group_braftonium_template_override',
            'title' => 'Template Swopper',
            'fields' => array(
                array(
                    'key' => 'field_braftonium_template_override',
                    'label' => __( "Template Overrides", "braftonium" ),
                    'name' => 'braftonium_template_page',
                    'type' => 'repeater',
                    'instructions' => __( 'Create a template redirect. This will let you switch a template file for a specific audience. Now you can develop without breaking a template everyone else sees.', 'braftonium' ),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'layout' => 'block'
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'template-swopper',
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
            'key'            => 'template_override_val_key',
            'label'          => 'Template to override',
            'name'           => 'template_override_val',
            'parent'         => 'field_braftonium_template_override',
            'type'           => 'text',
            'placeholder'    => 'filename/url',
            'instructions' => __( 'You can add the target template as a filename(no need for .php) or a specific full url', 'braftonium' ),
            'required'       => 1,
            'wrapper' => array(
                'width' => '80',
                'class' => '',
                'id' => '',
            ),
        ));
        acf_add_local_field( array (
            'key'            => 'template_override_disable_key',
            'label'          => 'Disable Swop',
            'name'           => 'template_override_disable',
            'parent'         => 'field_braftonium_template_override',
            'type'           => 'checkbox',
            'choices' => array(
                'disable' => 'Disable',
            ),
            'required'       => 0,
            'wrapper' => array(
                'width' => '20',
                'class' => '',
                'id' => '',
            ),
        ));
        acf_add_local_field( array (
            'key'            => 'template_override_template_key',
            'label'          => 'Template File Name',
            'name'           => 'template_override_template',
            'parent'         => 'field_braftonium_template_override',
            'placeholder'    => '/themes/ OR /plugins/',
            'instructions' => __( 'The path will be added onto /wp-content. Use /themes OR /plugins to access your new template.', 'braftonium' ),
            'type'           => 'text',
            'required'       => 1,
            'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
            ),
        ));
        acf_add_local_field( array (
            'key'            => 'template_override_audience_key',
            'label'          => 'Audience',
            'name'           => 'template_override_audience',
            'parent'         => 'field_braftonium_template_override',
            'type'           => 'text',
            'instructions' => __( 'You can set all(logged out users included) OR specific user/s... For all users {all}, for only unique users: {user_1,dev,some_other_person,etc}', 'braftonium' ),
            'placeholder'   => esc_html(wp_get_current_user()->user_login ).'/all/'.esc_html(wp_get_current_user()->user_login ).',username1,username2',
            'default_value'   => esc_html(wp_get_current_user()->user_login ),
            'required'       => 1,
            'wrapper' => array(
                'width' => '40',
                'class' => '',
                'id' => '',
            ),
        ));
    }
?>