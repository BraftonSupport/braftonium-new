<?php 

    //Setup Widget fields
   add_action('acf/init', 'braftonium_manage_widgets_init');
   function braftonium_manage_widgets_init(){
       acf_add_options_page(array(
           'page_title' 	=> 'Widgets Areas',
           'menu_title'	=> 'Widgets Areas',
           'menu_slug' 	=> 'braftonium-manage-widgets',
           'capability'	=> 'edit_posts',
           'redirect'		=> false,
           'parent_slug'   => 'braftonium-settings'
           )
       );
       acf_add_local_field_group(array(
           'key' => 'group_braftonium_manage_widgets',
           'title' => 'Widgets Areas',
           'fields' => array(
               array(
                   'key' => 'field_braftonium_manage_widgets',
                   'label' => __( "Widget Areas", "braftonium" ),
                   'name' => 'braftonium_manage_widgets',
                   'type' => 'repeater',
                   'required' => 0,
                   'conditional_logic' => 0,
                   'instructions' => __( 'You can create new widgets here, which you can then work on in the default widget page.', 'braftonium' ),
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
       ));
       acf_add_local_field( array (
            'key'            => 'widget_name',
            'label'          => 'Name',
            'name'           => 'widget_name',
            'parent'         => 'field_braftonium_manage_widgets',
            'type'           => 'text',
            'placeholder'    => '',
            'required'       => 1,
            'wrapper' => array(
                'width' => '60',
                'class' => '',
                'id' => '',
            ),
        ));
        acf_add_local_field( array (
            'key'            => 'widget_class',
            'label'          => 'Class',
            'name'           => 'widget_class',
            'parent'         => 'field_braftonium_manage_widgets',
            'type'           => 'text',
            'placeholder'    => '',
            'required'       => 0,
            'wrapper' => array(
                'width' => '20',
                'class' => '',
                'id' => '',
            ),
        ));
        acf_add_local_field( array (
            'key'            => 'widget_id',
            'label'          => 'ID',
            'name'           => 'widget_id',
            'parent'         => 'field_braftonium_manage_widgets',
            'type'           => 'text',
            'placeholder'    => '',
            'required'       => 0,
            'wrapper' => array(
                'width' => '20',
                'class' => '',
                'id' => '',
            ),
        ));
        acf_add_local_field( array (
            'key'            => 'widget_description',
            'label'          => 'Description',
            'name'           => 'widget_description',
            'parent'         => 'field_braftonium_manage_widgets',
            'type'           => 'textarea',
            'placeholder'    => '',
            'required'       => 0,
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
                    $name=$widget['widget_name'];
                    //get/create id, class and description
                    $id=$widget['widget_id'] ? $widget['widget_id'] && $widget['widget_id']!=='' : 'braftonium-widget-'.str_replace('_','-',str_replace(' ','-',strtolower($name)));
                    $class=$widget['widget_class'] ? $widget['widget_class'] && $widget['widget_class']!=='' : 'braftonium-widget-'.$id;                    
                    $description=$widget['widget_description'] ? $widget['widget_description'] && $widget['widget_description']!=='': 'Braftonium widget';

                    register_sidebar(array(
                        'name'		  => __( ucfirst($name), 'braftonium' ),//make first letter capital
                        'id'			=> $id,
                        'description'   => __( $description, 'braftonium' ),
                        'before_widget' => '<div class="'.$class.'">',
                        'after_widget'  => '</div>',
                    ));
                }
            }    
    }
?>