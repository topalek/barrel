<?php
function IGNET_FILTER_get_number( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';	
	
	$size = $data['size']?$data['size']:'';
	
	$min = empty($data['filter_default_min'])?'0':$data['filter_default_min'];
	$max = empty($data['filter_default_max'])?'10000':$data['filter_default_max'];
	$min_val = ($_GET[$slug]['min'])?$_GET[$slug]['min']:$min;
	$max_val = ($_GET[$slug]['max'])?$_GET[$slug]['max']:$max;	
	
	wp_enqueue_script('IGNET_FILTER-site-script');
	wp_enqueue_style('IGNET_FILTER-site-styles');
	
	$prfx = uniqid( $slug.'_slider_' );
	
	$return = '<div class="filter_number_slider_div filter_number_slider_div_'.$slug.'">
	<script type="text/javascript">
	jQuery(function(){
		slider_init("'.$prfx.'", '.$min.', '.$max.');
		jQuery("input#minCost_'.$prfx.'").change(function(){change_min_cost("'.$prfx.'")});
		jQuery("input#maxCost_'.$prfx.'").change(function(){change_max_cost("'.$prfx.'")});		
	})
	</script>
	<div class=" number_slider">
		<div class="formCost">
			<label class="from_label" for="minCost">
				<span class="property_name">'.$data['name'].'</span> 
				<span class="from">от</span>
				<input size="'.$size.'" class="cost-input" type="text" name="'.$slug.'[min]" id="minCost_'.$prfx.'" value="'. $min_val .'">
				'.$after.'
			</label> 
			<label class="to_label" for="maxCost">
				<span class="to">до</span>
				<input size="'.$size.'" class="cost-input" type="text" name="'.$slug.'[max]" id="maxCost_'.$prfx.'" value="'. $max_val .'">
				'.$after.'
			</label>
		</div>
		
		<div class="sliderCont">
			<div id="price_slider_'.$prfx.'" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
				<div class="ui-slider-range ui-widget-header" style="left: 0%;"></div>
				<a class="ui-slider-left-point ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a>
				<a class="ui-slider-right-point ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	</div>';

	return $return;
}

function IGNET_FILTER_get_select( array $data ){
	if( empty($data['slug']) or !is_array($data['options']) ) return false;
	global $post; 
	$my_post = $post;

	$slug = $data['slug'];
	$before = !empty($data['before'])?$data['before']:$data['name'];
	
	$return = '<div class="filter_list_div filter_list_div_'.$slug.'">';	
	$return .=	'<div class="filter_list_title filter_list_title_'.$slug.'">';
	$return .=	$before;
	$return .=	'</div>';
	
	$return .= '<ul class="filter_list filter_list_'.$slug.'">';
	foreach($data['options'] as $option){
		$return .= '<li>
						<label class="filter_label label_'.$slug.'">
							<input '.IGNET_FILTER_checked($option[0], $slug, $data['filter_default']).'
										type="checkbox"
										id="filter_'.$slug.'_'.$option[0].'_input"
										name="'.$slug.'[]" 
										value="'.$option[0].'" >
									<span class="filter_span filter_span_'.$slug.'">'.$option[1].'</span>
								</label>
							</li>';
	}
	$return .= '</ul>';
	$return .= '</div>';
	return $return;
}

function IGNET_FILTER_get_radio( array $data ){
	if( empty($data['slug']) or !is_array($data['options']) ) return false;
	global $post; 
	$my_post = $post;

	$slug = $data['slug'];
	$before = !empty($data['before'])?$data['before']:$data['name'];
	
	$return = '<div class="filter_list_div filter_list_div_'.$slug.'">';	
	$return .=	'<div class="filter_list_title filter_list_title_'.$slug.'">';
	$return .=	$before;
	$return .=	'</div>';
	$return .= '<ul class="filter_list filter_list_'.$slug.'">';
	foreach($data['options'] as $option){
		$content = isset($option[1])?$option[1]:$option[2];
		$return .= '<li>
						<label class="filter_label label_'.$slug.'">
							<input '.IGNET_FILTER_checked($option[0], $slug, $data['filter_default']).'
										type="checkbox"
										id="filter_'.$slug.'_'.$option[0].'_input"
										name="'.$slug.'[]" 
										value="'.$option[0].'" >
									<span class="filter_span filter_span_'.$slug.'">'.$content.'</span>
								</label>
							</li>';
	}
	$return .= '</ul>';
	$return .= '</div>';
	return $return;
}

