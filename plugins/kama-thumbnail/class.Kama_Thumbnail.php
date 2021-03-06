<?php

class Kama_Thumbnail {

	public static $opt_name = 'kama_thumbnail';
	public static $opt;

	function __get( $name ){
		if( $name == 'opt' ) return self::$opt;
	}

	function __construct(){
		self::$opt = (object) ( ($tmp = get_option(self::$opt_name)) ? $tmp : self::def_options() );

		if( ! @ self::$opt->no_photo_url ) self::$opt->no_photo_url = KT_URL .'no_photo.png';
		if( ! @ self::$opt->cache_folder ) self::$opt->cache_folder = str_replace('\\', '/', WP_CONTENT_DIR . '/cache/thumb');
		if( ! @ self::$opt->cache_folder_url ) self::$opt->cache_folder_url = content_url() .'/cache/thumb';

		// allow_hosts
		$ah = & self::$opt->allow_hosts;
		if( $ah && ! is_array($ah) ){
			$ah = preg_split('/[\s,]+/s', trim( $ah ) ); // сделаем массив
			foreach( $ah as & $host )
				$host = str_replace('www.', '', $host );
		}
		else
			$ah = array();

		$this->wp_init();
	}

	function wp_init(){
		// админка
		if( is_admin() && ! defined('DOING_AJAX') ){
			add_action('admin_menu', array( & $this, 'admin_options') ); // закомментируйте, чтобы убрать опции из админки
			add_action('admin_menu', array( & $this, 'claer_handler') );

			add_filter('save_post', array( & $this, 'clear_post_meta') );

			// ссылка на настойки со страницы плагинов
			add_filter('plugin_action_links', array( & $this, 'setting_page_link'), 10, 2 );
		}

		if( self::$opt->use_in_content ){
			add_filter('the_content', array( & $this, 'replece_in_content') );
			add_filter('the_content_rss', array( & $this, 'replece_in_content') );
		}

		if( ! defined('DOING_AJAX') ){
			// l10n
			$locale = get_locale();
			if( $locale != 'ru_RU' ){
				$patt   = KT_DIR . 'lang/'. self::$opt_name .'-%s.mo';
				$mofile = sprintf( $patt, $locale );
				if( ! file_exists( $mofile ) )
					$mofile = sprintf( $patt, 'en_US' );

				load_textdomain( self::$opt_name, $mofile );
			}
		}

	}

	public static function def_options(){
		return array(
			'meta_key'       => 'photo_URL',
			'cache_folder'   => '', // полный путь до папки миниатюр
			'cache_folder_url' => '', // URL до папки миниатюр
			'no_photo_url'   => '', // УРЛ на заглушку
			'use_in_content' => 'mini',  // искать ли класс mini у картинок в тексте, чтобы изменить их размер
			'auto_clear'     => 0,
			'no_stub'        => 0,
			'quality'        => 85,
			'subdomen'       => '', // поддомены на котором могут быть исходные картинки (через запятую): img.site.ru,img2.site.ru
			'allow_hosts'    => '', // доступные хосты, кроме родного, через запятую
		);
	}

	## Функции поиска и замены в посте
	function replece_in_content( $content ){
		$miniclass = (self::$opt->use_in_content == 1) ? 'mini' : self::$opt->use_in_content;

		if( false !== strpos( $content, '<img ') && false !== strpos( $content, $miniclass) ){
			$img_ex = '<img([^>]*class=["\'][^\'"]*(?<=[\s\'"])'. $miniclass .'(?=[\s\'"])[^\'"]*[\'"][^>]*)>';
			// разделение ускоряет поиск почти в 10 раз
			$content = preg_replace_callback("~(<a[^>]+>\s*)$img_ex|$img_ex~", array( & $this, '__replece_in_content'), $content );
		}

		return $content;
	}
	function __replece_in_content( $m ){
		$a_prefix = $m[1];
		$is_a_img = '<a' === substr( $a_prefix, 0, 2);
		$attr = $is_a_img ? $m[2] : $m[3];

		$attr = trim( $attr, '/ ');

		// get src="xxx"
		preg_match('~src=[\'"]([^\'"]+)[\'"]~', $attr, $match_src );
		$src = $match_src[1];
		$attr = str_replace( $match_src[0], '', $attr );

		// make args from attrs
		$args = preg_split('~ *(?<!=)["\'] *~', $attr );
		$args = array_filter( $args );

		$_args = array();
		foreach( $args as $val ){
			list( $k, $v ) = preg_split('~=[\'"]~', $val );
			$_args[ $k ] = $v;
		}
		$args = $_args;

		// parse srcset if set
		if( isset($args['srcset']) ){
			$srcsets = array_map('trim', explode(',', $args['srcset']) );
			$_cursize = 0;
			foreach( $srcsets as $_src ){
				preg_match('/ ([0-9]+[a-z]+)$/', $_src, $mm );
				$size = $mm[1];
				$_src = str_replace( $mm[0], '', $_src );

				// retina
				if( $size === '2x' ){
					$src = $_src;
					break;
				}

				$size = intval($size);
				if( $size > $_cursize )
					$src = $_src;

				$_cursize = $size;
			}

			unset( $args['srcset'] );
		}

		//print_r($args + [$src]); echo "\n\n\n\n";
		$kt = new Kama_Make_Thumb( $args, $src );

		return $is_a_img ? $a_prefix . $kt->img() : $kt->a_img();
	}

