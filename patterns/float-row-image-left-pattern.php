<?php 
    //Copy this file and rename it using "-" to seperate words and must end "-pattern.php"  
    //Edit as needed

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)),  //braftonium/example-pattern
      [
        'title' => __('Float Row Image Left', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Your pattern description',
          'Block pattern description',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:cover {"overlayColor":"primary","align":"full"} -->
<div class="wp-block-cover alignfull"><span aria-hidden="true" class="wp-block-cover__background has-primary-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size">Hi</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->',        
      ]
    );      
?>
