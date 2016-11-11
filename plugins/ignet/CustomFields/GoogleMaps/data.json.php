<?php
if(isset($_GET['object'])):
	$home_dir = explode('wp-content', dirname(__FILE__), 2);
	$home_dir = $home_dir[0];
	require( $home_dir . '/wp-blog-header.php' );
	$str_query = '';
	$my_query_filter =  IGNET_FILTER_get_query_array();
	
	if( in_array($_GET['object'], Config::getInstance()->getObjectsSlags()) ){
		$str_query = array(
						'posts_per_page' => -1,
						'post_type' => $_GET['object'],
						'meta_query' => $my_query_filter['meta_query'],
						'tax_query' => $my_query_filter['tax_query'] 
		);
	}
	elseif( $_GET['object'] == 'all' ){
		$str_query = array(
						'posts_per_page' => -1,
						'post_type' => Config::getInstance()->getObjectsSlags(),
						'meta_query' => $my_query_filter['meta_query'],
						'tax_query' => $my_query_filter['tax_query'] 
		);	
	
	}	
	
	header('Content-Type: application/x-javascript; charset=UTF-8');
	header("Expires: " . date("r"));
	header("Cache-Control: no-store");
	
	 
echo'var data = { 
	"object_type": "'.$_GET['object'].'",
	"object": [
';

	$ignet_query = new WP_Query($str_query);
		if ($ignet_query->have_posts()) :
			while ($ignet_query->have_posts()) : $ignet_query->the_post();
			$googlemap = Config::getInstance()->getFieldsByType(get_post_type( get_the_ID() ),'googlemap');
			$longitude = get_post_meta(get_the_ID(), 'address_longitude', true);
			$latitude = get_post_meta(get_the_ID(), 'address_latitude', true);
			if( !empty($longitude) && !empty($latitude) ){
echo 
'		{
			"object_id": '.get_the_ID().',
			"infobox": '.$googlemap[0]['infobox'].',
			"longitude": '.get_post_meta(get_the_ID(), 'address_longitude', true).', 
			"latitude": '.get_post_meta(get_the_ID(), 'address_latitude', true).',
			"object_title": "'.get_the_title().'", 
			"object_url": "'.get_permalink().'", 
			"object_photo_url": "'.IGNET_get_Map_Infobox_Photo( get_the_ID() ).'", 
			"object_icon_url": "'.IGNET_get_Map_Icon( get_the_ID() ).'",
			"object_price": "'.IGNET_get_Map_Infobox_Price( get_the_ID() ).'"
		},
';			
			}

				endwhile;
			endif;
				echo 
'       ]
}';		
			wp_reset_postdata();
endif;