<?php 
    //Copy this file and rename it using "-" to seperate words and must end "-pattern.php"  
    //Edit as needed

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)),  //braftonium/example-pattern
      [
        'title' => __('Banner Left', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Banner with left aligned content',
          'Banner with left aligned content',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:acf/banner {"id":"block_633590fc17a2f","name":"acf/banner","data":{"field_632ae933c0131":"","field_632aebb1c0138":"rgba(221,130,130,0.38)","field_632aff5701e57":"","field_633594808c6e9":"left","field_6335968c6d2f4":"1200px"},"align":"full","mode":"preview"} -->
        <!-- wp:heading -->
        <h2>Heading</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph -->
        <p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry.</p>
        <!-- /wp:paragraph -->
        <!-- /wp:acf/banner -->',        
      ]
    );      
?>