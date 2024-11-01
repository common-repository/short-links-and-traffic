<?php
/**
Plugin Name: Short Links and Traffic
Description: Easily readable and speakable memorable short links controlled from within your WordPress dashboard using your domain name.
Version: 0.0.2
Author: klick on it
Author URI: http://klick-on-it.com
License: GPLv2 or later
Text Domain: klick-slt
 */

/*
This plugin developed by klick-on-it.com
*/

/*
Copyright 2017 klick on it (http://klick-on-it.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 3 - GPLv3)
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!defined('ABSPATH')) die('No direct access allowed');

if (!class_exists('Klick_Slt')) :
define('KLICK_SLT_VERSION', '0.0.1');
define('KLICK_SLT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KLICK_SLT_PLUGIN_MAIN_PATH', plugin_dir_path(__FILE__));
define('KLICK_SLT_PLUGIN_SETTING_PAGE', admin_url() . 'admin.php?page=klick_slt');

class Klick_Slt {

	protected static $_instance = null;

	protected static $_options_instance = null;

	protected static $_notifier_instance = null;

	protected static $_logger_instance = null;

	protected static $_dashboard_instance = null;

	protected static $_plugin_instance = null;
	
	/**
	 * Constructor for main plugin class
	 */
	public function __construct() {
		
		register_activation_hook(__FILE__, array($this, 'klick_slt_activation_actions'));

		register_deactivation_hook(__FILE__, array($this, 'klick_slt_deactivation_actions'));

		add_action('wp_ajax_klick_slt_ajax', array($this, 'klick_slt_ajax_handler'));
		
		add_action('admin_menu', array($this, 'init_dashboard'));
		
		add_action('plugins_loaded', array($this, 'setup_translation'));
		
		add_action('plugins_loaded', array($this, 'setup_loggers'));

		add_action( 'wp_footer', array($this, 'klick_slt_ui_scripts'));

		add_action('wp_head', array($this, 'klick_slt_ui_css'));

		if ($_SERVER["REQUEST_METHOD"] == 'GET' && !is_admin()) { 
		  add_action('plugins_loaded', array($this, 'slt_redirect'), 1);
		}
	}

	/**
	 * Instantiate klick_slt if needed
	 *
	 * @return object klick_slt
	 */
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Instantiate Klick_Slt_Options if needed
	 *
	 * @return object Klick_Slt_Options
	 */
	public static function get_options() {
		if (empty(self::$_options_instance)) {
			if (!class_exists('Klick_Slt_Options')) include_once(KLICK_SLT_PLUGIN_MAIN_PATH . '/includes/class-klick-slt-options.php');
			self::$_options_instance = new Klick_Slt_Options();
		}
		return self::$_options_instance;
	}
	
	/**
	 * Instantiate Klick_Slt_Dashboard if needed
	 *
	 * @return object Klick_Slt_Dashboard
	 */
	public static function get_dashboard() {
		if (empty(self::$_dashboard_instance)) {
			if (!class_exists('Klick_Slt_Dashboard')) include_once(KLICK_SLT_PLUGIN_MAIN_PATH . '/includes/class-klick-slt-dashboard.php');
			self::$_dashboard_instance = new Klick_Slt_Dashboard();
		}
		return self::$_dashboard_instance;
	}
	
	/**
	 * Instantiate Klick_Slt_Logger if needed
	 *
	 * @return object Klick_Slt_Logger
	 */
	public static function get_logger() {
		if (empty(self::$_logger_instance)) {
			if (!class_exists('Klick_Slt_Logger')) include_once(KLICK_SLT_PLUGIN_MAIN_PATH . '/includes/class-klick-slt-logger.php');
			self::$_logger_instance = new Klick_Slt_Logger();
		}
		return self::$_logger_instance;
	}
	
	/**
	 * Instantiate Klick_Slt_Notifier if needed
	 *
	 * @return object Klick_Slt_Notifier
	 */
	public static function get_notifier() {
		if (empty(self::$_notifier_instance)) {
			include_once(KLICK_SLT_PLUGIN_MAIN_PATH . '/includes/class-klick-slt-notifier.php');
			self::$_notifier_instance = new Klick_Slt_Notifier();
		}
		return self::$_notifier_instance;
	}
	
	/**
	 * Instantiate Klick_Slt_Redirection if needed
	 *
	 * @return object Klick_Slt_Redirection
	 */
	public static function get_redirection(){
		if (empty(self::$_plugin_instance)) {
			include_once(KLICK_SLT_PLUGIN_MAIN_PATH . '/includes/class-klick-slt-redirection.php');
			self::$_plugin_instance = new Klick_Slt_Redirection();
		}
		return self::$_plugin_instance;
	}
		
	/**
	 * Establish Capability
	 *
	 * @return string
	 */
	public function capability_required() {
		return apply_filters('klick_slt_capability_required', 'manage_options');
	}
	
	/**
	 * Init dashboard with menu and layout
	 *
	 * @return void
	 */
	public function init_dashboard() {
		$dashboard = $this->get_dashboard();
		$dashboard->init_menu();
		load_plugin_textdomain('klick-slt', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * To enqueue js at user side
	 *
	 * @return void
	 */
	public function klick_slt_ui_scripts(){
		$dashboard = $this->get_dashboard();
		$dashboard->init_user_end();
	}

	/**
	 * To enqueue css at user side
	 *
	 * @return void
	 */
	public function klick_slt_ui_css(){
		$dashboard = $this->get_dashboard();
		$dashboard->init_user_css();
		$this->get_redirection();
	}

	/**
	 * Perform post plugin loaded setup
	 *
	 * @return void
	 */
	public function setup_translation() {
		load_plugin_textdomain('klick-slt', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Creates an array of loggers, Activate and Adds
	 *
	 * @return void
	 */
	public function setup_loggers() {
		
		$logger = $this->get_logger();

		$loggers = $logger->klick_slt_get_loggers();
		
		$logger->activate_logs($loggers);
		
		$logger->add_loggers($loggers);
	}
	
	/**
	 * Ajax Handler
	 */
	public function klick_slt_ajax_handler() {

		$nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];

		if (!wp_verify_nonce($nonce, 'klick_slt_ajax_nonce') || empty($_POST['subaction'])) die('Security check');
		
		$parsed_data = array();
		$data = array();
		
		$subaction = sanitize_key($_POST['subaction']);
		
		$post_data = isset($_POST['data']) ? $_POST['data'] : null;
		
	
		parse_str(html_entity_decode($post_data), $parsed_data); // convert string to array

		switch ($subaction) {
			case "klick_slt_save_settings":
				$data['klick_slt_name'] = isset($parsed_data['klick_slt_name']) ? sanitize_text_field($parsed_data['klick_slt_name']) : null;
				$data['klick_slt_desc'] = isset($parsed_data['klick_slt_desc']) ? sanitize_text_field($parsed_data['klick_slt_desc']) : null;
				$data['klick_slt_url'] = isset($parsed_data['klick_slt_url']) ? sanitize_text_field($parsed_data['klick_slt_url']) : null;
				$data['klick_slt_id'] = isset($parsed_data['klick_slt_id']) ? sanitize_text_field($parsed_data['klick_slt_id']) : null;
				$data['klick_slt_command'] = isset($parsed_data['klick_slt_command']) ? sanitize_text_field($parsed_data['klick_slt_command']) : null;
				break;
			case "klick_slt_delete_row":
				$data['id'] = isset($parsed_data['id']) ? sanitize_text_field($parsed_data['id']) : null;
			case "klick_slt_edit_row":
				$data['id'] = isset($parsed_data['id']) ? sanitize_text_field($parsed_data['id']) : null;	
			case "klick_slt_reload":
				$data['reload'] = isset($parsed_data['reload']) ? sanitize_text_field($parsed_data['reload']) : null;	
				break;
			// Add more cases here if you add subaction in plugin
			default:
				error_log("klick_slt_Commands: ajax_handler: no such sub-action (" . esc_html($subaction) . ")");
				die('No such sub-action/command');
		}
		
		$results = array();
		
		// Get sub-action class
		if (!class_exists('klick_slt_Commands')) include_once(KLICK_SLT_PLUGIN_MAIN_PATH . 'includes/class-klick-slt-commands.php');

		$commands = new klick_slt_Commands();

		if (!method_exists($commands, $subaction)) {
			error_log("klick_slt_Commands: ajax_handler: no such sub-action (" . esc_html($subaction) . ")");
			die('No such sub-action/command');
		} else {
			$results = call_user_func(array($commands, $subaction), $data);

			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
					);
			}
		}
		
		echo json_encode($results);
		die;
	}

	/**
	 * To get current page url and converts to array separated by slash
	 *
	 * @return object klick_slt
	 */
	public  function slt_redirect() {
		$request_uri = "http:/" . $_SERVER['REQUEST_URI'];
		preg_match('#^(https?://.*?)(/.*)$#', $request_uri, $subarray);
		$slug = trim($subarray[2], '/');
		$this->get_track_and_redirect($slug);
	}
	
	/**
	 * Redirect to proper url
	 *
	 * @return void
	 */
	public function get_track_and_redirect($slug) {
		$url = $this->get_redirection()->where_to_redirect($slug);
		if(isset($url) && !empty($url)){
			header('Location: ' . $url . '', true, 302);
			exit;
		}
	}

	/**
	 * Plugin activation actions.
	 *
	 * @return void
	 */
	public function klick_slt_activation_actions() {
		$this->get_options()->set_default_options();
	}

	/**
	 * Plugin deactivation actions.
	 *
	 * @return void
	 */
	public function klick_slt_deactivation_actions() {
		$this->get_options()->delete_all_options();
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'slt_details';

		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
	}

}

register_uninstall_hook(__FILE__,'klick_slt_uninstall_option');

/**
 * Delete data when uninstall
 *
 * @return void
 */
function klick_slt_uninstall_option(){
	klick_slt()->get_options()->delete_all_options();
}

/**
 * Instantiates the main plugin class
 *
 * @return instance
 */
function klick_slt(){
     return klick_slt::instance();
}

endif;

$GLOBALS['klick_slt'] = Klick_Slt();
