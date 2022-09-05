<?php 
    //Use ACF fields to create template overrides which can be audience, url and/or template specific
    //the class {dev-template} is automatically added to the template to allow custom styling   
    //NOTE
    //A swopped template will have the same classes as the original template, with 'dev-template' class added.
       
    function getOverrideOptions($template){
            global $current_user;   //Need to confirm audience
            global $wpdb;           //Need for SQL queries
            global $post;           //To check Page/Post Name
            
            //Total amount of rules
            $optionCount = $wpdb->get_results("SELECT COUNT(option_name) AS count FROM {$wpdb->prefix}options WHERE option_name LIKE 'options_braftonium_template_page_%_template_override_val'");
            
            //Loop through each rule and get values
            for($optionCounter=0;$optionCounter<$optionCount[0]->count;$optionCounter++){
                    $fieldsForOption = $wpdb->get_results( "SELECT option_name,option_value FROM {$wpdb->prefix}options WHERE option_name LIKE 'options_braftonium_template_page_".$optionCounter."%'", OBJECT );            
                
                //Search & prepare keys
                    $search=array_search('options_braftonium_template_page_'.$optionCounter.'_template_override_audience', array_column($fieldsForOption, 'option_name'));
                    $keys=array_column($fieldsForOption,'option_value','option_name');                   

                //Convert audience value to an array
                    $audience=[$keys[getTemplateValue($optionCounter,'audience')]];
                    if(strpos($audience[0],',')!==false){
                        $audience=explode(',',$audience[0]);                        
                    }

                //Set all values & confirm rule is live and for current user
                    $audienceCheck      = $audience[0]=='all' || in_array($current_user->user_login,$audience);
                    $live               = $keys[getTemplateValue($optionCounter,'disable')]=='';                    

                //Check if rule is live && is user is in the target audience
                if($live && $audienceCheck){

                    //Current page values | Template Name, Full URL & Page Name
                        $defaultTemplateName    = basename($template,'.php'); //removing .php
                        $currenUrl              = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $currentPageName        = $post->post_name;

                    //Template target to replace & new replacement template
                        $newTemplate            = $keys[getTemplateValue($optionCounter,'template')];
                        $targetToReplace        = $keys[getTemplateValue($optionCounter,'val')];
                        $newTemplate            = WP_CONTENT_DIR.$newTemplate;   
                    
                    //Check if current URL, Page Name/Template name are targeted
                        $isTargetUrl            = $currenUrl            ==  $targetToReplace;
                        $isPageName             = $post->post_name      ==  $targetToReplace;
                        $isTemplate             = $defaultTemplateName  ==  $targetToReplace;

                    //Swop Template and add dev-template class (If new template file does exist)
                        if(is_file($newTemplate) && ($isTargetUrl || $isPageName || $isTemplate)){

                            $template=$newTemplate;
                            add_filter( 'body_class', 'custom_class' );
                            function custom_class( $classes ) {
                                remove_filter('body_class','custom_class');
                                $classes[] = 'dev-template';
                                return $classes;
                            }
                        }
                }                
            }
        return $template;
    }
    add_filter('template_include', 'getOverrideOptions',1001);

    function getTemplateValue($count,$field){
        return 'options_braftonium_template_page_'.$count.'_template_override_'.$field;
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
                    'instructions' => __( 'Create a template redirect for a specific audience. Now you can develop without breaking a template everyone else sees and share it with other users.', 'braftonium' ),
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
            'label'          => 'Target to override',
            'name'           => 'template_override_val',
            'parent'         => 'field_braftonium_template_override',
            'type'           => 'text',
            'placeholder'    => 'filename.php/full-url/page name',
            'instructions' => __( 'Add the target template as a filename(no need for .php) or a specific full url', 'braftonium' ),
            'required'       => 1,
            'wrapper' => array(
                'width' => '80',
                'class' => '',
                'id' => '',
            ),
        ));
        acf_add_local_field( array (
            'key'            => 'template_override_disable_key',
            'label'          => 'Disable',
            'name'           => 'template_override_disable',
            'parent'         => 'field_braftonium_template_override',
            'type'           => 'checkbox',
            'choices' => array(
                'disable' => 'Yes',
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
            'label'          => 'New Template',
            'name'           => 'template_override_template',
            'parent'         => 'field_braftonium_template_override',
            'placeholder'    => '/themes/.. OR /plugins/..',
            'instructions' => __( 'The path will be added onto /wp-content. You can choose any theme/plugin.', 'braftonium' ),
            'type'           => 'text',
            'required'       => 1,
            'wrapper' => array(
                'width' => '',
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
            'instructions' => __( 'You can set all to switch the template for any user OR specific user/s... all or username csv(without spaces): {user_1,dev,some_other_person,etc}', 'braftonium' ),
            'placeholder'   => esc_html(wp_get_current_user()->user_login ).'/all/'.esc_html(wp_get_current_user()->user_login ).',username1,username2',
            'default_value'   => esc_html(wp_get_current_user()->user_login ),
            'required'       => 1,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ));
    }
?>