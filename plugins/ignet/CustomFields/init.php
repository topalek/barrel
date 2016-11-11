<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
	// Добавляем меню в админку
	if ( is_admin() ) {
		add_action( 'admin_menu', 'IGNET_C_FIELDS_admin_generate_menu');
	}
	function IGNET_C_FIELDS_admin_generate_menu()
	{

	}
	
	
	//Добавление скриптов и стилей
	// Если мы в адм. интерфейсе
	if ( is_admin() ) {
		 // Добавляем стили и скрипты в админку
		 add_action('admin_enqueue_scripts', 'IGNET_C_FIELDS_admin_load_scripts');
		 add_action('admin_enqueue_scripts', 'IGNET_C_FIELDS_admin_load_styles');
	} 
	else {
		 // Добавляем стили и скрипты в шаблон
		 add_action('wp_enqueue_scripts', 'IGNET_C_FIELDS_site_load_scripts');
		 add_action('wp_enqueue_scripts', 'IGNET_C_FIELDS_site_load_styles');
	}
	
	/**
	* Добавляем скрипты в админку
	*/
	function IGNET_C_FIELDS_admin_load_scripts()
	{	
		wp_localize_script( 'jquery', 'IGNET_C_FIELDS_ajax_data', 
			array(
			   'url' => admin_url('admin-ajax.php'),
			   'nonce' => wp_create_nonce("IGNET_C_FIELDS_nonce")
			)
		);
		wp_register_script( 'IGNET_C_FIELDS-admin-script', IGNET_C_FIELDS_URL .'fields-script.js', array('jquery') );
		wp_register_script( 'IGNET_C_FIELDS-admin-select2', IGNET_C_FIELDS_URL .'select2/select2.js', array('jquery') );
		wp_register_script( 'IGNET_C_FIELDS-admin-mediauploader', IGNET_C_FIELDS_URL .'mediauploader/uploader.js', array('jquery') );		

		wp_enqueue_script('IGNET_C_FIELDS-admin-script');
		
		
		wp_register_script(
			'jquery-ui', 
			IGNET_C_FIELDS_URL . 'UI/jquery-ui.min.js',
			//'http://code.jquery.com/ui/1.11.3/jquery-ui.min.js',
			array('jquery')
		);
		wp_register_script(
			'datepicker-ru', 
			IGNET_C_FIELDS_URL . 'UI/datepicker-ru.js', 
			array('jquery-ui')
		);
		//*****************************************************//
		
		wp_register_script(
			'jqueryUItimepickerAddoni',
			IGNET_C_FIELDS_URL . 'UI/Timepicker/jquery-ui-timepicker-addon.js', 
			array('jquery-ui')
		);
		wp_register_script(
			'jqueryUItimepickerAddoniRU',
			IGNET_C_FIELDS_URL . 'UI/Timepicker/i18n/jquery-ui-timepicker-ru.js', 
			array('jqueryUItimepickerAddoni')
		);		
	}
	
	/**
	* Добавляем стили в админку
	*/
	function IGNET_C_FIELDS_admin_load_styles()
	{
		wp_register_style( 'IGNET_C_FIELDS-admin-style', IGNET_C_FIELDS_URL .'fields-style.css' );
		wp_enqueue_style('IGNET_C_FIELDS-admin-style');

		wp_register_style( 'IGNET_C_FIELDS-select2-style', IGNET_C_FIELDS_URL .'select2/select2.css' );
		wp_enqueue_style('IGNET_C_FIELDS-select2-style');
		
		
		wp_register_style( $handle = 'jquery-ui_css', $src = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		
		wp_register_style( $handle = 'timepicker-ui_css', $src = IGNET_C_FIELDS_URL .'UI/Timepicker/jquery-ui-timepicker-addon.css');		

		wp_enqueue_style( 'jquery-ui_css' );
		wp_enqueue_style( 'timepicker-ui_css' );
		
		wp_register_style( 'IGNET_C_FIELDS-mediauploader-style', IGNET_C_FIELDS_URL .'mediauploader/uploader.css' );
		wp_enqueue_style('IGNET_C_FIELDS-mediauploader-style');		
		
		
	}	
	
	/**
	* Добавляем скрипты в шаблон
	*/
	function IGNET_C_FIELDS_site_load_scripts()
	{
		IGNET_C_FIELDS_admin_load_scripts();
	}
		
	/**
	* Добавляем стили в шаблон
	*/
	function IGNET_C_FIELDS_site_load_styles()
	{
		IGNET_C_FIELDS_admin_load_styles();
	}

	require_once( IGNET_C_FIELDS_DIR . 'functions.php');

	// Подключаем блоки дополнительных свойств
	add_action('add_meta_boxes', 'IGNET_DEF_add_box_extra_fields', 1);
	function IGNET_DEF_add_box_extra_fields() {
		$ALL_OBJECTS = Config::getInstance()->getToInit('OBJECTS');
		//print_r($ALL_OBJECTS);
		$callback_args = array();
		foreach($ALL_OBJECTS as $object){
			if(get_post_type() == $object['slug']){
				
				$boxes = Config::getInstance()->getBoxesByObject($object['slug']);

				foreach($boxes as $box){
					//print_r($box);
					$empty_bool = true;
					foreach(Config::getInstance()->getFieldsByBox($box['id']) as $field){
						if( in_array(get_post_type(), $field['postsTypes']) ){
							if( !empty($field['postsIds']) ){
								if( in_array(get_the_ID(), $field['postsIds']) ){
									$empty_bool = false;
									break;		
								}
							}
							else{
								$empty_bool = false;
								break;							
							}
						}
					}
					
					if( ! $empty_bool ){
						add_meta_box(
								$box['id'], //ID блока
								$box['title'], //Заголовок блока
								'IGNET_DEF_get_box_extra_fields', //Функция вывода полей
								$object['slug'], //Тип записи для которой показывается блок
								$box['context'], //Место показа блока ('normal', 'advanced' или 'side')
								$box['priority'], //Приоритет расположения
								$callback_args
							);
					}

				}
				
				
			}

		}
	

	}
	
	//Формируем блок дополнительный свойств
	function IGNET_DEF_get_box_extra_fields( $post, $callback_args ){
		
		$fields = Config::getInstance()->getFieldsByBox($callback_args['id']);

		foreach($fields as $field){
			if( in_array(get_post_type(), $field['postsTypes']) ){
				if($field['type'] == 'select2'){
					echo IGNET_DEF_get_extra_input_SELECT2($field);
				}
				elseif($field['type'] == 'file'){
					echo IGNET_DEF_get_extra_input_FILE($field);
				}				
				elseif($field['type'] == 'text'){
					echo IGNET_DEF_get_extra_input_TEXT($field);
				}
				elseif($field['type'] == 'datetime'){
					echo IGNET_DEF_get_extra_input_DATETIME($field);
				}				
				elseif($field['type'] == 'number'){
					echo IGNET_DEF_get_extra_input_NUMBER($field);
				}				
				elseif($field['type'] == 'textarea'){
					echo IGNET_DEF_get_extra_input_TEXTAREA($field);
				}			
				elseif($field['type'] == 'select'){
					echo IGNET_DEF_get_extra_input_SELECT($field);
				}
				elseif($field['type'] == 'radio'){
					echo IGNET_DEF_get_extra_input_RADIO($field);
				}			
				elseif($field['type'] == 'checkbox'){
					echo IGNET_DEF_get_extra_input_CHECKBOX($field);
				}			
				elseif($field['type'] == 'hidden'){
					echo IGNET_DEF_get_extra_input_hidden($field);
				}
			}
		}
		echo IGNET_DEF_get_extra_fields_nonce();
	}
	
	// DIR адрес для плагина Гугл Карт
	if(!defined('IGNET_G_MAP_DIR')) DEFINE('IGNET_G_MAP_DIR', IGNET_C_FIELDS_DIR . 'GoogleMaps/' );
	// URL адрес для плагина Гугл Карт
	if(!defined('IGNET_G_MAP_URL')) DEFINE('IGNET_G_MAP_URL', IGNET_C_FIELDS_URL. 'GoogleMaps/' );	
	if (file_exists(IGNET_G_MAP_DIR . 'init.php')) require_once(IGNET_G_MAP_DIR . 'init.php');
	//******************************************//