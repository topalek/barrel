<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if (!class_exists('GoogleMaper')) {
	class GoogleMaper {
		public $coordinates;
		public $address;
		public $width;
		public $height;
		public $zoom;
		public $map;
		
		function __construct ( $atts = array() ){
			$atts['address'] = trim($atts['address']);
			
			if( empty($atts['address']) ){
				if(is_admin() && isset($_GET['post_type'])){
					$googlemap = Config::getInstance()->getFieldsByType($_GET['post_type'],'googlemap');
					$address = $googlemap[0]['defaultCenter'];
				}
				else{
					$address = 'London';
				}
				$this->address = $address;
				$def_zoom = 10;
			}
			else{
				$this->address = $atts['address'];
				$def_zoom = 15;
			}
			
			$this->width = (!empty($atts['width']) AND is_numeric($atts['width']))?$atts['width']:'100%';
			$this->height = (!empty($atts['height']) AND is_numeric($atts['height']))?$atts['height']:'400px';
			$this->zoom = (!empty($atts['zoom']) AND is_numeric($atts['zoom']))?$atts['zoom']:$def_zoom;
			$this->post_id = (!empty($atts['id']) AND is_numeric($atts['id']))?$atts['id']:0;
			
			$this->coordinates = $this->getCoordinates( $this->address );
			$this->map = $this->getMap();
		}
		
		private function getCoordinates( $address, $force_refresh = false ) {

			$address_hash = md5( $address );

			$coordinates = get_transient( $address_hash );

			if ($force_refresh || $coordinates === false) {

				$args       = array( 'address' => urlencode( $address ), 'sensor' => 'false' );
				$url        = add_query_arg( $args, 'http://maps.googleapis.com/maps/api/geocode/json' );
				$response 	= wp_remote_get( $url );

				if( is_wp_error( $response ) )
					return;

				$data = wp_remote_retrieve_body( $response );

				if( is_wp_error( $data ) )
					return;

				if ( $response['response']['code'] == 200 ) {

					$data = json_decode( $data );

					if ( $data->status === 'OK' ) {

						$coordinates = $data->results[0]->geometry->location;

						$cache_value['lat'] 	= $coordinates->lat;
						$cache_value['lng'] 	= $coordinates->lng;
						$cache_value['address'] = (string) $data->results[0]->formatted_address;

						// cache coordinates for 3 months
						set_transient($address_hash, $cache_value, 3600*24*30*3);
						$data = $cache_value;

					} elseif ( $data->status === 'ZERO_RESULTS' ) {
						return __( 'No location found for the entered address.', 'pw-maps' );
					} elseif( $data->status === 'INVALID_REQUEST' ) {
						return __( 'Invalid request. Did you enter an address?', 'pw-maps' );
					} else {
						return __( 'Something went wrong while retrieving your map, please ensure you have entered the short code correctly.', 'pw-maps' );
					}

				} else {
					return __( 'Unable to contact Google API service.', 'pw-maps' );
				}

			} else {
			   // return cached results
			   $data = $coordinates;
			}

			return $data;
		}
		
		private function getMap() { 
			if( $this->coordinates ) :

				$coordinates = $this->coordinates;

				if( !is_array( $coordinates ) )
					return;

				$map_id = uniqid( 'pw_map_' ); // generate a unique ID for this map
 
			$map = '<div class="pw_map_canvas" id="'.esc_attr( $map_id ).'" style="height:'.esc_attr( $this->height ).'; width:'. esc_attr( $this->width ).';"></div>
				<script type="text/javascript">
					var map_'.$map_id.';
					function run_map_'.$map_id.'(){
						var location = new google.maps.LatLng("'.$coordinates['lat'].'", "'.$coordinates['lng'].'");
						var map_options = {
							zoom: '.$this->zoom.',
							center: location,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						}
						map_'.$map_id .' = new google.maps.Map(document.getElementById("'. $map_id .'"), map_options);

						var marker = new google.maps.Marker({
							position: location,
							map: map_'.$map_id .',
							icon: "'.IGNET_get_Map_Icon( $this->post_id ).'",
						});

					}
					run_map_'.$map_id .'();
				</script>
				<input type="hidden" name="IGNET_G_MAP_extra[address_longitude]" value="'.$coordinates['lng'].'" >
				<input type="hidden" name="IGNET_G_MAP_extra[address_latitude]" value="'.$coordinates['lat'].'" >';
			endif;
			return $map;
		}
		
		function getJointMap() {
			$coordinates = $this->coordinates;
			if(!empty($_SERVER['QUERY_STRING'])){
				$get_str = $_SERVER['QUERY_STRING'];
			} 
			elseif( in_array(get_queried_object()->name, Config::getInstance()->getObjectsSlags()) ){
				$get_str = 'object='.get_queried_object()->name;		
			}
			elseif(isset(get_queried_object()->taxonomy)){
				$get_str = 'object=all&tax['.get_queried_object()->taxonomy.'][]='.get_queried_object()->slug;
			}
			else{
				$get_str = 'object=all';
			}
			
			return
			'<script src="'. IGNET_G_MAP_URL .'js/markerclusterer.js"></script>
			<script src="'. IGNET_G_MAP_URL .'js/infobox.js"></script>
			<script src="'. IGNET_G_MAP_URL .'data.json.php?'.$get_str.'"></script>
			<script type="text/javascript">
				google.maps.event.addDomListener(window, "load", initialize);
			
				var styles = [ [{
						url: "' . IGNET_G_MAP_URL . 'image/group-icon1.png",
						height: 40,
						width: 40,
						anchor: [4, 0],
						textColor: "#990000",
						textSize: 10
					},
					{
						url: "' . IGNET_G_MAP_URL . 'image/group-icon2.png",
						height: 46,
						width: 46,
						anchor: [8, 0],
						textColor: "#ff0000",
						textSize: 14
					},
					{
						url: "' . IGNET_G_MAP_URL . 'image/group-icon3.png",
						width: 50,
						height: 50,
						anchor: [12, 0],
						textColor: "#001CEC",
						textSize: 14
					}]
				];

				function initialize() {
					if(data.object.length == 0){
						var center = new google.maps.LatLng('.$coordinates['lat'].', '.$coordinates['lng'].');
						map = new google.maps.Map(document.getElementById("map"), {
							zoom: 12,
							center: center,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});
					}
					else{
						map = new google.maps.Map(document.getElementById("map"), {
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});	
						var bounds = new google.maps.LatLngBounds();
					}
					
					var markers = [];
					for (var i = 0; i < data.object.length; i++) {
						var dataplace = data.object[i];
						var latLng = new google.maps.LatLng(dataplace.latitude,  dataplace.longitude);
						
						var marker = new google.maps.Marker({
							position: latLng
						});
						bounds.extend(latLng);
						
						if(dataplace.infobox == 1){
							HTML = "<div id=\"content\">"+
									"<div style=\"text-align: center;font-size: 13px;\"><strong>"+
									"<a href=\""+dataplace.object_url+"\">" + 
									dataplace.object_title + 
									"</a></strong></div>" + 
									"<div><a href=\""+dataplace.object_url+"\"><img width=\"140\" src=\""+dataplace.object_photo_url+"\"></a></div>"+
									"<div style=\"text-align: center;font-size: 17px;font-weight: bold;\">"+
									dataplace.object_price+"</div>"+
									"</div>";

							var marker = add_marker(dataplace.latitude, dataplace.longitude, dataplace.object_title, HTML, dataplace.object_icon_url);					
						}
						
						markers.push(marker);
					}

					if(data.object.length != 0){
						map.fitBounds(bounds);
					}

					var markerCluster = new MarkerClusterer(map, markers, {gridSize: 50, maxZoom: 15, styles: styles[0]});
				}

				function add_marker(lat, lng, title, HTML, icon) {

					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(lat, lng),
						map: map,
						title: title,
						icon: icon
					});

					var onMarkerClick = function() {
						var boxText = document.createElement("div");
						boxText.style.cssText = "border: 1px solid black; margin-top: 2px; background: #FFF; padding: 5px; border-radius: 5px;";
						boxText.innerHTML = HTML;
									
						var myOptions = {
										 content: boxText
										,disableAutoPan: false
										,maxWidth: 0
										,pixelOffset: new google.maps.Size(-75, -100)
										,zIndex: null
										,boxStyle: { opacity: 0.9,width: "150px" }
										,closeBoxMargin: "5px 3px 5px 5px"
										,infoBoxClearance: new google.maps.Size(1, 1)
										,isHidden: false
										,pane: "floatPane"
										,enableEventPropagation: false
						};
									
						var ib = new InfoBox(myOptions);
						ib.open(map, this);
					};
								
					google.maps.event.addListener(marker, "click", onMarkerClick);

					return marker;
				}
			</script>
			<div id="map" style="height:'.$this->height.'; width:'.$this->width.'"></div>';
		}
	}
}