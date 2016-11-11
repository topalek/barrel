<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	//Регистрируем отдельный размер миниатюры для иконок внутри карты
	if ( function_exists( 'add_image_size' ) AND get_option('IGNET_G_MAP_icon_work') == 1 ) {
		add_image_size( 'map_icon', '35', '35', true );
	}
	
	/*
	// Добавляем меню и обработчик опций в админку
	if ( is_admin() ) {
		$map_options = array (
		
			array( "id" => "IGNET_G_MAP_default_center" ),// Что показывать на карте если нет обьектов? (адрес по умолчанию)		
		
			//Работа с map_icon
			array( "id" => "IGNET_G_MAP_icon_work" ), // Создвать map_icon?
			array( "id" => "IGNET_G_MAP_icon_ever" ),//Обязательный map_icon (не публиковать посты без тумбы) ? 
			array( "id" => "IGNET_G_MAP_icon_default_img" ),//выбрать img по умолчнию, если тумба не загружена
			
			//Формирования инфобокса
			array( "id" => "IGNET_G_MAP_infobox_work" ),// Использовать инфобокс?
			//Фото берется из тумбы по умолчанию
			array( "id" => "IGNET_G_MAP_infobox_default_img" ),//выбрать img по умолчнию, если тумба не загружена
			array( "id" => "IGNET_G_MAP_infobox_price" ),// Что использовать как цену?
			array( "id" => "IGNET_G_MAP_infobox_price_name" ),//Текст после цены (usd, rur ...)
		);	
		add_action( 'admin_menu', 'IGNET_G_MAP_admin_generate_menu');
	}
	function IGNET_G_MAP_admin_generate_menu()
	{
		global $map_options;
		
		if ('map_save' == $_REQUEST['action'] ) {
			foreach ($map_options as $value) {
			
				if( isset( $_REQUEST[ $value['id'] ] ) ){
					$value_one = $_REQUEST[ $value['id'] ];
					
					if(is_array($value_one))
						$value_one = serialize($value_one);

					update_option( $value['id'], $value_one); 
				} 
			}
			
			if(stristr($_SERVER['REQUEST_URI'],'&saved=true')) {
				$location = $_SERVER['REQUEST_URI']; 
			} 
			else {
				$location = $_SERVER['REQUEST_URI'] . "&saved=true"; 
			}
			
			header("Location: $location");
			die;
		}

		add_options_page('Настройки Google Map', 'Настройки Google Map', 10, 'gmap-settings', 'IGNET_G_MAP_admin_page');
	}
	*/
	//Добавляем страницу админки 
	function IGNET_G_MAP_admin_page()
	{
		require_once( IGNET_G_MAP_DIR . 'admin_page.php');
	}
	
	//Добавление скриптов и стилей
	// Если мы в адм. интерфейсе
	if ( is_admin() ) {
		 // Добавляем стили и скрипты в админку
		 add_action('admin_enqueue_scripts', 'IGNET_G_MAP_admin_load_scripts');
		 add_action('admin_enqueue_scripts', 'IGNET_G_MAP_admin_load_styles');
	} 
	else {
		 // Добавляем стили и скрипты в шаблон
		 add_action('wp_enqueue_scripts', 'IGNET_G_MAP_site_load_scripts');
		 add_action('wp_enqueue_scripts', 'IGNET_G_MAP_site_load_styles');
	}
	
	/**
	* Добавляем скрипты в админку
	*/
	function IGNET_G_MAP_admin_load_scripts()
	{	
		wp_register_script( 'google-maps-api', 'http://maps.google.com/maps/api/js?sensor=false', array('jquery') );
		wp_register_script( 'IGNET_G_MAP-jquery-ajax', IGNET_G_MAP_URL .'js/ajax.js' , array('jquery'));
		/*****************************/
		wp_enqueue_script('jquery');
		wp_enqueue_script('google-maps-api');
		wp_enqueue_script('IGNET_G_MAP-jquery-ajax');

		
		wp_register_script('IGNET_G_MAP-media-upload', IGNET_G_MAP_URL.'/js/media-upload.js', array('jquery', 'media-upload', 'thickbox'));
		global $pagenow;
		if( $pagenow == 'options-general.php' AND $_GET['page']=='gmap-settings'){
			//Только для страницы редактирования профиля
			wp_enqueue_script('IGNET_G_MAP-media-upload');
		}
	}
	
	/**
	* Добавляем стили в админку
	*/
	function IGNET_G_MAP_admin_load_styles()
	{
		wp_register_style( 'IGNET_G_MAP-admin-styles', IGNET_G_MAP_URL .'styles.css' );
		wp_enqueue_style('IGNET_G_MAP-admin-styles');
		
		
		wp_register_style('IGNET_G_MAP-uploader-style', IGNET_G_MAP_URL.'/css/uploader-style.css');
		global $pagenow;
		if( $pagenow == 'options-general.php' AND $_GET['page']=='gmap-settings'){
			//Только для страницы редактирования профиля
			wp_enqueue_style('thickbox');
			wp_enqueue_style('IGNET_G_MAP-uploader-style');
		}
	}	
	
	/**
	* Добавляем скрипты в шаблон
	*/
	function IGNET_G_MAP_site_load_scripts()
	{
		wp_register_script( 'google-maps-api', 'http://maps.google.com/maps/api/js?sensor=false' );
		/*****************************/
		wp_enqueue_script('jquery');
		wp_enqueue_script('google-maps-api');
	}
		
	/**
	* Добавляем стили в шаблон
	*/
	function IGNET_G_MAP_site_load_styles()
	{

	}

	require_once( IGNET_G_MAP_DIR . 'functions.php');