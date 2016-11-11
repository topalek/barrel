<?php
/**
Plugin Name: Kama SpamBlock
Version: 1.5.2
Plugin URI: http://wp-kama.ru/?p=95
Description: Block auto-spam when spam comment is posted. Pings and trackbacks cheks for real backlink. If you need completely disable blocking, define the constant: <code>define('KS_FORCE_DISABLE', 1);</code>
Author: Kama
Author URI: http://wp-kama.ru/
*/  


add_action('init', create_function('','new Kama_Spamblock();') );


class Kama_Spamblock {
	private $nonce;
	public $opt;
	const  OPT_NAME = 'ks_options';
	
	function __construct(){
		! $this->opt = get_option( self::OPT_NAME ) AND $this->opt = $this->def_opt();
		$this->opt = (object) $this->opt;
		
		# translation 
		load_textdomain('ks', dirname(__FILE__) . '/'. get_locale() .'.mo' );

		# admin
		if( is_admin() )
			$this->admin_init();
		
		# blocking for front-end only
		if( ! is_admin() && ! defined('KS_FORCE_DISABLE') )
			$this->init();
	}
	
	function init(){
		# to exactly output js
		// add_action('comment_form', array($this, 'main_js') );
		add_action('get_footer',   array($this, 'main_js') );
		add_action('wp_footer',    array($this, 'main_js') );

		$this->nonce = preg_replace('@\d@', '', md5( date('dm') ) );
		
		add_action('preprocess_comment', array($this, 'spamblock'), 0);
	}

	## blocking
	function spamblock( $commentdata ){	
		$ctype = &$commentdata['comment_type'];
		
		# Pings and trackbacks protect
		if( $ctype == 'trackback' || $ctype == 'pingback' ){
			if( ! $pars = @file_get_contents( $commentdata['comment_author_url'] ) )
				return $commentdata;
			if( ! preg_match('@<a[^>]+href=[\'"]'. preg_quote( get_option('home') ) .'@si', $pars) )
				die;
		}

		# spam blocking. Only for comment_type == '' (comment)
		if( $ctype == '' && (trim( @$_POST['code'] ) != $this->nonce) )
			wp_die( $this->block_form() );

		return $commentdata;
	}
	
	function main_js(){
		static $done; if( isset($done) ) return; $done=1; // do it once
		
		// if( ! is_singular() ) return; // in some themes this check not work
		?>
		<script type="text/javascript">/* <![CDATA[ */
			try{ var sbmt = document.getElementById('<?php echo $this->opt->sibmit_button_id ?>');
			var npt = document.createElement('input'); npt.value = '<?php echo $this->nonce ?>'; npt.name = 'code'; npt.type = 'hidden';
			var ksinit = function(){ sbmt.parentNode.insertBefore( npt, sbmt ); }
			sbmt.onmousedown = ksinit; sbmt.onkeypress = ksinit; }catch(e){}
		/* ]]>*/
		</script>
		<?php
	}
	
	function block_form(){
		$out  = '<h1>'. __('Antispam block your comment!','ks') .'</h1>
		
		<form action="'. site_url('/wp-comments-post.php' ) .'" method="post">
			<p>'. __('Copy this code to the field:','ks') .' <code style="background:#eee;">'. $this->nonce .'</code> → <input type="text" name="code" value="" style="width:150px;" /> '. __('and press button:','ks') .'</p>
			 
			<input type="submit" style="height:70px;width:100%;font-size:35px;cursor:pointer;border:none;color:#fff;background:#555;" value="'. __('Send comment once again','ks') .'" />';
			unset( $_POST['code'] );
			foreach( $_POST as $k=>$v )
				$out .= '<textarea style="display:none;" name="'. $k .'">'. stripslashes($v) .'</textarea>';
		$out .= '</form>';
		
		return $out;
	}

	# default_options
	function def_opt(){
		return array(
			'sibmit_button_id'  => 'submit'
		);
	}
	
	
	
	## admin
	function admin_init(){
		add_action( 'admin_init', array( $this, 'admin_options' ) );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'settings_link') );
	}
	
	function settings_link( $links ){
		array_push( $links, '<a href="'. admin_url('/options-discussion.php#wpfooter') .'">'. __('Settings','ks') .'</a>' ); 
		return $links; 
	}
	
	function admin_options(){
		add_settings_section('kama_spamblock', '', '', 'discussion'); // set no title

		add_settings_field( self::OPT_NAME . '_field', __('Kama Spamblock settings','ks'), array( $this, 'options_field'), 'discussion', 'kama_spamblock' );

		register_setting( 'discussion', self::OPT_NAME );
	}
	function options_field(){
		echo '<input type="text" name="'. self::OPT_NAME .'[sibmit_button_id]" value="'. $this->opt->sibmit_button_id .'" style="width:200px;" /> — ';
		echo __('ID attribute of comment form submit button. Default: <code>submit</code>','ks');
	}
	
}