<?php
if (!defined('KLICK_SLT_VERSION')) die('No direct access allowed');

/**
 * Access via klick_slt()->get_redirection().
 */
class Klick_Slt_Redirection {
	
	private $options;

	/**
	 * Constructor for Redirection class
	 *
	 */
	public function __construct() {
		$this->options = klick_slt()->get_options();
	}

	/**
	 * Get full list of links
	 * 
	 * @return array
	 */
	public function get_all(){
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "slt_details"); 
		return $results;
	}

	/**
	 * To find url of particular link name as slug
	 * 
	 * @param  string $slug
	 * @return string
	 */
	public function where_to_redirect($slug) {
		global $wpdb;
		$return_type = OBJECT;
		$query  = "SELECT * FROM ".$wpdb->prefix ."slt_details WHERE name=%s";
		$query  = $wpdb->prepare($query, $slug);
		$link   = $wpdb->get_row($query, $return_type);
		if(isset($link)) {
			return $link->url;
		}
	}

	/**
	 * Save and update row
	 * 
	 * @param  array $data
	 * @return array
	 */
	public function save($data) {
		global $wpdb;
		$is_exist = $this->is_link_exist($data['klick_slt_name']);


		if ($data['klick_slt_command'] == "add" ) {
			if ($is_exist) {
				$return_array['messages'] = $this->show_admin_warning(__("Redirection is already exist.. Try another.", "klick-slt"),'updated fade');
				$return_array['status'] = 2;
				return $return_array;
			}
			
			$wpdb->insert( 
				$wpdb->prefix . 'slt_details', 
				array( 
					'name' => $data['klick_slt_name'], 
					'description' => $data['klick_slt_desc'],
					'url' => $data['klick_slt_url'], 
				), 
				array( 
					'%s', 
					'%s',
					'%s',
				) 
			);
			$return_array['messages'] = $this->show_admin_warning(__("Redirection is saved.", "klick-slt"),'updated fade');
				if ($wpdb->insert_id != null) {
					$return_array['status'] = 1;
				} else {
					$return_array['status'] = 0;
				}

			return $return_array;
		}

		if ($data['klick_slt_command'] == "update") {
			$wpdb->update( 
				$wpdb->prefix . 'slt_details', 
				array( 
					'name' => $data['klick_slt_name'], 
					'description' => $data['klick_slt_desc'],
					'url' => $data['klick_slt_url'],  
				), 
				array('id' => $data['klick_slt_id'])
			);
			$return_array['messages'] = $this->show_admin_warning(__("Redirection is updated.", "klick-slt"),'updated fade');
			$return_array['status'] = 1;
			return $return_array;
		}
		
	}

	/**
	 * Check if any link exist
	 * 
	 * @param  string $name
	 * @return boolean
	 */
	public function is_link_exist($name) {
		global $wpdb;
		$return_type = OBJECT;
		$query = "SELECT * FROM ".$wpdb->prefix."slt_details WHERE name = %s";
		$query  = $wpdb->prepare($query, $name);
		$row   = $wpdb->get_row($query, $return_type);
		if (isset($row) && is_object($row)) {
			return true;	
		} else {
			return false;
		}
	}

	/**
	 * Delete link
	 * 
	 * @param  array $data
	 * @return boolean
	 */
	public function delete($data) {
		$id = $data['id'];
		global $wpdb;
		$return_type = OBJECT;
		$query = "DELETE FROM " . $wpdb->prefix . "slt_details WHERE id = %d";
		$query  = $wpdb->prepare($query, $id);
		$row   = $wpdb->get_row($query, $return_type);
		return true;
	}

	/**
	 * Edit link
	 * 
	 * @param  array $data
	 * @return object
	 */
	public function edit($data) {
		$id = $data['id'];
		global $wpdb;
		$return_type = OBJECT;
		$query = "SELECT * FROM " . $wpdb->prefix . "slt_details WHERE id = %d";
		$query  = $wpdb->prepare($query, $id);
		$row   = $wpdb->get_row($query, $return_type);
		return $row;
	}

	/**
	 * Render link list in row format
	 * 
	 * @param  array $data
	 * @return array
	 */
	public function link_list() {
		$data_table = $this->get_all();
		if(empty($data_table)){
			return "You don't have any redirection yet";
		}
		$table_array = json_decode(json_encode($data_table), true);
		$link_list = "";
		$link_list .= '<div class="klick-slt-title">';
		$link_list .= '<div>Name</div>';
		$link_list .= '<div>Desciption</div>';
		$link_list .= '<div>URL</div>';
		$link_list .= '</div>';
		foreach ($table_array as  $value) {
			$id = $value['id'];	
			$name = $value['name'];	
			$description = $value['description'];	
			$url = $value['url'];
		
			$link_list .= "<div  class='klick-slt-row-container'>";
				$link_list .= "<span data-label='Delete' style='width: 30px; text-align: center;' data-id='$id' class='klick-slt-delete-row'><span class='dashicons dashicons-trash'></span></span>";
				$link_list .= "<span data-label='Edit' style='width: 30px; text-align: center;' data-id='$id' class='klick-slt-edit-row'><span class='dashicons dashicons-edit'></span></span>";
				$link_list .= "<span class='details-link theme-install'>$name</span>";
				$link_list .= "<span class='details-link theme-install'>$description</span>";
				$link_list .= "<span class='details-link theme-install'>$url</span>";
			$link_list .= '</div>';
		}

		return $link_list;
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
}
