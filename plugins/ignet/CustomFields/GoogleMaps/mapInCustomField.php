<?php
// Подключаем блоки дополнительных свойств
add_action('add_meta_boxes', 'IGNET_G_MAP_add_box_extra_fields', 10);
function IGNET_G_MAP_add_box_extra_fields() {
	$G_MAP_FIELDS =  array();
	foreach(Config::getInstance()->getToInit('OBJECTS') as $object_name=>$object_data){
		foreach(Config::getInstance()->getFieldsByType($object_name, 'googlemap') as $FIELD ){
			if( !in_array($FIELD, $G_MAP_FIELDS) ) $G_MAP_FIELDS[] = $FIELD;
		}
	}

	foreach($G_MAP_FIELDS as $FIELD){
		if(is_array( $FIELD['postsTypes'])){
			foreach($FIELD['postsTypes'] as $object_name){
				IGNET_G_MAP_add_meta_box( $object_name, $FIELD['slug'] );
			}
		}
		else{
			IGNET_G_MAP_add_meta_box( $FIELD['postsTypes'], $FIELD['slug'] );
		}
	}
}

function IGNET_G_MAP_add_meta_box(  $object_name, $slug  ){
			add_meta_box(
							'autocomplete_map_extra_field_'.$slug, //ID блока
							'Объект на карте', //Заголовок блока
							'IGNET_G_MAP_get_box_extra_fields', //Функция вывода полей
							$object_name, //Тип записи для которой показывается блок
							'normal', //Место показа блока ('normal', 'advanced' или 'side')
							'low' //Приоритет расположения
						);
}

function IGNET_G_MAP_get_box_extra_fields(){
	global $post; 
	$my_post = $post;
?>								
	<p><span class="property">Метка на карте:</span><br>
		<label>Город <input id="to_map_address_city" type="text" name="IGNET_G_MAP_extra[address_city]" value="<?php echo get_post_meta($my_post->ID, 'address_city', 1); ?>" size="15" /></label>
		<label>улица <input id="to_map_address_street" type="text" name="IGNET_G_MAP_extra[address_street]" value="<?php echo get_post_meta($my_post->ID, 'address_street', 1); ?>" size="30" /></label>	
		<label>дом <input id="to_map_address_house" type="text" name="IGNET_G_MAP_extra[address_house]" value="<?php echo get_post_meta($my_post->ID, 'address_house', 1); ?>" size="5" /></label>
			<span id="reload_pam"> Показать на карте </span>
	</p>
	<?php	
		$GoogleMaper = new GoogleMaper(
			array('address' => trim(get_post_meta($my_post->ID, 'address_city', 1).' '.get_post_meta($my_post->ID, 'address_street', 1).' '.get_post_meta($my_post->ID, 'address_house', 1)), 'id' =>$my_post->ID )
		);?>
		
	<div id="map_box" style="height: <?php echo $GoogleMaper->height; ?>; width: <?php echo $GoogleMaper->width; ?>;">
		<?php echo $GoogleMaper->map; ?>
	</div>
	 <input type="hidden" name="IGNET_G_MAP_extra_fields_nonce" value="<?php echo wp_create_nonce('nonce'); ?>" />
	 <input type="hidden" id="hide_google_pam_dir_url" value="<?php echo IGNET_G_MAP_URL; ?>" />
<?php

}
// включаем обновление полей при сохранении
add_action('save_post', 'IGNET_G_MAP_extra_fields_update', 0);
/* Сохраняем данные, при сохранении поста */
function IGNET_G_MAP_extra_fields_update( $post_ID ){

    if ( !wp_verify_nonce( $_POST['IGNET_G_MAP_extra_fields_nonce'], 'nonce') ) return false; // проверка
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // выходим если это автосохранение
	
	if ( !current_user_can('edit_post', $post_ID ) ) return false; // выходим если юзер не имеет право редактировать запись

	if( !isset($_POST['IGNET_G_MAP_extra']) ) return false;	// выходим если данных нет
	
	//Теперь, нужно сохранить/удалить данные
	$_POST['IGNET_G_MAP_extra'] = array_map('trim', $_POST['IGNET_G_MAP_extra']); // чистим все данные от пробелов по краям
	foreach( $_POST['IGNET_G_MAP_extra'] as $key=>$value ){
		if( empty($value) ){
			delete_post_meta($post_ID , $key); // удаляем поле если значение пустое
			continue;
		}
		update_post_meta($post_ID , $key, $value); // add_post_meta() работает автоматически
	
	}
	return $post_ID ;
}