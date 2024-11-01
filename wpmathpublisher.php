<?php
/*
Plugin Name: WpMathPublisher
Plugin URI: http://devblog.kraeuterbruederchen.de/2012/04/wpmathpublisher-2/
Description: Allows to include formulas written for <a href="http://www.xm1math.net/phpmathpublisher/" title="PhpMathPublisher">PhpMathPublisher</a>. Formulas have to be put into [math][/math] - tags. For formula information visit: <a href="http://www.xm1math.net/phpmathpublisher/doc/help.html" title="PhpMathPublisher - Help">PhpMathPublisher-Page</a>
Author: Timm Severin
Author URI: http://kraeuterbruederchen.de
Text Domain: wpmathpublisher
Version: 0.6.4.1

Copyright:
	Plugin changes by Timm Severin (admin@kraeuterbruederchen.de)
	Original functionality (which really is the main part) by Pascal Brachet
		see mathpublisher.php for complete (original) copyright

Latest Changes:
	v 0.4 [2008-07-09]
		! Fixed keeping a variable for theme-page output that was meant to be only temporary
		+ added function to clear the image cache and let the images be regenerated
	v 0.4.1 [2009-02-12]
		! Fixed error occuring while installation
	v 0.4.2 [2009-03-08]
		! Cleaned up code
		- removed making background transparent bye choosing so, use alpha = 127 instead
		+ offers change of size for each individual image
	v 0.4.3 [2009-03-08]
		+ added noparse attribute
		+ added short user guide on plugins configuration page
		- removed deactivation hook to preserve configuration if plugin is updated
	v 0.4.3.1 [2009-03-08]
		! fixed a problem with the svn, reupload for complete installation
	v 0.4.4 [2009-09-*] (never uploaded)
		! fixed height of text in graphics as suggested by a productive user ;)
	v 0.5 [2009-10-09]
		- removed reading options written by older versions of this plugin 
		+ added german translation ("Sie" und "Du")
		+ will create image folder if possible
		+ if image folder is not writeable or could not be created, plugin will output a message in admin center
		+ added option to change default fontsize
		! exported display settings of admin page to css-file
	v 0.6 [2009-10-09]
		+ integration in tinymce 3.x
		+ outputs allowed font size
		+ added simple color-picker by integrating jscolor
		+ temporary added ability to import old database values - really got to get this saving stuff sorted out
		! update of translation
	v 0.6.1 [2009-10-17]
		+ added slider to set transparency
		+ added credits for all the people i got the scripts from
	v 0.6.2 [2009-10-17]
		! updated german translation
		! fixed display of &#8211; instead a minus, tinymce likes to change a minus to a dash
	v 0.6.3 [2010-03-22]
		+ added belorussian translation
	v 0.6.3.1 [2010-03-23]
		! fixed things this f*cking svn gui messed with
		! working up to Wordpress 2.8.6
	v 0.6.4 
		! fixed activation problem
		! fixed tinyMCE integration (language issue)
		+ added romanian translation (since this update not complete) by Alexander Ovsov [ http://webhostinggeeks.com/ ]
		! updated german translation
	v 0.6.4.1
		! fixed replacement of &gt; and &lt; done by tinymce
		
Roadmap:
	- add option to change default fontset
	- write ajax class to be able to create pictures dynamically
*/

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	
define('WPMP_TEXTDOMAIN', 'wpmathpublisher');
define('WPMP_FOLDER', basename(dirname(__FILE__)));
define('WPMP_BASEDIR', WP_PLUGIN_DIR.'/'.WPMP_FOLDER);
define('WPMP_BASEURL', WP_PLUGIN_URL.'/'.WPMP_FOLDER);

# Admin-Interface output function
function trigger_message($message, $errno) {
	if(isset($_GET['action']) && $_GET['action'] == 'error_scrape') {
        echo '<div style="font-size: 8pt; font-family: Tahoma;">' . $message . '</div>';
        exit;
    } else {
        trigger_error($message, $errno);
    }
}

## get other constants
include_once('constants.php'); 
	
## include PhpMathPublisher
include_once('mathpublisher.php');
  
class WpMathPublisher {
	var $font = array('color' => '000000', 'alpha' => 0);
	var $back = array('color' => 'ffffff', 'alpha' => 0);
	var $size = 12;
	var $imgPath = '';
	var $ready = true;
	
	// constructor
	function WpMathPublisher() {
		$this->__construct();
	}
	
	// constructor for later php versions
	function __construct() {
		// get path for saving images	
		$imgDir = WPMP_BASEDIR.'/img/';
		if(!is_dir($imgDir))
			$this->ready = false;
		if(!is_writeable($imgDir))
			$this->ready = false;
				
		$this->imgPath = WPMP_BASEURL.'/img/';

		$this->readOptions();
	}
	
