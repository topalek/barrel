<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
	// Добавляем меню в админку
	if ( is_admin() ) {
		add_action( 'admin_menu', 'IGNET_TAXO_admin_generate_menu');
	}
	function IGNET_TAXO_admin_generate_menu()
	{

	}
	
	
	//Добавление скриптов и стилей
	// Если мы в адм. интерфейсе
	if ( is_admin() ) {
		 // Добавляем стили и скрипты в админку
		 add_action('admin_enqueue_scripts', 'IGNET_TAXO_admin_load_scripts');
		 add_action('admin_enqueue_scripts', 'IGNET_TAXO_admin_load_styles');
	} 
	else {
		 // Добавляем стили и скрипты в шаблон
		 add_action('wp_enqueue_scripts', 'IGNET_TAXO_site_load_scripts');
		 add_action('wp_enqueue_scripts', 'IGNET_TAXO_site_load_styles');
	}
	
	/**
	* Добавляем скрипты в админку
	*/
	function IGNET_TAXO_admin_load_scripts()
	{	

	}
	
	/**
	* Добавляем стили в админку
	*/
	function IGNET_TAXO_admin_load_styles()
	{

	}	
	
	/**
	* Добавляем скрипты в шаблон
	*/
	function IGNET_TAXO_site_load_scripts()
	{

	}
		
	/**
	* Добавляем стили в шаблон
	*/
	function IGNET_TAXO_site_load_styles()
	{

	}

	require_once( IGNET_TAXO_DIR . 'functions.php');
	
	//Регистрация всех таксономий из конфигурации
	function IGNET_DEF_CommonTaxonomy(){
		$all_taxonomy = Config::getInstance()->getToInit('TAXONOMY');
		foreach($all_taxonomy as $taxonomy){
			IGNET_DEF_TaxonomyRegister( $taxonomy );
		}	
	}
	add_action('init', 'IGNET_DEF_CommonTaxonomy');