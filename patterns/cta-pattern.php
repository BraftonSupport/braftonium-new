<?php 
    //Copy this file and rename it using "-" to seperate words and must end "-pattern.php"  
    //Edit as needed

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)),  //braftonium/example-pattern
      [
        'title' => __('Brafton CTA Block', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Brafton CTA Block',
          'Banner with left aligned content',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:acf/cta {"id":"block_637e7929605ff","name":"acf/cta","data":{"field_634727da9eda4":"","field_634727daa6162":"0"},"align":"","mode":"preview"} -->
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading -->
        <h2></h2>
        <!-- /wp:heading -->
        <!-- wp:paragraph -->
        <p></p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:buttons -->
        <div class="wp-block-buttons"><!-- wp:button /--></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        <!-- /wp:acf/cta -->',        
      ]
    );      
?>