function IGNET_FILTER_get_checkbox( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	$slug = $data['slug'];
	$content = !empty($data['before'])?$data['before']:$data['name'];
	$before = !empty($data['before'])?$data['before']:'';
	
	$return = '<div class="filter_list_div filter_list_div_'.$slug.'">';	
	$return .=	'<div class="filter_list_title filter_list_title_'.$slug.'">';
	$return .=	$before;
	$return .=	'</div>';
	$return .= '<ul class="filter_list filter_list_'.$slug.'">
					<li>
						<label class="filter_label label_'.$slug.'">
							<input '.IGNET_FILTER_checked(1, $slug, $data['filter_default']).'
										type="checkbox"
										id="filter_'.$slug.'_checkbox_input"
										name="'.$slug.'[]" 
										value="1" >
									<span class="filter_span filter_span_'.$slug.'">'.$content.'</span>
						</label>
					</li>
				</ul>';
	$return .= '</div>';
	return $return;
}

function IGNET_FILTER_checked($var_value, $var_name, $def_value=''){

	if(isset($_GET[$var_name])){
		if(is_array($_GET[$var_name])){
			if(in_array($var_value, $_GET[$var_name])) 
			return 'checked';
		}
		else{
			if( $var_value == $_GET[$var_name]) 
			return 'checked';
		}
	}
	elseif(isset($_GET['taxonomy'][$var_name]) AND is_array($_GET['taxonomy'][$var_name])){
		if(in_array($var_value, $_GET['taxonomy'][$var_name])) 
		return 'checked';
	}
	elseif($var_value == $def_value){
		return 'checked';
	}
}


function IGNET_FILTER_selected($var_value, $var_name){
	if(isset($_GET[$var_name])){
		if(is_array($_GET[$var_name])){
			if(in_array($var_value, $_GET[$var_name])) 
			return 'selected';
		}
		else{
			if( $var_value == $_GET[$var_name]) 
			return 'selected';
		}
	}
	elseif(isset($_GET['taxonomy'][$var_name]) AND is_array($_GET['taxonomy'][$var_name])){
		if(in_array($var_value, $_GET['taxonomy'][$var_name])) 
		return 'selected';
	}
}


function IGNET_FILTER_get_query_array(){
	$data = $_GET;
	$meta_query = array();
	$tax_query = array();
	$post_type = array();
	
	if( isset($data['object']) ){
		$ALL_FIELDS = Config::getInstance()->getToInit('FIELDS');	
		$object = $data['object'];
		$post_type[] = apply_filters('ignet_filter_post_get_query_object', $object );
		unset($data['object']);
		
		if( isset($data['taxonomy']) ){

			foreach($data['taxonomy'] as $name=>$array_slugs){
				$tax_query[] = array(
									'taxonomy' => $name,
									'field' => 'slug',
									'terms' => $array_slugs,
									'operator' => 'IN'
								);
			}
			
			unset($data['taxonomy']);
		}

		foreach($data as $name=>$value){
				foreach($ALL_FIELDS as $field){
					if($field['slug'] == $name){
						$return_field = $field;
						break;
					}
				}
				
				if( is_array($return_field) ){
					if($return_field['type'] == 'number'){
						if(isset($value['min']) AND isset($value['max'])){
							$meta_query[] = array(	'key' => $name,
													'value' => array($value['min'], $value['max']),
													'compare' => 'BETWEEN',
													'type' => 'NUMERIC'
												);
						}
					}
					elseif($return_field['type'] == 'select'){
						$meta_query[] = array(	'key' => $name,
												'value' => $value
												);	
					}
					elseif($return_field['type'] == 'radio'){
						$meta_query[] = array(	'key' => $name,
												'value' => $value
												);	
					}			
					elseif($return_field['type'] == 'checkbox'){
						if( is_array($value) ){
							$meta_query[] = array(	'key' => $name,
													'value' => $value,
													'compare' => 'IN'
												);
						}
						else{
							$meta_query[] = array(	'key' => $name,
													'value' => $value
													);
						}
					}
				}
		}
		
		$meta_query = apply_filters('ignet_filter_post_get_query_meta', $meta_query );
		$tax_query = apply_filters('ignet_filter_post_get_query_tax', $tax_query );
		
		return array(   'tax_query' => $tax_query, 
						'meta_query' => $meta_query,
						'post_type' => $post_type);
	}
	else{
		$meta_query = apply_filters('ignet_filter_post_get_query_meta', $meta_query );

		return array('meta_query' => $meta_query);
	}
}