	## Удалет произвольное поле со ссылкой при обновлении поста, чтобы создать его снова
	function clear_post_meta( $post_id ){
		update_post_meta( $post_id, self::$opt->meta_key, '' );
	}


	### ADMIN PART -----------------------------------
	static function activation(){
		if( ! get_option(self::$opt_name) )
			update_option( self::$opt_name, self::def_options() );
	}

	function uninstall(){
		$this->clear_cache();
		$this->del_customs();

		delete_option( self::$opt_name );
		@ rmdir( $this->opt->cache_folder );
	}

	## для вывода сообещний в админке
	static function show_message( $text = '', $class = 'updated' ){
		$echo = '<div id="message" class="'. $class .' notice is-dismissible"><p>'. $text .'</p></div>';
		add_action('admin_notices', create_function('', "echo '$echo';" ) );
	}

	function admin_options(){
		// Добавляем блок опций на базовую страницу "Чтение"
		add_settings_section('kama_thumbnail', __kt('Настройки Kama Thumbnail'), '', 'media' );

		// Добавляем поля опций. Указываем название, описание,
		// функцию выводящую html код поля опции.
		add_settings_field( 'kt_options_field',
			'
			<a href="?kt_clear=clear_cache_stub" class="button">'. __kt('Очистить кэш заглушек') .'</a> <br><br>
			<a href="?kt_clear=clear_cache" class="button">'. __kt('Очистить весь кеш') .'</a> <br><br>
			<a href="?kt_clear=del_customs" class="button">'. __kt('Удалить произвольные поля') .'</a>',
			array( & $this, 'options_field'), // можно указать ''
			'media', // страница
			'kama_thumbnail' // секция
		);

		// Регистрируем опции, чтобы они сохранялись при отправке
		// $_POST параметров и чтобы callback функции опций выводили их значение.
		register_setting('media', self::$opt_name );
	}

	function options_field(){
		$opt_name = self::$opt_name;
		$opt = (object) get_option( $opt_name );

		$def_opt = (object) self::def_options();

		$out = '';

		$out .= '
		<input type="text" name="'. $opt_name .'[cache_folder]" value="'. $opt->cache_folder .'" style="width:100%;" placeholder="'. $this->opt->cache_folder .'"><br>
		'. __kt('Полный путь до папки кэша с правами 755 и выше. По умолчанию: пусто.') .'
		<br><br>

		<input type="text" name="'. $opt_name .'[cache_folder_url]" value="'. $opt->cache_folder_url .'" style="width:100%;" placeholder="'. $this->opt->cache_folder_url .'"><br>
		'. __kt('УРЛ папки кэша. По умолчанию: пусто.') .'
		<br><br>

		<input type="text" name="'. $opt_name .'[no_photo_url]" value="'. $opt->no_photo_url .'" style="width:100%;" placeholder="'. $this->opt->no_photo_url .'"><br>
		'. __kt('УРЛ картинки заглушки. По умолчанию: пусто.') .'
		<br><br>

		<input type="text" name="'. $opt_name .'[meta_key]" value="'. $opt->meta_key .'" class="regular-text"><br>
		'. __kt('Название произвольного поля, куда будет записываться УРЛ миниатюры. По умолчанию:') .' <code>'. $def_opt->meta_key .'</code>
		<br><br>

		<textarea name="'. $opt_name .'[allow_hosts]" style="width:100%;height:45px;">'. esc_textarea($opt->allow_hosts) .'</textarea><br>
		'. __kt('Хосты с которых разрешается создавать миниатюры. 1 на строке. Пр.: <i>sub.mysite.com</i>. Укажите здесь <code>any</code>, чтобы можно было использовать любые хосты.') .'
		<br><br>

		<input type="text" name="'. $opt_name .'[quality]" value="'. $opt->quality .'" style="width:50px;">
		'. __kt('Качество создаваемых миниатюр от 0 до 100. По умолчанию:') .' <code>'. $def_opt->quality .'</code>
		<br><br>

		<label>
			<input type="checkbox" name="'. $opt_name .'[no_stub]" value="1" '. checked(1, @ $opt->no_stub, 0) .'> '. __kt('Не показывать картинку-заглушку.') .'
		</label><br><br>

		<label>
			<input type="checkbox" name="'. $opt_name .'[auto_clear]" value="1" '. checked(1, @ $opt->auto_clear, 0) .'> '. __kt('Автоматическая очистка всего кэша каждые 7 дней.') .'
		</label><br><br>

		<label>
			<input type="text" name="'. $opt_name .'[use_in_content]" value="'.( isset($opt->use_in_content) ? esc_attr($opt->use_in_content) : 'mini' ).'"> '. __kt('Искать указанный класс у картинки в тексте поста и сделать из нее миниатюру по указанным у нее размерам. Оставьте поле пустым, чтобы отключить поиск. По умолчанию: <code>mini</code>') .'
		</label>
		';

		echo $out;
	}

