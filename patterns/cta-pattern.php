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
        '<!-- wp:acf/cta {"name":"acf/cta","data":{"field_634727da9eda4":"","field_634727daa6162":"1"},"mode":"preview","className":"is-style-basic-cta","backgroundColor":"pale-cyan-blue","availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}],"classesFetched":true} -->
        <!-- wp:columns {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"},{"label":"Increase Top Padding","value":"increase-top-spacing"},{"label":"Increase Bottom Padding","value":"increase-bottom-spacing"},{"label":"Increase Padding","value":"increase-spacing"}]} -->
        <div class="wp-block-columns"><!-- wp:column {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-column"><!-- wp:heading {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <h2>Heading Goes Here</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <p>Content Goes here</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-column"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-buttons"><!-- wp:button {"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <div class="wp-block-button"><a class="wp-block-button__link wp-element-button">CTA Button</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        <!-- /wp:acf/cta -->',        
      ]
    );      
?>