<?php
    $name='example';
    $title='Example';

    acf_register_block_type(array(
        'name'			    => $name,
        'title'			    => __($title),   
        'description'       => __('Custom example block'),
        'render_template'	=> dirname(__FILE__).'/'.basename(dirname(__FILE__)).".html.php",
        'enqueue_assets'    => function (){
            //auto enqueue assets with -auto
            braftonium_enqueue_assets(__file__);

            //OR add them manually
            //wp_enqueue_style(basename(dirname(__FILE__)).'-css', plugin_dir_url(__FILE__).'/example.css');
            wp_enqueue_script(basename(dirname(__FILE__)).'-js', plugin_dir_url(__FILE__).'/example.js');
        },
        'category'          => 'braftonium',
        'mode'			    => 'auto',        
        'supports'		    => [//Options - https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/
                'align'			    => [ 'left', 'right', 'full' ],
                'anchor'		    => true,
                'customClassName'	=> true,
                'jsx' 			    => true,
                'color'             => [
                        'background'            => true,
                        'gradients'             => true,
                        'text'                  => true,
                        '__experimentalDuotone' => '> .duotone-img, > .duotone-video',
                ],
                'spacing'           => [
                        'margin'                => true,
                        'padding'               => ['top','bottom'],
                        'blockGap'              => true,
                ],
                'typography'        => [
                        'fontSize'              => true,
                        'lineHeight'            => true,
                ]
        ],        
    ));  
    
    acf_add_local_field_group(array (
        'key' => 'group_braftonium_'.$name,
        'title' => $title,
        'fields' => json_decode(file_get_contents(plugin_dir_url(__FILE__).'/fields.json'), true),
        'location' => array (
            array (
                array (
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/'.$name,
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
?>