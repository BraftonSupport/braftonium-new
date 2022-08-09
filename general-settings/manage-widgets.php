<?php 

    //Setup Widget fields
   add_action('acf/init', 'braftonium_manage_widgets_init');
   function braftonium_manage_widgets_init(){
       acf_add_options_page(array(
           'page_title' 	=> 'Manage Widgets',
           'menu_title'	=> 'Manage Widgets',
           'menu_slug' 	=> 'braftonium-manage-widgets',
           'capability'	=> 'edit_posts',
           'redirect'		=> false,
           'parent_slug'   => 'braftonium-settings'
           )
       );
       acf_add_local_field_group(array(
           'key' => 'group_braftonium_manage_widgets',
           'title' => 'Manage Widgets',
           'fields' => array(
               array(
                   'key' => 'field_braftonium_manage_widgets',
                   'label' => __( "Custom Widgets", "braftonium" ),
                   'name' => 'braftonium_manage_widgets',
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
                       'value' => 'braftonium-manage-widgets',
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
            'key'            => 'widget_name',
            'label'          => 'Widget Name',
            'name'           => 'widget_name',
            'parent'         => 'field_braftonium_manage_widgets',
            'type'           => 'text',
            'placeholder'    => '',
            'required'       => 1,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ));

        //Check fields and create widgets
            $widgets=get_field('braftonium_manage_widgets', 'option');
            if(isset($widgets) && count($widgets)>0){
                foreach($widgets as $widget){

                    //Sanitize Id be lowercase without spaces or _
                    $id=str_replace('_','-',str_replace(' ','-',strtolower($widget['widget_name']))); 

                    register_sidebar(array(
                        'name'		  => __( ucfirst($widget['widget_name']), 'braftonium' ),//make first letter capital
                        'id'			=> $id,
                        'description'   => __( 'Braftonium widget', 'braftonium' ),
                        'before_widget' => '<div class="braftonium-widget-'.$id.'">',//custom class
                        'after_widget'  => '</div>',
                    ));
                }
            }    
    }
?>