	## add theme page
	function admin_menu() {
		wp_enqueue_style('wpmathpublisher', WPMP_BASEURL.'/style.css');
		wp_enqueue_script('wpmathpublisher-jscolor', WPMP_BASEURL.'/inc/jscolor/jscolor.js');
		wp_enqueue_script('wpmathpublisher-slider', WPMP_BASEURL.'/inc/slider.js');
		add_theme_page(__('WpMathPublisher', WPMP_TEXTDOMAIN), __('WpMathPublisher', WPMP_TEXTDOMAIN), 5, WPMP_BASEDIR.'/inc/admin_themePage.php');
	}
	
	## initialization function
	function init() {
		# get localization
		load_plugin_textdomain(WPMP_TEXTDOMAIN, WPMP_BASEDIR.'/locale', WPMP_FOLDER.'/locale');
		
		# output information if plugin is not ready
		if(!$this->ready)
			echo '<div id="wpmp-warning" class="updated fade"><p><strong>'.__('WpMathPublisher not yet ready: ').'</strong>'.sprintf(__('Couldn\'t access image directory [%s], create it and chmod it to 777.', WPMP_TEXTDOMAIN), WPMP_FOLDER.'/img').'</p></div>';

		# add tinymce buttons (only if necessary)
		if(( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') ) {
			add_filter("mce_external_plugins", array($this, 'addTinyMcePlugin'));
			add_filter('mce_buttons', array($this, 'registerButton'));
		}
		
	}
	
	## function to add button to tinymce
	function registerButton($_buttons) {
		array_push($_buttons, 'wpmathpublisher', '|');
		return $_buttons;
	}
	
	## function to add plugin
	function addTinyMcePlugin($_pluginArray) {
		$_pluginArray['wpmathpublisher'] = WPMP_BASEURL.'/tinymce/editor_plugin.js';
		return $_pluginArray;
	}
	
	## activation / deactivation
	function activate() {
		$message = ''; $failed = false;
		if(!is_dir(WPMP_BASEDIR.'/img/')) {
			$message .= __('Image directory does not exist. Trying to create it...');
			if((substr(sprintf('%o', fileperms(WPMP_BASEDIR)), -4) != "0777") or (!@mkdir(WPMP_BASEDIR.'/img/', 0777))){
				$message .= __('creating failed, permission denied. Please do the following steps manually:');
				$message .= '
					<ol>
						<li>'.sprintf(__('Use a FTP-Client to access your wordpress installation and go to the WpMathPublisher-Plugin directory %s'), WPMP_BASEDIR).'</li>
						<li>'.__('Create a subdirectory "img"').'</li>
						<li>'.__('Grant full write access to the image directory (<i>chmod 777</i>)').'</li>
					</ol>
				';
				// seems not to be necessary
				#deactivate_plugins(basename(__FILE__)); // Deactivate itself
				
				$failed = true;
			} else {
				$message .= __('successfull');
			}
		} elseif(substr(sprintf('%o', fileperms(WPMP_BASEDIR.'/img/')), -4) != "0777") {
			$message .= __('Permission to write to image directory has been denied. Trying to set permissions...');
			if((substr(sprintf('%o', fileperms(WPMP_BASEDIR)), -4) != "0777") or (!@chmod(WPMP_BASEDIR.'/img/', 0777))){
				$message .= __('failed to set permissions. Please do the following steps manually:');
				$message .= '
					<ol>
						<li>'.sprintf(__('Use a FTP-Client to access your wordpress installation and go to the WpMathPublisher-Plugin directory %s'), WPMP_BASEDIR).'</li>
						<li>'.__('Grant full write access to the image directory (<i>chmod 777</i>)').'</li>
					</ol>
				';
				// seems not to be necessary
				#deactivate_plugins(basename(__FILE__)); // Deactivate itself
				$failed = true;
			} else {
				$message .= __('successfull');
			}
		}
		if(!empty($message)) { trigger_message($message, E_USER_ERROR); }
		if($failed) { exit; }
				
		## Check if update or newly installed
		if(get_option('wpmathpublisher_font') === false) {
			## new installation, setup default values
			add_option('wpmathpublisher_font', array('color' => '000000', 'alpha' => 0));
			add_option('wpmathpublisher_back', array('color' => 'ffffff', 'alpha' => 0));
			add_option('wpmathpublisher_size', 12);
		} elseif(isset($font['red'])) {
			## UPDATE, convert old values (earlier versions)
			$fontColor = $this->convertRgbToHex($font);
			update_option('wpmathpublisher_font', array('color' => $fontColor, 'alpha' => $font['alpha']));
			$backColor = $this->convertRgbToHex($back);
			update_option('wpmathpublisher_back', array('color' => $backColor, 'alpha' => $back['alpha']));
		} else {
			## no changes necessary
		}
	}
	
	function deactivate() {
		# clear database
		delete_option('wpmathpublisher_font');
		delete_option('wpmathpublisher_back');
		delete_option('wpmathpublisher_size');
		
		# clean filesystem
		self::clearCache();
		
		## delete image-directory if possible // not executed, why?
		#if(substr(sprintf('%o', fileperms(WPMP_BASEDIR)), -4) == "0777")
		#	rmdir(WPMP_BASEDIR.'/img');
	}
	
	## resets the plugins options to default values
	function toDefaults() {
		update_option('wpmathpublisher_font', array('color' => '000000', 'alpha' => 0));
		update_option('wpmathpublisher_back', array('color' => 'ffffff', 'alpha' => 0));
		update_option('wpmathpublisher_size', 12);
		
		$this->readOptions();
		
		return true;
	}
	
	## plugin working functions
	function readOptions() {
		if($font = get_option('wpmathpublisher_font'))
			$this->font = $font;
		if($back = get_option('wpmathpublisher_back'))
			$this->back = $back;
		if($size = get_option('wpmathpublisher_size'))
			$this->size = $size;
	}
	
	## function that checks whether given rgb values are valid
	function checkColor($_color, $_alpha=0) {
		$message = '';
		if(!preg_match_all('~#?[0-9a-f]{6}~i', $_color, $colors) || empty($colors))
			$message .= '<li>'. __('invalid color value', WPMP_TEXTDOMAIN).'</li>';
		if(!is_numeric($_alpha) || $_alpha < 0 || $_alpha > 127)
			$message .= '<li>'. __('transparency value is invalid', WPMP_TEXTDOMAIN).'</li>';
		
		return $message;
	}
	
	## function to convert hex to rgb
	function convertHexToRgb($_hex) {
		$rgb = array(
			'red' => hexdec(substr($_hex, 0, 2)),
			'green' => hexdec(substr($_hex, 2, 2)),
			'blue' => hexdec(substr($_hex, 4, 2))
		);

		return $rgb;
	}
	
	## function to convert rgb to hex
	function convertRgbToHex($_colors) {
		return sprintf('#%02X%02X%02X', $_colors['red'], $_colors['green'], $_colors['blue']);
	}
	
	## parses shortcode
	function parseMath($attributes, $content) {
		$attrString = '';
		
		if(!$this->ready) {
			foreach($attributes as $key => $value)
				$attrString .= ' '.$key.'='.$value;
			
			$output = '[math'.$attrString.']'.$content.'[/math]';
			
			return $output;
		} elseif(isset($attributes['noparse']) && $attributes['noparse'] == 'true') {
			unset($attributes['noparse']);
			foreach($attributes as $key => $value)
				$attrString .= ' '.$key.'='.$value;
			
			$output = '[math'.$attrString.']'.$content.'[/math]';
			return $output;
		}
		// replace special chars (tinymce likes to do the replacement of some letters
		$searchArray = array(
			'–', '&#8211;', // dash => minus
			'&lt;', '&gt;' // &lt; => < // &gt; => >
		);
		$replaceArray = array(
			'-', '-', // minus <= dash
			'<', '>'
		);
		$content = str_replace($searchArray, $replaceArray, $content);
		
		// read size information
		$size = (!isset($attributes['size']) || empty($attributes['size'])) ? $this->size : $attributes['size'];
		return mathimage($content, $size, $this->imgPath);
	}
	
	## function to delete all images in cache
	static function clearCache() {
		$basePath = WPMP_BASEDIR.'/img';
	
		$dir = dir($basePath);
		$count = 0;
		while (false !== ($entry = $dir->read())) {
			$path = $basePath.DIRECTORY_SEPARATOR.$entry;
			
			if(is_file($path)) {
				unlink($path);
				$count++;
			}
		}
		
		return $count;
	}

	## function to set the background-color of the images
	function getBackgroundColor($_im) {
		$color = $this->convertHexToRgb($this->back['color']);
		$color = imagecolorallocatealpha($_im, $color['red'], $color['green'], $color['blue'], $this->back['alpha']);
		return $color;
	}
	
	## function to get the font color
	function getFontColor($_im) {
		$color = $this->convertHexToRgb($this->font['color']);
		$color = imagecolorallocatealpha($_im, $color['red'], $color['green'], $color['blue'], $this->font['alpha']);
		return $color;
	}
}

$myWpMathPublisher =& new WpMathPublisher;

// Create data/clean up
register_activation_hook(__FILE__, array('WpMathPublisher', 'activate'));
register_deactivation_hook(__FILE__, array('WpMathPublisher', 'deactivate'));

add_action('init', array($myWpMathPublisher, 'init'));
add_action('admin_menu', array($myWpMathPublisher, 'admin_menu'));
add_shortcode('math', array($myWpMathPublisher, 'parseMath'));
add_shortcode('m', array($myWpMathPublisher, 'parseMath'));

if(!function_exists('wpMathPublisher_getFontColor')) {
	function wpMathPublisher_getFontColor($_im) {
		global $myWpMathPublisher;
		return $myWpMathPublisher->getFontColor($_im);
	}
}

if(!function_exists('wpMathPublisher_getBackgroundColor')) {
	function wpMathPublisher_getBackgroundColor($_im) {
		global $myWpMathPublisher;
		return $myWpMathPublisher->getBackgroundColor($_im);
	}
}
?>