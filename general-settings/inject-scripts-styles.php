<?php
    //Enqueue options for scripts include Defer & Async

    //Initialize Global(Settings page) & Local(Every post & Page)
    add_action('acf/init', 'braftonium_injector_init');
    function braftonium_injector_init(){
        acf_add_options_page(array(
            'page_title' 	=> 'Scripts & Styles',
            'menu_title'	=> 'Scripts & Styles',
            'menu_slug' 	=> 'braftonium-injector',
            'capability'	=> 'edit_posts',
            'redirect'		=> false,
            'parent_slug'   => 'braftonium-settings'
            )
        );
        acf_add_local_field_group(array(
             'key' => 'group_braftonium_injector',
             'title' => 'Scripts & Styles',
             'fields' => array(
                 array(
                     'key' => 'field_braftonium_injectors',
                     'label' => __( "Rules", "braftonium" ),                     
                     'name' => 'braftonium_injector',
                     'type' => 'repeater',
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
                         'value' => 'braftonium-injector',
                     ),
                ),
                array (
                    array (
                       'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                ),
                array (
                    array (
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
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
            'key'            => 'braftonium_injector_location',
            'label'          => 'Location',
            'name'           => 'location',
            'parent'         => 'field_braftonium_injectors',
            'type'           => 'radio',           
            'required'       => 1,
            'wrapper' => array(
                'width' => '20',
                'class' => '',
                'id' => '',
            ),
            'choices'   => array(
                'header'	                => 'Header',
                'footer'                    => 'Footer',
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'braftonium-injector',
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
            'key'            => 'braftonium_injector_method',
            'label'          => 'Method',
            'name'           => 'inject_method',
            'parent'         => 'field_braftonium_injectors',
            'type'           => 'select',
            'placeholder'    => 'Make sure to wrap your content with either: <style> OR <script>',            
            'required'       => 1,
            'wrapper' => array(
                'width' => '40',
                'class' => '',
                'id' => '',
            ),
            'choices'   => array(
                'css'                           => 'CSS',                   //footer
                'stylesheet'                    => 'Stylesheet',            //footer
                'js'                            => 'JS',                    //enqueue
                'js_script'	                    => 'JS (script)',           //enqueue
                'js_script_async'               => 'JS (async)',            //enqueue
                'js_script_defer'               => 'JS (defer)',            //enqueue                       
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'braftonium-injector',
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
            'key'            => 'braftonium_injector_disabled',
            'label'          => 'Disable Rule',
            'name'           => 'html_disable',
            'parent'         => 'field_braftonium_injectors',
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
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'braftonium-injector',
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
            'key'            => 'braftonium_injector_id',
            'label'          => 'ID',
            'name'           => 'script_id',
            'parent'         => 'field_braftonium_injectors',
            'type'           => 'text',
            'required'       => 0,
            'wrapper' => array(
                'width' => '20',
                'class' => '',
                'id' => '',
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'braftonium-injector',
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
            'conditional_logic' => array(
				array(
					array(
						'field' => 'field_6306472f9ea20',
						'operator' => '!=',
						'value' => 'css',
					),
				),
				array(
					array(
						'field' => 'field_6306472f9ea20',
						'operator' => '!=',
						'value' => 'js',
					),
				),
			),
        ));
        acf_add_local_field( array (
            'key'            => 'braftonium_injector_text',
            'label'          => 'Inject this',
            'name'           => 'html_value',
            'parent'         => 'field_braftonium_injectors',
            'type'           => 'textarea',
            'instructions' => __( 'Either input the URL, JS/CSS you want to inject.', 'braftonium' ),
            'placeholder'    => 'URL OR just write your css/js.',
            'required'       => 1,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'braftonium-injector',
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
    }

    function braftonium_inject(){   
        //Create list of rules
            $injections=get_field('field_braftonium_injectors', 'option') ? get_field('field_braftonium_injectors', 'option') : array();
            global $post;
            $localInjections=get_field('field_braftonium_injectors', $post->ID);
            if(get_field('field_braftonium_injectors', 'option')){
                $injections=array_merge(get_field('field_braftonium_injectors', 'option'),$injections);
            }

            $scriptCounter=0;
            foreach($injections as $rule){
                $scriptCounter=$scriptCounter+1;
                if($rule['html_disable']!='disable'){ //skip disabled rules
                    $method=$rule['inject_method'];
                    $content=$rule['html_value'];
                    if($method=='css'){
                        echo '<style>'.$content.'</style>';
                    } elseif($method=='stylesheet'){
                        wp_enqueue_style( $rule['script_id'] , $content, NULL, NULL, $rule['location'] && $rule['location']=='footer');
                    } elseif($method=='js'){
                        echo '<script>'.$content.'</script>';
                    } elseif($method=='js_script'){
                        wp_enqueue_script( $rule['script_id'] , $content, NULL, NULL, $rule['location'] && $rule['location']=='footer');
                    } elseif($method=='js_script_defer'){
                        wp_enqueue_script( $rule['script_id'], $content, NULL, NULL, $rule['location'] && $rule['location']=='footer');
                        wp_script_add_data( $rule['script_id'] , 'defer', true );
                    } elseif($method=='js_script_async'){
                        wp_enqueue_script( $rule['script_id'], $content, NULL, NULL, $rule['location'] && $rule['location']=='footer');
                        wp_script_add_data( $rule['script_id'] , 'async', true );
                    }
                }            
            }        
    }
    add_action('wp_enqueue_scripts', 'braftonium_inject');
    ?>