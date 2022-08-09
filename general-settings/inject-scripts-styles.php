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
            'label'          => 'Method/Location',
            'name'           => 'html_location',
            'parent'         => 'field_braftonium_injectors',
            'type'           => 'select',
            'placeholder'    => 'Make sure to wrap your content with either: <style> OR <script>',            
            'required'       => 1,
            'wrapper' => array(
                'width' => '80',
                'class' => '',
                'id' => '',
            ),
            'choices'   => array(
                'header'	                => 'Header',
                'before_content'            => 'Before Content',
                'after_content'             => 'After Content',
                'footer'                    => 'Footer',
                'enqueque_style'            => 'Enqueque Style',
                'enqueque_script'           => 'Enqueque Script',
                'enqueque_script_defer'     => 'Enqueque Script (defer)',
                'enqueque_script_async'     => 'Enqueque Script (async)',
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
            'key'            => 'braftonium_injector_text',
            'label'          => 'HTML to inject',
            'name'           => 'html_value',
            'parent'         => 'field_braftonium_injectors',
            'type'           => 'textarea',
            'instructions' => __( 'If you are using enqueue to need to use the url for the file.', 'braftonium' ),
            'placeholder'    => 'URL(for enqueue) OR wrap your content with <style>/<script> tags',
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

    //Header
        function inject_into_header() {
            global $post;
            echo inject_loop(get_field('field_braftonium_injectors', $post->ID),'header');
            echo inject_loop(get_field('field_braftonium_injectors', 'option'),'header');
        }
        add_action('wp_head', 'inject_into_header');

    //Footer
        function inject_into_footer() {
            global $post;
            echo inject_loop(get_field('field_braftonium_injectors', 'option'),'footer');
            echo inject_loop(get_field('field_braftonium_injectors', $post->ID),'footer');
        }
        add_action('wp_foot', 'inject_into_footer');

    //Enqueue Normal Script/Style (NOT Async/Defer)
        function inject_enqueue() {
            global $post;
            $rules=get_field('field_braftonium_injectors', 'option');
            $counter=0;
            if(isset($rules) && $rules!=false && count($rules)>0){
                foreach($rules as $rule){                    
                    $counter.=1;
                    if($rule['html_location']=='enqueque_style'){                    
                        wp_enqueue_style( 'brafton-injection-'.$counter, $rule['html_value']);
                    } elseif(strpos('enqueque_script', $rule['html_location'])>-1){
                        wp_enqueue_script( 'brafton-injection-'.$counter.'-'.str_replace('_','-',$rule['html_location']), $rule['html_value']);
                    }
                }
            }

            $rules=(get_field('field_braftonium_injectors', $post->ID));
            $counter=0;
            if(isset($rules) && $rules!=false && count($rules)>0){
                foreach($rules as $rule){                    
                    $counter.=1;
                    if($rule['html_location']=='enqueque_style'){                    
                        wp_enqueue_style( 'brafton-injection-local-'.$counter, $rule['html_value']);
                    } elseif(strpos('enqueque_script', $rule['html_location'])>-1){
                        wp_enqueue_script( 'brafton-injection-local-'.$counter.'-'.str_replace('_','-',$rule['html_location']), $rule['html_value']);
                    }
                }
            }
        }
        add_action('wp_enqueue_scripts', 'inject_enqueue');

    //Enqueue Async/Defer Scripts
        function braftonium_inject_defer_async() {
            global $post;
            $defer = array();
            $async = array();
            $rules=get_field('field_braftonium_injectors', 'option');

            if(isset($rules) && $rules!=false && count($rules)>0){
                foreach($rules as $rule){
                    if(strpos($rule['html_location'],'defer')>-1){
                        echo '<script src="' . $rule['html_value'] . '" defer="defer" type="text/javascript"></script>';                       
                    } elseif(strpos($rule['html_location'],'async')>-1){
                        echo '<script src="' . $rule['html_value'] . '" type="text/javascript" async></script>';              
                    }
                }
            }

            $rules=get_field('field_braftonium_injectors', $post->ID);
            if(isset($rules) && $rules!=false && count($rules)>0){
                foreach($rules as $rule){
                    if(strpos($rule['html_location'],'defer')>-1){
                        echo '<script src="' . $rule['html_value'] . '" defer="defer" type="text/javascript"></script>';                       
                    } elseif(strpos($rule['html_location'],'async')>-1){
                        echo '<script src="' . $rule['html_value'] . '" type="text/javascript" async></script>';              
                    }
                }
            }
        } 
        add_filter( 'wp_body_open', 'braftonium_inject_defer_async', 10, 3 );

    //Before Content       
        function inject_before_content($content){
            global $post;
            echo inject_loop(get_field('field_braftonium_injectors', $post->ID),'before_content');
            echo inject_loop(get_field('field_braftonium_injectors', 'option'),'before_content');        
        }
        add_filter( 'wp_body_open', 'inject_before_content', 1 );

    //After Content Content
        function inject_after_content($content){
            global $post;
            echo inject_loop(get_field('field_braftonium_injectors', $post->ID),'after_content');
            echo inject_loop(get_field('field_braftonium_injectors', 'option'),'after_content');        
        }
        add_filter( 'wp_body_after', 'inject_after_content', 1 );
    
    //Scan through loops and confirm the rules are met and inject <script> | <style> tagged rules
        function inject_loop($rules,$condition){
            $injections='';
            if(isset($rules) && $rules!=false && count($rules)>0){
                foreach($rules as $rule){
                    if($rule['html_location']==$condition){                    
                        $injections.=$rule['html_value'];
                    }
                }
            }
            return $injections;
        }
?>