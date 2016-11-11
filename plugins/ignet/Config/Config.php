<?php
/* Синглтон для хранения всех конфигурационных данных, 
   установленных в конфигурационном файле */
   
class Config {
	
	//Файлы конфигурации
	const OBJECTS_CONFIG_FILE = 'object-config.ini';
	const TAXONOMY_CONFIG_FILE = 'taxonomy-config.ini';
	const FIELDS_CONFIG_FILE = 'fields-config.ini';
	const BOXES_CONFIG_FILE = 'boxes-config.ini';
	
	private $_settings = array(
							'OBJECTS' => array('post', 'page'),
							'TAXONOMY' => array(),
							'FIELDS' => array(),
							'BOXES' => array()
						);
	
	static private $_instance = null;
	private function __clone(){} // запрещаем клонирование
    //возможность вызова только из getInstance
    static function getInstance(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }    
	
	private function __construct(){
		//Разбираем файл конфигурации объектов
    	if(file_exists(IGNET_CONFIG_DIR . self::OBJECTS_CONFIG_FILE)){
      		$this->_settings['OBJECTS'] = parse_ini_file( IGNET_DEF_DIR . 'Config/' . self::OBJECTS_CONFIG_FILE, true);
				
				$this->_settings['OBJECTS']['page'] = array('slug' => 'page');
				$this->_settings['OBJECTS']['post'] = array('slug' => 'post');
				
				foreach($this->_settings['OBJECTS'] as $key=>$OBJECT){
					if( !empty($OBJECT['taxonomies']))
					//Если в конфигурации объекта прописаны связи с таксономиями
					{
						//Парсим связи с таксономиями
						$this->_settings['OBJECTS'][$key]['taxonomies'] = array_map('trim', explode(',', $OBJECT['taxonomies']) );
					}
					
					if( !empty($OBJECT['supports']))
					//Если в конфигурации объекта прописаны блоки редактирования
					{
						//Парсим блоки
						$this->_settings['OBJECTS'][$key]['supports'] = array_map('trim', explode(',', $OBJECT['supports']) );
					}					
				}
    	}

		//Разбираем файл конфигурации таксономии
    	if(file_exists(IGNET_CONFIG_DIR . self::TAXONOMY_CONFIG_FILE)){
      		$this->_settings['TAXONOMY'] = parse_ini_file( IGNET_DEF_DIR . 'Config/' . self::TAXONOMY_CONFIG_FILE, true);

				foreach($this->_settings['TAXONOMY'] as $key=>$TAXONOMY){
					if( !empty($TAXONOMY['postsTypes']))
					//Если в конфигурации таксономии прописаны связи с объектами
					{
						//Парсим связи с объектами
						$this->_settings['TAXONOMY'][$key]['postsTypes'] = explode(',', $TAXONOMY['postsTypes']);
					}
				}
    	}
		
		//Разбираем файл конфигурации пользовательских полей
    	if(file_exists(IGNET_CONFIG_DIR . self::FIELDS_CONFIG_FILE)){
      		$this->_settings['FIELDS'] = parse_ini_file( IGNET_DEF_DIR . 'Config/' . self::FIELDS_CONFIG_FILE, true);
				foreach($this->_settings['FIELDS'] as $key=>$FIELDS){
					if( !empty($FIELDS['options']) AND is_array($FIELDS['options']) )
					//Если в свойствах поля присутствуют опции
					{
						//Разбираем опции
						foreach($FIELDS['options'] as $in_key=>$option){
							$this->_settings['FIELDS'][$key]['options'][$in_key] = array_map('trim', explode(',', $FIELDS['options'][$in_key]) );						
						}
					}
					
					if( !empty($FIELDS['postsTypes']) )
					//Если в свойствах указана связь с объектами
					{
						//Разбираем связь с объектами
						$this->_settings['FIELDS'][$key]['postsTypes'] = array_map('trim', explode(',', $FIELDS['postsTypes']) );
					} 
					else
					{
						$this->_settings['FIELDS'][$key]['postsTypes'] = $this->getObjectsSlags();
					}
					
					if( !empty($FIELDS['postsIds']) )
					//Если в свойствах указана связь с конкретными постами (указаны ID)
					{
						//Разбираем ID постов
						$this->_settings['FIELDS'][$key]['postsIds'] = array_map('trim', explode(',', $FIELDS['postsIds']) );
					}					
					
					if( !empty($FIELDS['targetPostType']) )
					//Если в свойствах указаны целевые объекты, к которым нужно обращаться
					{
						//Разбираем связь с объектами
						$this->_settings['FIELDS'][$key]['targetPostType'] = array_map('trim', explode(',', $FIELDS['targetPostType']) );
					}
					
					if( !empty($FIELDS['targetPostTax']) )
					//Если в свойствах указаны таксономии целевых объектов
					{
						//Разбираем связь с таксономиями
						$targetPostTax = explode('(', $FIELDS['targetPostTax']);
						$taxName = $targetPostTax[0];
						$targetPostTax = explode(')', $targetPostTax[1]);
						$operator = $targetPostTax[0];
						$targetPostTax = array_map('trim', explode(',', $targetPostTax[1]) );
						
						$target['name'] = $taxName;
						$target['operator'] = $operator;
						
						if( is_numeric($targetPostTax[0]) ){
							$target['ids'] = $targetPostTax;
						}
						else{
							$target['slugs'] = $targetPostTax;
						}
						
						$this->_settings['FIELDS'][$key]['targetPostTax'] = $target;
					}					
					
					if( !empty($FIELDS['targetPostField']) )
					//Если в свойствах указаны таксономии целевых объектов
					{
						//Разбираем связь с таксономиями
						$targetPostField = explode('(', $FIELDS['targetPostField']);
						$FieldName = $targetPostField[0];
						$targetPostField = explode(')', $targetPostField[1]);
						$operator = $targetPostField[0];
						$targetPostField = array_map('trim', explode(',', $targetPostField[1]) );
						
						$target['name'] = $FieldName;
						$target['operator'] = $operator;
						
						if( is_numeric($targetPostField[0]) ){
							$target['ids'] = $targetPostField;
						}
						else{
							$target['slugs'] = $targetPostField;
						}
						
						$this->_settings['FIELDS'][$key]['targetPostField'] = $target;
					}
					
					//Если это поле гугл карты, у него можеть быть только предустановленный бокс.
					if($FIELDS['type'] == 'googlemap'){
						$this->_settings['FIELDS'][$key]['boxesIds'] = array('autocomplete_map_extra_field_googlemap');
					}					
					elseif( !empty($FIELDS['boxesIds']) )
					//Если в свойствах указана связь с боксами
					{
						//Разбираем связь с боксами
						$this->_settings['FIELDS'][$key]['boxesIds'] = array_map('trim', explode(',', $FIELDS['boxesIds']) );
					} 
					else
					{
						$this->_settings['FIELDS'][$key]['boxesIds'] = array('extra_fields_default');
					}
				}
    	}		
		
		//После того как файлы конфигурации объектов и таксономий разобраны, мы можем разобрать связь объектов и таксономий по умолчанию (если она не была назначена в конфигурации)
    	if(file_exists(IGNET_CONFIG_DIR . self::OBJECTS_CONFIG_FILE)){
			foreach($this->_settings['OBJECTS'] as $key=>$OBJECT){
				if( empty($OBJECT['taxonomies']))
				{
					//Конкретные slugs назначенные для этого конкретного объекта
					$this->_settings['OBJECTS'][$key]['taxonomies'] = $this->getTaxonomySlags($OBJECT['slug']);
				}
				
				if( empty($OBJECT['supports']))
				{
					//Включенные блоки редактирования по умолчанию 
					$this->_settings['OBJECTS'][$key]['supports'] = array('title','editor');
				}				
			}
    	}
	
    	if(file_exists(IGNET_CONFIG_DIR . self::TAXONOMY_CONFIG_FILE)){
			foreach($this->_settings['TAXONOMY'] as $key=>$TAXONOMY){
				if( empty($TAXONOMY['postsTypes']))
				{
					//Все slugs пользовательских объектов
					$this->_settings['TAXONOMY'][$key]['postsTypes'] = $this->getObjectsSlags();
				}
			}
    	}	
	

		if(file_exists(IGNET_CONFIG_DIR . self::BOXES_CONFIG_FILE)){
			$BOXES = parse_ini_file( IGNET_DEF_DIR . 'Config/' . self::BOXES_CONFIG_FILE, true);
			
			$BOXES[] = array(
				'id' => 'extra_fields_default',
				'title' => 'Свойства объекта',
				'context' => 'advanced',
				'priority' => 'default',
			);
			//print_r($BOXES);
			foreach($BOXES as $key=>$BOX){
				if( !empty($BOX['id']))
				{
					foreach($this->_settings['FIELDS'] as $FIELD){
						if( in_array($BOX['id'], $FIELD['boxesIds']) ){
							
							$this->_settings['BOXES'][$key] = $BOX;
							
							if( empty($BOX['title']))
							{
								$this->_settings['BOXES'][$key]['title'] = 'Свойства объекта';
							}
							
							if( empty($BOX['postsTypes']))
							{
								//Все slugs пользовательских объектов
								$this->_settings['BOXES'][$key]['postsTypes'] = $this->getObjectsSlags();
							}
							else
							{
								//Парсим связи с объектами
								$this->_settings['BOXES'][$key]['postsTypes'] = array_map('trim', explode(',', $BOX['postsTypes']) );
							}
							
							if( empty($BOX['context']))
							{
								$this->_settings['BOXES'][$key]['context'] = 'advanced';
							}				
							
							if( empty($BOX['priority']))
							{
								$this->_settings['BOXES'][$key]['priority'] = 'default';
							}
							
							break;
						}
					}
				}
			}
		}
	}
	
