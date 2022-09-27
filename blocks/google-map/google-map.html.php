<!-- Main block layout - Must be have the name.html.php (example.html.php) -->
<?php  
    $classes                = array('google-map');   //use your own general class name
    $map = get_field('google_map');
    /*
        This example uses both block support attributes and ACF. Example features include:
        1. Background Image/Color Options
        2. Background Image Overlay (Including opacity/alpha with RGBA)
        3. Alignment, Padding & Margin options
        4. Custom ID and Class
        5. Title Input (optional) - Font-size, line-height, color
        6. Innerblocks - So you can add any other blocks inside        
    */

    //Block ID
        $blockId                = array_key_exists('anchor',$block) ? 'id="'.$block['anchor'].'"' : '';

    //Classes
        if(array_key_exists('className',$block)){           //Custom class from user input
            array_push($classes,$block['className']);
        }
    
        $inlineStyles           = array();                  //inline styles - block
        $titleStyles            = '';                       //title styles  - title h2
        $titleClasses           = array();                  //title classes


    //Background Color
        if(array_key_exists('backgroundColor',$block)){
            array_push($classes,'has-'.$block['backgroundColor'].'-background-color');
        }


    //Block Styles
        if(array_key_exists('style',$block)){
            $styles=$block['style'];

            //Margins & Padding
            if(array_key_exists('spacing',$styles)){
                foreach($styles['spacing'] as $type => $values){
                    foreach($values as $key => $value){
                        array_push($inlineStyles,$type.'-'.$key.':'.$value.';');
                    }
                }
            }

            //Typography            
            if(array_key_exists('typography',$styles)){
                $typography = $styles['typography'];
                $titleStyles=array_key_exists('fontSize',$typography) ? 'font-size:'.$typography['fontSize'].';' : '';
                $titleStyles.=array_key_exists('lineHeight',$typography) ? 'line-height:'.$typography['lineHeight'].'px;' : '';
            }
        }

    //Title Font Size
        if(array_key_exists('fontSize',$block)){
            array_push($titleClasses,'has-'.$block['fontSize'].'-font-size');
        }    
    
    //Title Color
        if(array_key_exists('textColor',$block)){
            array_push($titleClasses,'has-'.$block['textColor'].'-color');
        }

    //Alignment
        if(array_key_exists('align',$block)){
            array_push($classes,'align'.$block['align']);
        }
?>

<div <?php echo $blockId;?> class="<?php echo implode(' ',$classes); ?>" style="<?php echo implode('',$inlineStyles); ?>">
    <div class="wrap"> 
        <script type="text/javascript">
        (function( $ ) {

        /**
         * initMap
         *
         * Renders a Google Map onto the selected jQuery element
         *
         * @date    22/10/19
         * @since   5.8.6
         *
         * @param   jQuery $el The jQuery element.
         * @return  object The map instance.
         */
        function initMap( $el ) {

            // Find marker elements within map.
            var $markers = $el.find('.marker');

            // Create gerenic map.
            var mapArgs = {
                zoom        : $el.data('zoom') || 16,
                mapTypeId   : google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map( $el[0], mapArgs );

            // Add markers.
            map.markers = [];
            $markers.each(function(){
                initMarker( $(this), map );
            });

            // Center map based on markers.
            centerMap( map );

            // Return map instance.
            return map;
        }

        /**
         * initMarker
         *
         * Creates a marker for the given jQuery element and map.
         *
         * @date    22/10/19
         * @since   5.8.6
         *
         * @param   jQuery $el The jQuery element.
         * @param   object The map instance.
         * @return  object The marker instance.
         */
        function initMarker( $marker, map ) {

            // Get position from marker.
            var lat = $marker.data('lat');
            var lng = $marker.data('lng');
            var latLng = {
                lat: parseFloat( lat ),
                lng: parseFloat( lng )
            };

            // Create marker instance.
            var marker = new google.maps.Marker({
                position : latLng,
                map: map
            });

            // Append to reference for later use.
            map.markers.push( marker );

            // If marker contains HTML, add it to an infoWindow.
            if( $marker.html() ){

                // Create info window.
                var infowindow = new google.maps.InfoWindow({
                    content: $marker.html()
                });

                // Show info window when marker is clicked.
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open( map, marker );
                });
            }
        }

        /**
         * centerMap
         *
         * Centers the map showing all markers in view.
         *
         * @date    22/10/19
         * @since   5.8.6
         *
         * @param   object The map instance.
         * @return  void
         */
        function centerMap( map ) {

            // Create map boundaries from all map markers.
            var bounds = new google.maps.LatLngBounds();
            map.markers.forEach(function( marker ){
                bounds.extend({
                    lat: marker.position.lat(),
                    lng: marker.position.lng()
                });
            });

            // Case: Single marker.
            if( map.markers.length == 1 ){
                map.setCenter( bounds.getCenter() );

            // Case: Multiple markers.
            } else{
                map.fitBounds( bounds );
            }
        }

        // Render maps on page load.
        $(document).ready(function(){
            $('.acf-map').each(function(){
                var map = initMap( $(this) );
            });
        });

        })(jQuery);
        </script>
        <?php   
                if( !empty($map) ): 
                    ?>
                    <div class="acf-map">
                        <div class="marker" data-lat="<?php echo esc_attr($map['lat']); ?>" data-lng="<?php echo esc_attr($map['lng']); ?>"></div>
                    </div>
                <?php endif;
                if( have_rows('address') ):
                    
                        while ( have_rows('address') ) : the_row();
                            echo sanitize_text_field(get_sub_field('address_line'));
                            echo '<br/>';
                        endwhile;
                endif;
                if( have_rows('phone') ):
                    while ( have_rows('phone') ) : the_row();
                        echo sanitize_text_field(get_sub_field('phone_label')).': ';
                        $tel = '';
                        $tel = sanitize_text_field(get_sub_field('phone_number'));
                        printf('<a href="tel:%s">%s</a>', $tel, $tel);
                        echo '<br/>';
                    endwhile;
                endif;
                if( have_rows('email') ):
                    while ( have_rows('email') ) : the_row();
                        echo sanitize_text_field(get_sub_field('email_label')).': ';
                        echo sanitize_text_field(get_sub_field('email_address'));
                        echo '<br/>';
                    endwhile;
                endif;
        ?>
        <InnerBlocks />
    </div>
</div>