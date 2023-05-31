<?php 
    //Copy this file and rename it using "-" to seperate words and must end "-pattern.php"  
    //Edit as needed

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)),  //braftonium/example-pattern
      [
        'title' => __('Banner', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Banner with configurable content alignment an doptional background Image',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:acf/banner {"name":"acf/banner","data":{"field_632ae933c0131":"","field_632aebb1c0138":"rgba(55,76,239,0.4)","field_633594808c6e9":"left"},"align":"full","mode":"preview","availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}],"classesFetched":true} -->
        <!-- wp:heading {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <h2>Heading</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <p>industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>
        <!-- /wp:paragraph -->
        <!-- /wp:acf/banner -->',        
      ]
    ); 
    
?>