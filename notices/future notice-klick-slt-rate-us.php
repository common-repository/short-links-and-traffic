<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Slt_Rate_Us')) return;

require_once(KLICK_SLT_PLUGIN_MAIN_PATH . '/includes/class-klick-slt-abstract-notice.php');

/**
 * Class Klick_Slt_Rate_Us
 */
class Klick_Slt_Rate_Us extends Klick_Slt_Abstract_Notice {

	/**
	 * Klick_Slt_Rate_Us constructor
	 */
	public function __construct() {
		$this->notice_id = 'givemerate';
		$this->title = __('Please Rate Short links and traffic', 'klick-slt');
		$this->klick_slt = "";
		$this->notice_text = __('If you could spare just a few minutes it would help us alot - thanks', 'klick-slt');
		$this->image_url = '../images/our-more-plugins/slt.svg';
		$this->dismiss_time = 'dismiss-page-notice-until';
		$this->dismiss_interval = 30;
		$this->display_after_time = 0;
		$this->dismiss_type = 'dismiss forever';
		$this->dismiss_text= __('I have already rated', 'klick-slt');
		$this->position = 'top';
		$this->only_on_this_page = '';
		$this->button_link = 'https://wordpress.org/support/plugin/klick-slt/reviews/?rate=5#new-post';
		$this->button_text = __('Click Here', 'klick-slt');
		$this->notice_template_file = 'horizontal-notice.php';
		$this->validity_function_param = '';
		$this->validity_function = '';
	}
}