function IGNET_FILTER_pre_get_posts( $query ) {
	
	if ( ! is_admin() && $query->is_main_query() ) {
		$ALL_OBJECTS = Config::getInstance()->getObjectsSlags();
		$objects_to_filter = array('ignet-search-results');
		foreach($ALL_OBJECTS as $object){
			$FIELDS_FROM_OBJECT = Config::getInstance()->getFieldsSlags($object);
			if( !empty($FIELDS_FROM_OBJECT) ){
				$objects_to_filter[] = $object;
			}
		}
		
		if( is_post_type_archive( $objects_to_filter ) ){
			$my_query_filter =  IGNET_FILTER_get_query_array();
			
			if( !empty($my_query_filter['post_type']) ){
				$query->set( 'post_type', $my_query_filter['post_type'] );	
			}
			elseif( is_post_type_archive( 'ignet-search-results' ) ){
				$query->set( 'post_type', $objects_to_filter );
			}
			
			if( !empty($my_query_filter['meta_query']) ){
				$query->set('meta_query', $my_query_filter['meta_query']);	
			}
			
			if( !empty($my_query_filter['tax_query']) ){
				$query->set('tax_query', $my_query_filter['tax_query']);	
			}			
		}
    }
	
}
add_action( 'pre_get_posts', 'IGNET_FILTER_pre_get_posts' );


function IGNET_FILTER_search_results_archive(){
	$labels = array(
		 'name' => 'Результаты поиска' // основное название для типа записи
		,'singular_name' => '' // название для одной записи этого типа
		,'add_new' => '' // для добавления новой записи
		,'add_new_item' => '' // заголовка у вновь создаваемой записи в админ-панели.
		,'edit_item' => '' // для редактирования типа записи
		,'new_item' => '' // текст новой записи
		,'view_item' => '' // для просмотра записи этого типа.
		,'search_items' => '' // для поиска по этим типам записи
		,'not_found' => '' // если в результате поиска ничего не было найдень
		,'not_found_in_trash' => '' // если не было найдено в корзине
		,'parent_item_colon' => '' // для родительских типов. для древовидных типов
		,'menu_name' => '' // название меню
	);
	$args = array(
		 'label' => null //Имя типа записи помеченное для перевода на другой язык
		,'labels' => $labels 
		,'description' => '' 
		,'public' => false //показывать ли эту менюшку в админ-панели.
		,'publicly_queryable' => true //Запросы относящиеся к этому типу записей будут работать во фронтэнде (в шаблоне сайта)
		,'exclude_from_search' => true //Исключить ли этот тип записей из поиска по сайту
		,'show_ui' => false //Показывать ли меню для управления этим типом записи в админ-панели. 
		,'show_in_menu' => false //Показывать ли тип записи в администраторском меню и где именно показывать управление этим типом записи. 
		,'menu_position' => null // Позиция где должно расположится меню нового типа записи
		,'menu_icon' => null //Ссылка на картинку, которая будет использоваться для этого меню
		,'capability_type' => 'post'
		,'hierarchical' => false //Будут ли записи этого типа иметь древовидную структуру (как постоянные страницы)
		,'supports' => array('title','editor') //Вспомогательные поля на странице создания/редактирования этого типа записи.
		,'taxonomies' => array() //Массив зарегистрированных таксономий, которые будут связанны с этим типом записей
		,'has_archive' => true
		,'rewrite' => true
		,'query_var' => true
		,'show_in_nav_menus' => null
	);
	register_post_type( 'ignet-search-results', $args );
}
add_action('init', 'IGNET_FILTER_search_results_archive');