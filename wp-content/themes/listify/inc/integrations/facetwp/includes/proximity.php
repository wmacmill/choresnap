<?php

class FacetWP_Facet_Proximity
{

    public $ordered_posts;


    function __construct() {
        $this->label = __( 'Proximity', 'listify' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $facet = $params['facet'];
        $value = $params['selected_values'];
        $unit = empty( $facet['unit'] ) ? 'mi' : $facet['unit'];

        $lat = empty( $value[0] ) ? '' : $value[0];
        $lng = empty( $value[1] ) ? '' : $value[1];
        $chosen_radius = empty( $value[2] ) ? '' : $value[2];
        $location_name = empty( $value[3] ) ? '' : urldecode( $value[3] );

        $radius_options = apply_filters( 'facetwp_proximity_radius_options', array( 5, 10, 25, 50, 100 ) );

        ob_start();
?>
        <input type="text" id="facetwp-location" value="<?php echo $location_name; ?>" placeholder="<?php _e( 'Enter location', 'listify' ); ?>" />

        <select id="facetwp-radius">
            <?php foreach ( $radius_options as $radius ) : ?>
            <?php $selected = ( $chosen_radius == $radius ) ? ' selected' : ''; ?>
            <option value="<?php echo $radius; ?>"<?php echo $selected; ?>><?php echo "$radius $unit"; ?></option>
            <?php endforeach; ?>
        </select>

        <div style="display:none">
            <input type="text" class="facetwp-lat" value="<?php echo $lat; ?>" />
            <input type="text" class="facetwp-lng" value="<?php echo $lng; ?>" />
        </div>

        <input type="button" class="facetwp-update" value="<?php _e( 'Apply', 'listify' ); ?>" />
        <input type="button" class="facetwp-reset" value="<?php _e( 'Reset', 'listify' ); ?>" />
<?php
        return ob_get_clean();
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $unit = empty( $facet['unit'] ) ? 'mi' : $facet['unit'];
        $earth_radius = ( 'mi' == $unit ) ? 3959 : 6371;

        if ( empty( $selected_values ) || empty( $selected_values[0] ) ) {
            return 'continue';
        }

        $lat = (float) $selected_values[0];
        $lng = (float) $selected_values[1];
        $radius = (int) $selected_values[2];

        // Lat = facet_value
        // Lng = facet_display_value
        $sql = "
        SELECT DISTINCT post_id,
        ( $earth_radius * acos( cos( radians( $lat ) ) * cos( radians( facet_value ) ) * cos( radians( facet_display_value ) - radians( $lng ) ) + sin( radians( $lat ) ) * sin( radians( facet_value ) ) ) ) AS distance
        FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}'
        HAVING distance < $radius
        ORDER BY distance";

        $this->ordered_posts = $wpdb->get_col( $sql );
        return $this->ordered_posts;
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/proximity', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.type-proximity .facet-unit').val(obj.unit);
    });

    wp.hooks.addFilter('facetwp/save/proximity', function($this, obj) {
        obj['source'] = $this.find('.facet-source').val();
        obj['unit'] = $this.find('.type-proximity .facet-unit').val();
        return obj;
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
?>
<script>

(function($) {
    $(document).on('facetwp-loaded', function() {
        var place;
        var input = document.getElementById('facetwp-location');
        var autocomplete = new google.maps.places.Autocomplete(input);

        var $input = $( '#facetwp-location' );
        var $lat = $( '.facetwp-lat' );
        var $lng = $( '.facetwp-lng' );

        $input.parent().prepend( '<i class="locate-me"></i>' );

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            place = autocomplete.getPlace();
            $lat.val(place.geometry.location.lat());
            $lng.val(place.geometry.location.lng());
            FWP.refresh();
        });

        $( '#facetwp-radius' ).change(function() {
            FWP.refresh();
        });

		var success = function(position) {
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;

			$lat.val( lat );
			$lng.val( lng );
			$input.val( lat + ', ' + lng );

			$( '.locate-me' ).removeClass( 'loading' );
			FWP.refresh();
		};

		var error = function() {
			$( '.locate-me' ).removeClass( 'loading' );
		};

        $input.parent().on( 'click', '.locate-me', function(e) {
			e.preventDefault();
			$( '.locate-me' ).addClass( 'loading' );
			navigator.geolocation.getCurrentPosition( success, error );
		});

        $(document).on('click', '#facetwp-location', function() {
            $(this).val('');
        });
    });
})(jQuery);

</script>
<script>
(function($) {
    wp.hooks.addAction('facetwp/refresh/proximity', function($this, facet_name) {
        var lat = $this.find('.facetwp-lat').val();
        var lng = $this.find('.facetwp-lng').val();
        var radius = $this.find('#facetwp-radius').val();
        var location = encodeURIComponent($this.find('#facetwp-location').val());
        FWP.facets[facet_name] = ('' != lat && 'undefined' != typeof lat) ?
            [lat, lng, radius, location] : [];
    });

    wp.hooks.addAction('facetwp/ready', function() {
        $(function() {
            $(document).on('click', '.facetwp-update', function() {
                FWP.refresh();
            });

            $(document).on('click', '.facetwp-reset', function() {
                var $parent = $(this).closest('.facetwp-facet');
                $parent.find('.facetwp-lat').val('');
                $parent.find('.facetwp-lng').val('');
                FWP.refresh();
            });
        });
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {
?>
        <tr class="facetwp-conditional type-proximity">
            <td>
                <?php _e('Unit of measurement', 'listify'); ?>:
            </td>
            <td>
                <select class="facet-unit">
                    <option value="mi"><?php _e( 'Miles', 'listify' ); ?></option>
                    <option value="km"><?php _e( 'Kilometers', 'listify' ); ?></option>
                </select>
            </td>
        </tr>
<?php
    }
}
