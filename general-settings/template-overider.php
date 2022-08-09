<?php 
    //Use ACF fields to create template overrides which can be audience, url and/or template sepecific
    //the class override is automatically added to the template to allow custom styling   
    //NOTE
    //A swopped template will have the same classes as the original template, with 'template-swopped' class added.

    function template_overider($template) {
        //Original template
        $defaultTemplate=basename($template,'.php'); //removing .php
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        //Get current post name, incase that is what was supplied
        global $post;
        $currentPageName=$post->post_name;

        //Get current user to cross check if user should see an overidden template
        global $current_user;
        $currentUser=$current_user->user_login;

        //This fires before ACF so SQL was used to grab the values. The results will contain 4 rows per redirect, one for each field
        global $wpdb;
        $repeaterResults = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 'options_braftonium_template_page_%'", OBJECT );
        
        //Set main directory wp-content
        $templateDir=WP_CONTENT_DIR;
        $newTemplate='';

        //Counter +4 as there are 4 results per repeater row
        for($itemBase=0;($itemBase+3)<count($repeaterResults);$itemBase+=4){             

            $targetTemplate=$repeaterResults[$itemBase+3]->option_value; //target template
            $disabled=$repeaterResults[$itemBase+1]->option_value; //url/filename
            $newTemplate=$repeaterResults[$itemBase+2]->option_value; //new template relative to /wp-content/

            //Check is this is the required URL/template name
            //1. Check using url/template name
            //2. Check page name
            //3. Check template name

            if(($url==$targetTemplate || $currentPageName==$targetTemplate || $defaultTemplate==$targetTemplate) && !isset($disabled)){

                //audience can either be a single user(eg. brafton_admin) or 
                //multiple CSV users without spaces (eg. brafton_admin,dev,another_user) or all
                    $audience=$repeaterResults[$itemBase+3]->option_value;                
                    $audienceList=[$currentUser];
                    if(strpos($audience,',')!==false){
                        $audienceList=explode(',',$audience);
                    }
                
                    //Check is the user should see the override template
                    if($audience=='all' || in_array($currentUser,$audienceList)){ //correct audience
                        
                        //Check if new template exists & swop them
                        $newTemplate=$templateDir.'/'.$newTemplate;

                        if(is_file($newTemplate)){
                            $template=$newTemplate;

                            //Add "override" class, allowing for custom styling
                            add_filter( 'body_class', 'custom_class' );
                            function custom_class( $classes ) {
                                $classes[] = 'template-swopped';
                                return $classes;
                            }
                        }
                    }
            }
        }
        return $template;
    }
    add_filter('template_include', 'template_overider',1000);

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
            'placeholder'    => '/wp-content..',
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
            'instructions' => __( 'You can set all(logged out users included) OR specific user/s.', 'braftonium' ),
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