<?php 
    //Copy this file and rename it using "-" to seperate words and must end "-pattern.php"  
    //Edit as needed

    register_block_pattern(
      'braftonium/'.str_replace('.php','',basename(__FILE__)),  //braftonium/example-pattern
      [
        'title' => __('Content List Grid', 'textdomain'),         //rename title
        'description' => _x(                                    //customize descripiton
          'Your pattern description',
          'Block pattern description',
          'textdomain'
        ),
        'categories' => ['braftonium'],                         //don't edit this
        'content'   =>                                          //paste the pattern you copied into here
        '<!-- wp:acf/custom-row {"name":"acf/custom-row","data":{"background_image":"","_background_image":"field_633476053c2a0","full_width":"1","_full_width":"field_63347b82a1eb6"},"mode":"preview","className":" increase-spacing","backgroundColor":"white","braftoniumClasses":[{"label":"Increase Padding","value":"increase-spacing"}],"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"},{"label":"Increase Top Padding","value":"increase-top-spacing"},{"label":"Increase Bottom Padding","value":"increase-bottom-spacing"},{"label":"Increase Padding","value":"increase-spacing"}],"classesFetched":true} -->
        <!-- wp:heading {"textAlign":"center"} -->
        <h2 class="has-text-align-center">Services</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:acf/custom-list {"name":"acf/custom-list","data":{"list_grid_columns_desktop":"3","_list_grid_columns_desktop":"field_6337006b074d3","list_grid_columns_tablet":"3","_list_grid_columns_tablet":"field_63370084074d4","list_grid_columns_mobile":"1","_list_grid_columns_mobile":"field_6337008e074d5","list_grid_row_gap":"5","_list_grid_row_gap":"field_6337009c074d6","list_grid_column_gap":"40","_list_grid_column_gap":"field_633700ac074d7","list_grid":"","_list_grid":"field_63370052074d2","full_width":"0","_full_width":"field_6336ee454c597","background_image":"","_background_image":"field_6336ee4544ec2"},"mode":"preview","availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <!-- wp:acf/custom-list-item {"name":"acf/custom-list-item","data":{"background_color":"","_background_color":"field_634ea89bc6377","item_expansion":"","_item_expansion":"field_634ecb9eff93a"},"mode":"preview","availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Shift Up 20%","value":"shift-20"},{"label":"Shift Up 50%","value":"shift-50"}]} -->
        <!-- wp:image {"align":"center","id":39,"sizeSlug":"large","linkDestination":"none"} -->
        <figure class="wp-block-image aligncenter size-large"><img src="https://brafton_demo.designs.brafton.com/wp-content/uploads/2022/11/Icon-awesome-coins.png" alt="Icon Awesome Coins" class="wp-image-39"/></figure>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":4,"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <h4 class="has-text-align-center"><strong>HeadinLorem ipsumg</strong></h4>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua lorem</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":"5px"}},"className":"is-style-outline"} -->
        <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" style="border-radius:5px">Read More</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons -->
        <!-- /wp:acf/custom-list-item -->
        
        <!-- wp:acf/custom-list-item {"name":"acf/custom-list-item","data":{"background_color":"","_background_color":"field_634ea89bc6377","item_expansion":"","_item_expansion":"field_634ecb9eff93a"},"mode":"preview"} -->
        <!-- wp:image {"align":"center","id":39,"sizeSlug":"large","linkDestination":"none"} -->
        <figure class="wp-block-image aligncenter size-large"><img src="https://brafton_demo.designs.brafton.com/wp-content/uploads/2022/11/Icon-awesome-coins.png" alt="Icon Awesome Coins" class="wp-image-39"/></figure>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":4} -->
        <h4 class="has-text-align-center"><strong>HeadinLorem ipsumg</strong></h4>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua lorem</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":"5px"}},"className":"is-style-outline"} -->
        <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" style="border-radius:5px">Read More</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons -->
        <!-- /wp:acf/custom-list-item -->
        
        <!-- wp:acf/custom-list-item {"name":"acf/custom-list-item","data":{"background_color":"","_background_color":"field_634ea89bc6377","item_expansion":"","_item_expansion":"field_634ecb9eff93a"},"mode":"preview"} -->
        <!-- wp:image {"align":"center","id":39,"sizeSlug":"large","linkDestination":"none"} -->
        <figure class="wp-block-image aligncenter size-large"><img src="https://brafton_demo.designs.brafton.com/wp-content/uploads/2022/11/Icon-awesome-coins.png" alt="Icon Awesome Coins" class="wp-image-39"/></figure>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":4,"availableClasses":[{"label":"Hide on Mobile","value":"mobile-hide"},{"label":"Remove Top and Bottom Padding","value":"remove-padding"},{"label":"Remove Top Padding","value":"remove-top-padding"},{"label":"Remove Bottom Padding","value":"remove-bottom-padding"}]} -->
        <h4 class="has-text-align-center"><strong>HeadinLorem ipsumg</strong></h4>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua lorem</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":"5px"}},"className":"is-style-outline"} -->
        <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" style="border-radius:5px">Read More</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons -->
        <!-- /wp:acf/custom-list-item -->
        <!-- /wp:acf/custom-list -->
        <!-- /wp:acf/custom-row -->',        
      ]
    );      
?>
