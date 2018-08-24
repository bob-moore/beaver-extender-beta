( function($) {

	BEGMap = function( options, el, centerAddress, markers ) {
		var defaults, $el, map, latlng, geocoder, i;

		var _create = function() {
			// first call will set the center
			_doLatLng( centerAddress, function( latlng ) {
				// Set the center
				if( latlng !== false ) {
					options.center = latlng;
				}
				// Create the map
				map = new google.maps.Map( el, options );
				// Set the resize function
				if( latlng !== false ) {
					google.maps.event.addDomListener( window, 'resize', function() {
					    map.setCenter( latlng );
					});
				}
				//Set the markers
				$.each( markers, function( int, marker ) {
					_doLatLng( marker.address, function( markerlatlng ) {
						if( markerlatlng !== false ) {
							// Add the new marker
							var markerArgs = {
								position: markerlatlng,
								map: map,
								title: marker.title
							};
							// Conditionally set an icon
							if( typeof marker.marker_src !== 'undefined' ) {
								markerArgs.icon = marker.marker_src;
							}
							var m = new google.maps.Marker( markerArgs );
							// Infowindow function
							google.maps.event.addListener( m, 'click', function() {
							    var infowindow = new google.maps.InfoWindow({
							        content : marker.content
							    });
							    infowindow.open( map, m );
							});
						}
					});
				});
			});
		};

		var _doLatLng = function( address, _callback ) {
			geocoder.geocode( { 'address' : address }, function( results, status ) {
		        if( status == google.maps.GeocoderStatus.OK ) {
		        	_callback( results[0].geometry.location );
		        } else {
		        	_callback( { lat : 55.205665844, lng : -119.42416497 } ); // Default coordinates
		        }
		    } );
		}

		var _setCenter = function() {
			geocoder.geocode( { 'address' : centerAddress }, function( results, status ) {
		        if( status == google.maps.GeocoderStatus.OK ) {
		        	options.center = results[0].geometry.location;
		        } else {
		        	options.center = null;
		        }
		    } );
		};

		var _init = function() {
			$el = $( el );
			geocoder = new google.maps.Geocoder();
			// make sure we have an element
			if( $el.length === 0 ) {
				return false;
			}
			// Extend our options
			defaults = {
			    zoom: 13,
			    minZoom: 1,
			    maxZoom: 20,
			    zoomControl: 1,
			    mapTypeId: google.maps.MapTypeId.ROADMAP,
			    scrollwheel: 1,
			    panControl: 1,
			    mapTypeControl: 1,
			    scaleControl: 1,
			    streetViewControl: 1,
			    overviewMapControl: 1,
			    rotateControl: 1
			};
			// Merge with defaults
			$.extend( defaults, options );
			// Create the map
			_create();
		};

		return {
			_init : _init
		}
	};

})(jQuery);