	//Отдать все slugs всех объектов связанных с указанной таксономией
	//Если таксономия не указана, отдаются slugs всех объектов
	public function getObjectsSlags($taxo = false){
		$slugs = array();
		foreach($this->_settings['OBJECTS'] as $one){
			if(!$taxo){
				$slugs[] = $one['slug'];
			}
			else{
				if( in_array($taxo, $one['taxonomies']) ){
					$slugs[] = $one['slug'];
				}
			}
		}
		return $slugs;
    }

	//Отдать все slugs всех таксономий связанных с указанным объектом
	//Если объект не указан, отдаются slugs всех таксономий	
	public function getTaxonomySlags($object_slugs = false){
		$slugs = array();
		foreach($this->_settings['TAXONOMY'] as $one){
			if(!$object_slugs){
				$slugs[] = $one['slug'];
			}
			else{
				if( !empty($one['postsTypes']) AND in_array($object_slugs, $one['postsTypes']) ){
					$slugs[] = $one['slug'];
				}
			}
		}		
		return $slugs;
    }	
	
	//Отдать все slugs всех пользовательских полей связанных с указанным объектом
	//Если объект не указан, отдаются slugs всех пользовательских полей	
	public function getFieldsSlags($object_slugs = false){
		$slugs = array();
		foreach($this->_settings['FIELDS'] as $one){
			if(!$object_slugs){
				$slugs[] = $one['slug'];
			}
			else{
				if( in_array($object_slugs, $one['postsTypes']) ){
					$slugs[] = $one['slug'];
				}
			}
		}		
		return $slugs;
    }
	