	function setting_page_link( $actions, $plugin_file ){
		if( false === strpos( $plugin_file, basename(KT_DIR) ) ) return $actions;

		$settings_link = '<a href="'. admin_url('options-media.php') .'">'. __kt('Настройки') .'</a>';
		array_unshift( $actions, $settings_link );

		return $actions;
	}
	### / ADMIN PART -----------------------------------


	### CLEAR -----------------------------------
	function claer_handler(){
		if( isset($_GET['kt_clear']) && current_user_can('manage_options') )
			return $this->force_clear( $_GET['kt_clear'] );

		if( isset( self::$opt->auto_clear ) )
			$this->clear();

		$this->clear_stub();
	}

	function clear(){
		$cache_dir = self::$opt->cache_folder;
		$expire_time = time() + (3600*24*7);

		$expire = @ file_get_contents( $cache_dir .'/expire');
		if( $expire && (int) $expire < time() )
			$this->clear_cache();

		@ file_put_contents( $cache_dir .'/expire', $expire_time );

		return;
	}

	function clear_stub(){
		$cache_dir = self::$opt->cache_folder;
		$expire_time = time() + (3600*24); // очистка заглушек каждый день
		//$expire_time = time() + 20; // тест

		$expire = @ file_get_contents( $cache_dir .'/expire_stub');
		if( $expire && (int) $expire < time() )
			$this->clear_cache('only_stub');

		@ file_put_contents( $cache_dir .'/expire_stub', $expire_time );

		return;
	}

	## ?kt_clear=clear_cache - очистит кеш картинок ?kt_clear=del_customs - удалит произвольные поля
	function force_clear( $do ){
		switch( $do ){
			case 'clear_cache': $text = $this->clear_cache(); break;
			case 'clear_cache_stub': $text = $this->clear_cache('only_stub'); break;
			case 'del_customs': $text = $this->del_customs(); break;
		}
	}

	function clear_cache( $only_stub = '' ){
		if( ! $cache_dir = self::$opt->cache_folder ){
			self::show_message( __kt('Путь до папки кэша не установлен в настройках.'), 'error');
			return false;
		}

		if( ! is_dir($cache_dir) ) return true;

		$find = $cache_dir .'/*';
		if( $only_stub )
			$find = $cache_dir .'/stub_*';

		foreach( glob( $find ) as $obj )
			unlink($obj);

		if( $only_stub )
			self::show_message( __kt('Картинки-заглушки были удалены из кэша <b>Kama Thumbnail</b>.') );
		else
			self::show_message( __kt('Кэш <b>Kama Thumbnail</b> был очищен.') );

		return true;
	}

	function del_customs(){
		global $wpdb;
		if( ! self::$opt->meta_key )
			return self::show_message('meta_key option not set.', 'error');

		if( $wpdb->delete( $wpdb->postmeta, array('meta_key'=>self::$opt->meta_key ) ) )
			self::show_message( sprintf( __kt('Все произвольные поля <code>%s</code> были удалены.'), self::$opt->meta_key ) );
		else
			self::show_message( sprintf( __kt('Не удалось удалить произвольные поля <code>%s</code>'), self::$opt->meta_key ) );

		return;
	}
	### / CLEAR -----------------------------------

}


