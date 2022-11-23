<?php 
    //Copy this file and rename it using "-" to seperate words and must end "-pattern.php"  
    //Edit as needed

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)).'-center',  //braftonium/example-pattern
      [
        'title' => __('Banner Center', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Banner with centered content',
          'Banner with centered content',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:acf/banner {"id":"block_637e279d954da","name":"acf/banner","data":{"field_632ae933c0131":"","field_632aebb1c0138":"rgba(221,130,130,0.38)","field_632aff5701e57":"","field_633594808c6e9":"left","field_6335968c6d2f4":"1200px"},"align":"full","mode":"preview"} -->
        <!-- wp:heading {"textAlign":"center"} -->
        <h2 class="has-text-align-center">Heading</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry.</p>
        <!-- /wp:paragraph -->
        <!-- /wp:acf/banner -->',        
      ]
    ); 
    
    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)).'-left',  //braftonium/example-pattern
      [
        'title' => __('Banner Left', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Banner with left aligned content',
          'Banner with left aligned content',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:acf/banner {"id":"block_633590fc17a2f","name":"acf/banner","data":{"field_632ae933c0131":"","field_632aebb1c0138":"rgba(85,145,143,0.34)","field_633594808c6e9":"left"},"align":"full","mode":"preview"} -->
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading -->
        <h2>Heading</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph -->
        <p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry.</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        <!-- /wp:acf/banner -->',        
      ]
    ); 

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)).'-right',  //braftonium/example-pattern
      [
        'title' => __('Banner Right', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Banner with right aligned content',
          'Banner with right aligned content',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:acf/banner {"id":"block_633590fc17a2f","name":"acf/banner","data":{"field_632ae933c0131":"","field_632aebb1c0138":"rgba(221,153,51,0.51)","field_633594808c6e9":"left"},"align":"full","mode":"preview"} -->
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading -->
        <h2>Heading</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph -->
        <p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry.</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        <!-- /wp:acf/banner -->',        
      ]
    ); 
?>