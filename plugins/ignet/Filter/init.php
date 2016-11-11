<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
	// Добавляем меню в админку
	if ( is_admin() ) {
		add_action( 'admin_menu', 'IGNET_FILTER_admin_generate_menu');
	}
	function IGNET_FILTER_admin_generate_menu()
	{

	}
	
	
	//Добавление скриптов и стилей
	// Если мы в адм. интерфейсе
	if ( is_admin() ) {
		 // Добавляем стили и скрипты в админку
		 add_action('admin_enqueue_scripts', 'IGNET_FILTER_admin_load_scripts');
		 add_action('admin_enqueue_scripts', 'IGNET_FILTER_admin_load_styles');
	} 
	else {
		 // Добавляем стили и скрипты в шаблон
		 add_action('wp_enqueue_scripts', 'IGNET_FILTER_site_load_scripts');
		 add_action('wp_enqueue_scripts', 'IGNET_FILTER_site_load_styles');
	}
	
	/**
	* Добавляем скрипты в админку
	*/
	function IGNET_FILTER_admin_load_scripts()
	{	

	}
	
	/**
	* Добавляем стили в админку
	*/
	function IGNET_FILTER_admin_load_styles()
	{

	}	
	
	/**
	* Добавляем скрипты в шаблон
	*/
	function IGNET_FILTER_site_load_scripts()
	{
		wp_register_script( 'jquery-ui', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array('jquery'));
		wp_register_script( 'IGNET_FILTER-site-script', IGNET_FILTER_URL .'filter-script.js', array('jquery-ui'));
		/*****************************/

	}
		
	/**
	* Добавляем стили в шаблон
	*/
	function IGNET_FILTER_site_load_styles()
	{
		wp_register_style( 'IGNET_FILTER-site-styles', IGNET_FILTER_URL .'filter-number-style.css' );
		wp_register_style( 'IGNET_FILTER-form-def-styles', IGNET_FILTER_URL .'filter-form-def-styles.css' );
		/*****************************/	
	}

	require_once( IGNET_FILTER_DIR . 'functions.php');
		
		
	function IGNET_GetFilterFields($slug, $object){
		if( empty($slug) OR empty($object) ) return false;
		$ALL_OBJECTS = Config::getInstance()->getObjectsSlags();
		if( !in_array($object, $ALL_OBJECTS) ) return false;
		$TAXO_FROM_OBJECT = Config::getInstance()->getTaxonomySlags($object);
		$FIELDS_FROM_OBJECT = Config::getInstance()->getFieldsSlags($object);
		
		if( in_array($slug, $TAXO_FROM_OBJECT) ){
			//Таксономия
			$taxonomy = get_terms($slug);			
			if( !empty($taxonomy) ){
				$return = '<div class="filter_list_div filter_list_div_'.$slug.'">';
				$return .= 	'<div class="filter_list_title filter_list_title_'.$slug.'">';
				$return .= 	get_taxonomy($slug)->labels->name;
				$return .= 	'</div>';
				$return .= '<ul class="filter_list filter_list_'.$slug.'">';
				foreach($taxonomy as $taxo){
					$return .= '<li>
									<label class="filter_label label_'.$slug.'">
										<input '.IGNET_FILTER_checked($taxo->slug, $slug).'
											type="checkbox"
											id="filter_'.$slug.'_'.$taxo->slug.'_input"
											name="taxonomy['.$slug.'][]" 
											value="'.$taxo->slug.'" >
										<span class="filter_span filter_span_'.$slug.'">'.$taxo->name.'</span>
									</label>
								</li>';
				}
				$return .= '</ul>';
				$return .= '</div>';			
			}
			else{
				$return = '';
			}
			
			return $return;
		}
		elseif( in_array($slug, $FIELDS_FROM_OBJECT) ){
			//Пользовательское поле
			$ALL_FIELDS = Config::getInstance()->getToInit('FIELDS');
			foreach($ALL_FIELDS as $field){
				if($field['slug'] == $slug){
					$return_field = $field;
					break;
				}
			}
			
			if( is_array($return_field) ){
				if($return_field['type'] == 'number'){
					return IGNET_FILTER_get_number($field);
				}				
				elseif($return_field['type'] == 'select'){
					return IGNET_FILTER_get_select($field);
				}
				elseif($return_field['type'] == 'radio'){
					return IGNET_FILTER_get_radio($field);
				}			
				elseif($return_field['type'] == 'checkbox'){
					return IGNET_FILTER_get_checkbox($field);
				}			
			}

		}

	}
	
	function IGNET_GetFilter( $object ){
		if( empty($object) ) return false;
		$ALL_FILTER_SLUGS = Config::getInstance()->getFilterSlags($object);
		
		wp_enqueue_style('IGNET_FILTER-form-def-styles');
		
		$output = '<div class="filter_div_object_'.$object.'">';
		$output .= '<form method="get">';
		
			foreach($ALL_FILTER_SLUGS as $FilterSlug){
				$output .= IGNET_GetFilterFields($FilterSlug['slug'], $object);
			}
		$output .= '<input type="hidden" name="object" value="'.$object.'"  />';
		$output .= '<input type="submit" value="Искать"  />';
		$output .= '</form>';
		$output .= '</div>';
		
		return $output;
	}