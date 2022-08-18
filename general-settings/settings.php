<?php     
    //Note - Everything here will be in the backend "Braftonium" Options page

    //Setup general options page
        add_action('acf/init', 'braftonium_settings');
        function braftonium_settings() {
            if( function_exists('acf_add_options_page') ) {
                acf_add_options_page(array(
                    'page_title' 	=> 'General Settings',
                    'menu_title'	=> 'Braftonium',
                    'menu_slug' 	=> 'braftonium-settings',
                    'capability'	=> 'edit_posts',
                    'redirect'		=> false,
                    )
                ); 

                braftonium_general_settings_group();                 
            }
        }

    //DEBUG will only be turned on for administrators - FONTS AWESOME
        function braftonium_general_settings_group(){
            acf_add_local_field_group(array (
                'key' => 'group_braftonium_settings',
                'title' => 'General',
                'fields' => array (
                    array(
                        'key' => 'field_braftonium_settings_1',
                        'label' => 'Turn Debug On',
                        'name' => 'debug-on',
                        'type' => 'checkbox',
                        'instructions' => 'Debug Mode will only be turned for for admin users. Anyone who is not administrator will not view debug feedback. ',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'choices' => array(
                            'on'	=> 'On'
                        ),
                        'return_format' => 'value'
                    ),
                    array(
                        'key' => 'field_braftonium_settings_2',
                        'label' => 'Use Fonts Awesome',
                        'name' => 'fonts-awesome',
                        'type' => 'checkbox',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'choices' => array(
                            'on'	=> 'On'
                        ),
                        'return_format' => 'value'
                    ),
                    array(
                        'key' => 'field_braftonium_settings_3',
                        'label' => 'Admin Override',
                        'name' => 'admin-override',
                        'type' => 'text',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'instructions' => 'Enter an admin email address here to set the main administrator, without needing a confirmation email',
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'choices' => array(
                            'on'	=> 'On'
                        ),
                        'return_format' => 'value'
                    ),
                ),
                'location' => array (
                    array (                
                        array (
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'braftonium-settings',
                        ),               
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
            ));

            if(get_field('debug-on', 'option')!==null && get_field('debug-on', 'option')[0]=='on' && current_user_can('administrator')){
                error_reporting( E_ALL );
                ini_set( 'display_errors', 1 );
            } 
            
            if(get_field('admin-override', 'option')!=null && get_field('admin-override', 'option')!=''){
                $currentAdminEmail=get_option('admin_email');
                $adminEmailOverride=get_field('admin-override', 'option');
                
                if($currentAdminEmail!=$adminEmailOverride){
                    update_option('admin_email',$adminEmailOverride);
                    update_option('new_admin_email',$adminEmailOverride);
                }
            } 

            if(count(get_field('fonts-awesome', 'option'))>0 && get_field('fonts-awesome', 'option')[0]=='on'){
                wp_enqueue_style( 'braftonium-fonts-awesom','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
            } 
        }  

    //Each file is kind of self explanatory, open the specific php file for more info
        include('custom-posts.php');
        include('template-overider.php');
        include('manage-widgets.php');
        include('inject-scripts-styles.php');
?>