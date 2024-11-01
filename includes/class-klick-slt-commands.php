<?php 

if (!defined('KLICK_SLT_PLUGIN_MAIN_PATH')) die('No direct access allowed');

/**
 * Commands available from control interface (e.g. wp-admin) are here
 * All public methods should either return the data, or a WP_Error with associated error code, message and error data
 */
/**
 * Sub commands for Ajax
 *
 */
class Klick_Slt_Commands {
	private $options;
	private $links;
	
	/**
	 * Constructor for Commands class
	 *
	 */
	public function __construct() {
		$this->options = Klick_Slt()->get_options();
		$this->links = Klick_Slt()->get_redirection();
	} 

	/**
	 * dis-miss button
	 *
	 * @param  Array 	$data an array of data UI form
	 *
	 * @return Array 	$status
	 */
	public function dismiss_page_notice_until($data) {

		return array(
			'status' => $this->options->dismiss_page_notice_until($data),
			);
	}

	/**
	 * dis-miss button
	 *
	 * @param  Array 	$data an array of data UI form
	 *
	 * @return Array 	$status
	 */
	public function dismiss_page_notice_until_forever($data) {
		
		return array(
			'status' => $this->options->dismiss_page_notice_until_forever($data),
			);
	}
	
	/**
	 * This sends the passed data value over to the save function
	 *
	 * @param  Array    $data an array of data UI form
	 *
	 * @return Array    $status
	 */
	public function klick_slt_save_settings($data) {
		
		return array(
			'status' => $this->links->save($data),
		);
	}
	
	/**
	 * This sends the passed data value over to the delete function
	 *
	 * @param  Array    $data an array of data UI form
	 *
	 * @return Array    $status
	 */
	public function klick_slt_delete_row($data) {
		return array(
			'status' => $this->links->delete($data),
		);

	}

	/**
	 * This sends the passed data value over to the reload function
	 *
	 * @param  Array    $data an array of data UI form
	 *
	 * @return Array    $status
	 */
	public function klick_slt_reload($data) {
		return array(
			'data' => $this->links->link_list(),
		);
	}

	/**
	 * This sends the passed data value over to the edit function
	 *
	 * @param  Array    $data an array of data UI form
	 *
	 * @return Array    $data
	 */
	public function klick_slt_edit_row($data) {
		return array(
			'data' => $this->links->edit($data),
		);
	}
}
