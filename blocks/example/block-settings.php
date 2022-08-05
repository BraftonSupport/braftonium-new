<?php
    /*  This file needs to be in every block folder and MUST aways have the same name
    *   Settings will be saved as a variable which uses the same name as the block folder, but all "-" becomes "_"
    *   
    *   Required:
    *    1. Block Name
    *    2. Block Description
    *    3. Fields
    */

    $GLOBALS[str_replace('_','-',basename(__DIR__)).'_settings']=array(
        'name'          => 'Example New', //Change this to your name of choice
        'description'   => 'Description', //Change to a custom description
        'fields'        =>  array(  //Delete the demo field and add your own
            array(//becomes field_braftonium_example_1. Always use brafton_field_name | function is in register-blocks.php
                'key' => brafton_field_name(basename(__DIR__)).'1', 
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
        )
    );

    //This is an optional function, if present the code inside will run once the block is all setup
    //uses the same name as the block folder, but all "-" becomes "_": example_init/new_block_init
    function example_init(){
        
    }
?>