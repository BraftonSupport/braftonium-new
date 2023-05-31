<?php 
    //Copy this file and rename it using "-" to seperate words and must end "-pattern.php"  
    //Edit as needed

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)),  //braftonium/example-pattern
      [
        'title' => __('Standard Row Image Left', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Your pattern description',
          'Block pattern description',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:columns {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"},{"label":"Increase Top Padding","value":"increase-top-spacing"},{"label":"Increase Bottom Padding","value":"increase-bottom-spacing"},{"label":"Increase Padding","value":"increase-spacing"}],"classesFetched":true} -->
        <div class="wp-block-columns"><!-- wp:column {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-column"><!-- wp:image {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <figure class="wp-block-image"><img alt=""/></figure>
        <!-- /wp:image --></div>
        <!-- /wp:column -->
        
        <!-- wp:column {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-column"><!-- wp:heading {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <h2>Heading</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. </p>
        <!-- /wp:paragraph -->
        
        <!-- wp:buttons {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-buttons"><!-- wp:button {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-button"><a class="wp-block-button__link wp-element-button">CTA Text</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->',        
      ]
    );      
?>
