<?php
require 'functions/recipes.php';

class ftheme {
	function __construct() {
		//create menus
		// $navs = array(
		// 	'main' => 'Main Menu',
		// 	'footer' => 'Footer Menu'
		// );

		// register_nav_menus($navs);

		//create options page
		if( function_exists('acf_add_options_page') ) {
			acf_add_options_page();
		}

		//add stylesheets and javascript files
		add_action('wp_enqueue_scripts', array($this, 'site_scripts'));
	}


	public function site_scripts() {
		$tem_dir = get_template_directory();
		$tem_dir_uri = get_template_directory_uri();


		//main stylesheet
		$files = scandir($tem_dir . '/css/');
		$ver = 0;
		$style_files = array();
		foreach($files as $file) {
			if ($file != '.' && $file != '..' && strpos($file, '.scss') !== false) {
				$style_files[] = $file;
			}
		}
		foreach($style_files as $f) {
			$t = filemtime($tem_dir . '/css/' . $f);
			if ($t > $ver) {
				$ver = $t;
			}
		}
		wp_enqueue_style('site_style', $tem_dir_uri . '/css/style.php?p=style.scss', array(), 'v' . $ver);

		//jquery
		wp_deregister_script('jquery');
      	wp_register_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js');
		wp_enqueue_script('jquery', '', array(), false, true);

		self::add_script('script.js');
	}

	public static function load_partials() {
		do_action('ftheme_load_partials');

		$tem_dir = get_template_directory();

		$partial_files = array();
		$files = scandir($tem_dir . '/partials/');
		foreach($files as $file) {
			if ($file != '.' && $file != '..' && strpos($file, '.php') !== false) {
				$partial_files[] = $file;
			}
		}

		foreach($partial_files as $file) {
			require $tem_dir . '/partials/' . $file;
		}
	}

	public static function add_script($filename) {
		$file = '/js/' . $filename;
		$tem_dir = get_template_directory();
		$tem_dir_uri = get_template_directory_uri();
		$ver = filemtime($tem_dir . $file);
		wp_enqueue_script($file, $tem_dir_uri . $file, array('jquery'), 'v' . $ver, true);

	}
}

new ftheme();

