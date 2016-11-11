<?php
function IGNET_DEF_TaxonomyRegister( array $data ){
	if( empty($data['slug']) ) return false;
	$slug = $data['slug'];
	$name = $data['name']?$data['name']:'Рубрики';
	$singularName = $data['singularName']?$data['singularName']:'Рубрика';
	$postsTypes = is_array($data['postsTypes'])?$data['postsTypes']:array('post');
	$NOTHierarchical = $data['NOTHierarchical']?false:true;
	
	$capabilities = array(
							'manage_terms' => 'edit_posts',
							'edit_terms' => 'edit_posts',
							'delete_terms' => 'manage_categories',
							'assign_terms' => 'edit_posts'			
						);
	
	$capabilities = apply_filters('ignet_taxonomy_capabilities', $capabilities, $slug);
	
	$labels = array(
		'name' => $name,
		'singular_name' => $singularName,
		'search_items' =>  'Поиск',
		'all_items' => 'Все '.$name,
		'parent_item' => $singularName,
		'parent_item_colon' => $singularName.':',
		'edit_item' => 'Редактировать',
		'update_item' => 'Обновить ',
		'add_new_item' => 'Добавить ',
		'new_item_name' => 'Создать новый ',
		'menu_name' => $name,
	);

	register_taxonomy(
		$slug,
		$postsTypes, //Типы записей для которых таксономия действует
		array(
			'hierarchical' => $NOTHierarchical,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => $slug ),
			'show_tagcloud' =>  false,
			'capabilities'  => $capabilities
		)
    ); 
	//-----------------------------------------------------// 		
}	
