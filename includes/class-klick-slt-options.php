<?php
if (!defined('KLICK_SLT_VERSION')) die('No direct access allowed');
/**
 * Access via klick_slt()->get_options().
 */
class Klick_Slt_Options {
	
	/**
	 * Get option from table
	 *
	 * @return string
	 */
	public function get_option($option, $setting = false) {
		return get_option('klick-slt-' . $option, $setting);
	}
	
	/**
	 * Update option in table
	 *
	 * @param  String 	$option
	 * @param  String 	$asetting
	 *
	 * @returns boolean
	 */
	public function update_option($option, $setting = false) {
		return update_option('klick-slt-' . $option, $setting);
	}
	
	/**
	 * Delete option from options table
	 *
	 * @param  string
	 * @return void
	 */
	public function delete_option($option) {
		delete_option('klick-slt-' . $option);
	}
	
	/**
	 * Get option names
	 *
	 * @return array of options names
	 */
	public function get_option_keys() {

		return apply_filters(
			'klick_slt_option_keys',
			array('logging','notice-display-time','plugin-name')
		);
	}
	
	/**
	 * Delete all options
	 *
	 * @return void
	 */
	public function delete_all_options() {
		$option_keys = $this->get_option_keys();
		foreach ($option_keys as $key) {
			$this->delete_option($key);
		}
	}

	/**
	 * Setup options if not exists already.
	 *
	 * @return void
	 */
	public function set_default_options() {
		$this->update_option('logging', true);
		$this->update_option('notice-display-time', false);

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		add_option('slt_details_db_version', '0.1');
		global $wpdb;

		$sql = '
		  CREATE TABLE '.$wpdb->prefix.'slt_details (
		    id int(11) NOT NULL auto_increment,
		    name varchar(255) NOT NULL,
		    description text,
		    url varchar(255) default NULL,
		    PRIMARY KEY  (id)
		  )';
		dbDelta($sql);
	}
	
	/**
	 * Update option with time + interval
	 *
	 * @param  String 	$notice_id
	 * 
	 * @return void
	 */
	public function dismiss_page_notice_until($notice_id) {

		$notices = klick_slt()->get_notifier()->get_notices();

		foreach ($notices as $notice) {

		    if ($notice->notice_id == $notice_id) {

		    	if (0 == $notice->dismiss_interval) return;

		    	 $display_notice_time = $this->get_option('notice-display-time');

		    	 $display_notice_time[$notice_id] = time() + $notice->dismiss_interval;
		    	 
		    	 $this->update_option('notice-display-time',$display_notice_time);
		    }
		}
	}

	/**
	 * Update option 0 to dismiss forever
	 *
	 * @param  String 	$notice_id
	 * 
	 * @return void
	 */
	public function dismiss_page_notice_until_forever($notice_id) {

		$notices = klick_slt()->get_notifier()->get_notices();

		foreach ($notices as $notice) {

		    if ($notice->notice_id == $notice_id) {

		    	if (0 == $notice->dismiss_interval) return;

		    	 $display_notice_time = $this->get_option('notice-display-time');

		    	 $display_notice_time[$notice_id] = 0;
		    	 
		    	 $this->update_option('notice-display-time',$display_notice_time);
		    }
		}
	}

	/**
	 * Create ajax notice
	 * @param  String 	$message as a notice
	 * @param  String 	$class an string if many then separated by space defalt is 'updated'
	 *
	 * @return String 	returns message
	 */
	public function show_admin_warning($message, $class = "updated") {
		return  '<div class="klick-ajax-notice ' . $class . '">' . "<p>$message</p></div>";
	}
	
	/**
	 * Returns the admin page url
	 *
	 * @return string
	 */
	public function admin_page_url() {
		return admin_url('admin.php?page=klick-slt');
	}
}
