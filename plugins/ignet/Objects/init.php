<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
	// Добавляем меню в админку
	if ( is_admin() ) {
		add_action( 'admin_menu', 'IGNET_OBJECTS_admin_generate_menu');
	}
	function IGNET_OBJECTS_admin_generate_menu()
	{

	}
	
	
	//Добавление скриптов и стилей
	// Если мы в адм. интерфейсе
	if ( is_admin() ) {
		 // Добавляем стили и скрипты в админку
		 add_action('admin_enqueue_scripts', 'IGNET_OBJECTS_admin_load_scripts');
		 add_action('admin_enqueue_scripts', 'IGNET_OBJECTS_admin_load_styles');
	} 
	else {
		 // Добавляем стили и скрипты в шаблон
		 add_action('wp_enqueue_scripts', 'IGNET_OBJECTS_site_load_scripts');
		 add_action('wp_enqueue_scripts', 'IGNET_OBJECTS_site_load_styles');
	}
	
	/**
	* Добавляем скрипты в админку
	*/
	function IGNET_OBJECTS_admin_load_scripts()
	{	

	}
	
	/**
	* Добавляем стили в админку
	*/
	function IGNET_OBJECTS_admin_load_styles()
	{

	}	
	
	/**
	* Добавляем скрипты в шаблон
	*/
	function IGNET_OBJECTS_site_load_scripts()
	{

	}
		
	/**
	* Добавляем стили в шаблон
	*/
	function IGNET_OBJECTS_site_load_styles()
	{

	}

	require_once( IGNET_OBJECTS_DIR . 'functions.php');
		
	//Регистрация всех объектов из конфигурации	
	function IGNET_DEF_AllObjectsRegister(){
		$ALL_OBJECTS = Config::getInstance()->getToInit('OBJECTS');
		foreach($ALL_OBJECTS as $object){
			IGNET_DEF_ObjectRegister( $object );	
		}

	}
	add_action('init', 'IGNET_DEF_AllObjectsRegister');