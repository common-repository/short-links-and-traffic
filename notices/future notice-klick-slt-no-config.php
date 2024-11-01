<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Slt_No_Config')) return;

require_once(KLICK_SLT_PLUGIN_MAIN_PATH . '/includes/class-klick-slt-abstract-notice.php');

/**
 * Class Klick_Slt_No_Config
 */
class Klick_Slt_No_Config extends Klick_Slt_Abstract_Notice {
	
	/**
	 * Klick_Slt_No_Config constructor
	 */
	public function __construct() {
		$this->notice_id = 'short-link-and-traffic';
		$this->title = __('Short links and traffic plugin is installed but not configured', 'klick-slt');
		$this->klick_slt = "";
		$this->notice_text = __('Configure it Now', 'klick-slt');
		$this->image_url = '../images/our-more-plugins/slt.svg';
		$this->dismiss_time = 'dismiss-page-notice-until';
		$this->dismiss_interval = 30;
		$this->display_after_time = 0;
		$this->dismiss_type = 'dismiss';
		$this->dismiss_text = __('Hide Me!', 'klick-slt');
		$this->position = 'dashboard';
		$this->only_on_this_page = 'index.php';
		$this->button_link = KLICK_SLT_PLUGIN_SETTING_PAGE;
		$this->button_text = __('Click here', 'klick-slt');
		$this->notice_template_file = 'main-dashboard-notices.php';
		$this->validity_function_param = 'short-links-and-traffic/short-links-and-traffic.php';
		$this->validity_function = 'is_plugin_configured';
	}
}