	//Отдать все slugs всех пользовательских полей и таксономий для фильра по указанному объекту (учитывается порядок, указанный в конфигурации)	
	public function getFilterSlags( $object_slugs ){
		
		$rez = array();
		$slugs = array();
		$ALL_OBJECTS = $this->getObjectsSlags();
		if( !in_array($object_slugs, $ALL_OBJECTS) ) return $slugs;
		
		foreach($this->_settings['TAXONOMY'] as $one){
			if( in_array($object_slugs, $one['postsTypes']) AND is_numeric($one['filter_order']) ){
				$slugs[] = array(
								'type' => 'taxonomy',
								'slug' => $one['slug'],
								'order' => $one['filter_order']
							);
			}
		}
		foreach($this->_settings['FIELDS'] as $one){
			if( in_array($object_slugs, $one['postsTypes']) AND is_numeric($one['filter_order']) ){
				$slugs[] = array(
								'type' => 'fields',
								'slug' => $one['slug'],
								'order' => $one['filter_order']
							);
			}		
		}
		
		foreach($slugs as $one){
			if( !isset($rez[$one['order']]) ){
				$rez[$one['order']] = array(
										'type' => $one['type'],
										'slug' => $one['slug']
										);
			}
			else{
				$order = $one['order'];
				$i = 1;
				while (isset($rez[$order])):
					$order = $one['order'] . '.' . $i;
					$i++;
				endwhile;
				
				$rez[$order] = array(
									'type' => $one['type'],
									'slug' => $one['slug']
									);
			}
		}
		ksort($rez);

		return $rez;
    }

	
	public function getFieldsByType( $object_slugs = false, $type = false ){
		$ALL_FIELDS = $this->getToInit('FIELDS');
		$return_fields = array();
		foreach($ALL_FIELDS as $name=>$field){
			if( $object_slugs ){
				if(in_array($object_slugs, $field['postsTypes']) ){
					if( $type ){
						if( $type == $field['type']){
							$return_fields[] = $field;
						}
					}
					else{
						$return_fields[] = $field;	
					}
				}			
			}
			else{
				if( $type ){
					if( $type == $field['type']){
						$return_fields[] = $field;
					}
				}
				else{
					$return_fields[] = $field;	
				}				
			}
		}
		return $return_fields;
	}

	public function getBoxesByObject( $object_slugs ){
		$BOXES_BY_OBJECT = array();
		//print_r($this->_settings['BOXES']);
		foreach($this->_settings['BOXES'] as $key=>$BOX){
			if( in_array($object_slugs, $BOX['postsTypes']) ){
				$BOXES_BY_OBJECT[$key] = $BOX;
			}
		}
		return $BOXES_BY_OBJECT;
	}	
	
	public function getFieldsByBox( $box_id ){
		$FIELDS_BY_BOX = array();
		foreach($this->_settings['FIELDS'] as $FIELD){
			if( in_array($box_id, $FIELD['boxesIds']) ){
				$FIELDS_BY_BOX[] = $FIELD;
			}
		}

		return $FIELDS_BY_BOX;
	}	
	
	//Отдать массив данных по указанной конфигурации (объекты / такосномии / пользовательские поля )
	public function getToInit($name){
		if(isset($this->_settings[$name])) return $this->_settings[$name];
    }
}