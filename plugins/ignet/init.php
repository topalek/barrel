<?php
/*
Plugin Name: IGNET CPT
Plugin URI: http://ignet.org
Description: Быстро и просто создаем сложные объекты (почти framework :)
Author: IGNET
Author URI: http://ignet.org
Version: 20141228
*/

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	// DIR адрес для нашего функционала
	if(!defined('IGNET_DEF_DIR')) DEFINE('IGNET_DEF_DIR', plugin_dir_path( __FILE__ ) );
	// URL адрес для нашего функционала
	if(!defined('IGNET_DEF_URL')) DEFINE('IGNET_DEF_URL', plugin_dir_url(__FILE__) );
	//********************************//
	//********************************//


	// DIR адрес для папки конфигураций
	if(!defined('IGNET_CONFIG_DIR')) DEFINE('IGNET_CONFIG_DIR', IGNET_DEF_DIR . 'Config/' );
	// URL адрес для папки конфигураций
	if(!defined('IGNET_CONFIG_URL')) DEFINE('IGNET_CONFIG_URL', IGNET_DEF_URL. 'Config/' );

	if (file_exists(IGNET_CONFIG_DIR . 'Config.php')) require_once(IGNET_CONFIG_DIR . 'Config.php');
	//*******************************//


	// DIR адрес для плагина Доп. объектов
	if(!defined('IGNET_OBJECTS_DIR')) DEFINE('IGNET_OBJECTS_DIR', IGNET_DEF_DIR . 'Objects/' );
	// URL адрес для плагина Доп. объектов
	if(!defined('IGNET_OBJECTS_URL')) DEFINE('IGNET_OBJECTS_URL', IGNET_DEF_URL. 'Objects/' );

	if (file_exists(IGNET_OBJECTS_DIR . 'init.php')) require_once(IGNET_OBJECTS_DIR . 'init.php');
	//*******************************//
	//*******************************//


	// DIR адрес для плагина таксономий
	if(!defined('IGNET_TAXO_DIR')) DEFINE('IGNET_TAXO_DIR', IGNET_DEF_DIR . 'Taxonomy/' );
	// URL адрес для плагина таксономий
	if(!defined('IGNET_TAXO_URL')) DEFINE('IGNET_TAXO_URL', IGNET_DEF_URL. 'Taxonomy/' );

	if (file_exists(IGNET_TAXO_DIR . 'init.php')) require_once(IGNET_TAXO_DIR . 'init.php');
	//*******************************//
	//*******************************//


	// DIR адрес для плагина Доп. полей
	if(!defined('IGNET_C_FIELDS_DIR')) DEFINE('IGNET_C_FIELDS_DIR', IGNET_DEF_DIR . 'CustomFields/' );
	// URL адрес для плагина Доп. полей
	if(!defined('IGNET_C_FIELDS_URL')) DEFINE('IGNET_C_FIELDS_URL', IGNET_DEF_URL. 'CustomFields/' );

	if (file_exists(IGNET_C_FIELDS_DIR . 'init.php')) require_once(IGNET_C_FIELDS_DIR . 'init.php');
	//*******************************//
	//*******************************//

	// DIR адрес для плагина фильтра объектов
	if(!defined('IGNET_FILTER_DIR')) DEFINE('IGNET_FILTER_DIR', IGNET_DEF_DIR . 'Filter/' );
	// URL адрес для плагина фльтра объектов
	if(!defined('IGNET_FILTER_URL')) DEFINE('IGNET_FILTER_URL', IGNET_DEF_URL. 'Filter/' );

	if (file_exists(IGNET_FILTER_DIR . 'init.php')) require_once(IGNET_FILTER_DIR . 'init.php');
	//*******************************//