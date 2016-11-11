<?php
require_once( IGNET_G_MAP_DIR . 'GoogleMaper.php');
require_once( IGNET_G_MAP_DIR . 'mapInCustomField.php');

function IGNET_get_Map( $p_id = false){
	global $post;
	if(!$p_id){
		$p_id = $post->ID;
	}
	elseif(!is_numeric($p_id)){
		return false;
	}
	
	$address_city = get_post_meta($p_id, 'address_city', true);
	$address_street = get_post_meta($p_id, 'address_street', true);
	$address_house = get_post_meta($p_id, 'address_house', true);
	
	if(
		!is_admin() AND
		empty($address_city) AND 
		empty($address_street) AND
		empty($address_house)
	) return false;
	
	$GoogleMaper = new GoogleMaper(array('address' => trim($address_city.' '.$address_street.' '.$address_house), 'id' =>$p_id ));
	
	return 	
	'<div id="map_box" style="height:'.$GoogleMaper->height.'; width:'.$GoogleMaper->width.'">'
		. $GoogleMaper->map .
	'</div>';
}

function IGNET_get_JointMap(){
	$GoogleMaper = new GoogleMaper();
	return $GoogleMaper->getJointMap();
}

function IGNET_get_Map_Icon( $post_id = 0){
	$googlemap = Config::getInstance()->getFieldsByType(get_post_type( $post_id ),'googlemap');

	if($googlemap[0]['mapIcon'] == 1){
		//Брать иконку из миниатюры
		if ( has_post_thumbnail($post_id)){
			$map_icon_ID = get_post_meta($post_id, '_map_icon_ID', true);
			if( empty($map_icon_ID) ){
				return null;
			}
			else{
				$image = wp_get_attachment_image_src( $map_icon_ID, 'map_icon' );
				return $image[0];
			}
		}
	}

	return null;
}

function IGNET_get_Map_Infobox_Photo( $post_id = 0){
	$googlemap = Config::getInstance()->getFieldsByType(get_post_type( $post_id ),'googlemap');

	if($googlemap[0]['infobox'] == 1){
		//Брать фото из миниатюры
		if ( has_post_thumbnail($post_id)){
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' );
			return $image[0];
		}
	}
	else{
		
		return null;
	}
}

function IGNET_get_Map_Infobox_Price( $post_id = 0){
	$googlemap = Config::getInstance()->getFieldsByType(get_post_type( $post_id ),'googlemap');
	if($googlemap[0]['infobox'] == 1){
		return null;
	}
	else{
		return null;
	}
}


function IGNET_hide_post_without_thumbnails($post_id) {
	// If this is a revision, get real post ID
	if ( $parent_id = wp_is_post_revision( $post_id ) ) 
		$post_id = $parent_id;
		
	$thumbnail_id = get_post_meta($post_id, '_thumbnail_id');
	$post_status = get_post_status( $post_id );
	$post_type = get_post_type( $post_id );
	$all_post_types = Config::getInstance()->getObjectsSlags();
	$googlemap = Config::getInstance()->getFieldsByType(get_post_type( $post_id ),'googlemap');
		
	if ( $post_status == 'publish' AND in_array($post_type, $all_post_types)) {
		// Check if this post does not have thumbnail
		if ( empty( $thumbnail_id ) ) {
			if($googlemap[0]['mapIconEver'] == 1){
				// unhook this function so it doesn't loop infinitely
				remove_action( 'save_post', 'IGNET_hide_post_without_thumbnails' );

				// update the post, which calls save_post again
				wp_update_post( array( 'ID' => $post_id, 'post_status' => 'private' ) );

				// re-hook this function
				add_action( 'save_post', 'IGNET_hide_post_without_thumbnails' );			
			}
		}
		else{
			$map_icon_ID = get_post_meta($post_id, '_map_icon_ID', true);

			if( empty($map_icon_ID) OR $map_icon_ID != get_post_thumbnail_id(  $post_id ) ){
			
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(  $post_id ), 'map_icon' );
				$image_path = explode('wp-content', $image[0], 2);
				$image_path = WP_CONTENT_DIR . $image_path[1];
			
				//Создаем красивую миниатюру
				$watermark = imagecreatefrompng(IGNET_G_MAP_URL . 'image/watermark.png');  //исходное изображение с прозрачностью
				imagealphablending($watermark, false);
				imagesavealpha($watermark, true);
				
				$result = imagecreatetruecolor(38, 42); //создаем новое изображение для копирования в него исходного     
				imagealphablending($result, false);
				imagesavealpha($result, true);
				
				$black = ImageColorAllocate($result, 0, 0, 0); // черный цвет
				$trans = imagecolortransparent($result, $black); // теперь черный прозрачен
				ImageFill($result, 0, 0, $black); //заливка прозрачным цветом
				//изображение теперь прозрачное
				//Копируем в прозрачный фон рамку
				imagecopyresampled ($result  ,  $watermark, 0, 0, 0, 0, 38, 42, 38, 42 );

				//Узнаем формат миниатюры
				//Открываем миниатюру
				$image = imagecreatefromjpeg($image_path);
				imagealphablending($image, true);

				if ($image === false){
					return false;
				}
				
				//Копируем миниатюру внутрь результирующей картинки
				imagecopyresampled ($result  ,  $image, 2, 2, 0, 0, 33, 33, 33, 33 );
				
				//Сохраняем результат
				if(imagepng($result, $image_path, 0)){
					//Сохраняем ИД миниатюры которую УЖЕ ОБРАБОТАЛИ, чтобы не обрабатывать ее при каждом сохранении
					if ( !update_post_meta($post_id, '_map_icon_ID', get_post_thumbnail_id(  $post_id )) ){
						add_post_meta($post_id, '_map_icon_ID', get_post_thumbnail_id(  $post_id ), true);
					} 
				}
				
				//Освобождаем память
				imagedestroy($result);
				imagedestroy($watermark);
				imagedestroy($image);
			}
		}
	}
}

if(is_admin() && isset($_GET['post_type'])){
	$googlemap = Config::getInstance()->getFieldsByType($_GET['post_type'],'googlemap');
	if($googlemap[0]['mapIcon'] == 1){
		add_action( 'save_post', 'IGNET_hide_post_without_thumbnails');
	}
}