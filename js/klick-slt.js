/**
 * Send an action via admin-ajax.php
 * 
 * @param {string} action - the action to send
 * @param * data - data to send
 * @param Callback [callback] - will be called with the results
 * @param {boolean} [json_parse=true] - JSON parse the results
 **/

var klick_slt_send_command = function (action, data, callback, json_parse) {
	json_parse = ('undefined' === typeof json_parse) ? true : json_parse;
	var ajax_data = {
		action: 'klick_slt_ajax',
		subaction: action,
		nonce: klick_slt_ajax_nonce,
		data: data
	};
	jQuery.post(ajaxurl, ajax_data, function (response) {
		
		if (json_parse) {
			try {
				var resp = JSON.parse(response);
			} catch (e) {
				console.log(e);
				console.log(response);
				return;
			}
		} else {
			var resp = response;
		}
		
		if ('undefined' !== typeof callback) callback(resp);
	});
}

/**
 * When DOM ready
 * 
 */
jQuery(document).ready(function ($) {
	klick_slt = klick_slt(klick_slt_send_command);
});

/**
 * Function for sending communications
 * 
 * @callable klick_slt_send_command Callable
 * @param {string} action - the action to send
 * @param * data - data to send
 * @param Callback [callback] - will be called with the results
 * @param {boolean} [json_parse=true] - JSON parse the results
 */
/**
 * Main klick_slt
 * 
 * @param {sendcommandCallable} send_command
 */
var klick_slt = function (klick_slt_send_command) {
	var $ = jQuery;
	$(".klick-slt-overlay").hide();
	$('#klick_slt_add_link').hide();
	
	/**
	 * Proceses the tab click handler
	 *
	 * @return void
	 */
	$('#klick_slt_nav_tab_wrapper .nav-tab').click(function (e) {
		e.preventDefault();
		
		var clicked_tab_id = $(this).attr('id');
	
		if (!clicked_tab_id) { return; }
		if ('klick_slt_nav_tab_' != clicked_tab_id.substring(0, 18)) { return; }
		
		var clicked_tab_id = clicked_tab_id.substring(18);

		$('#klick_slt_nav_tab_wrapper .nav-tab:not(#klick_slt_nav_tab_' + clicked_tab_id + ')').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');

		$('.klick-slt-nav-tab-contents:not(#klick_slt_nav_tab_contents_' + clicked_tab_id + ')').hide();
		$('#klick_slt_nav_tab_contents_' + clicked_tab_id).show();
	});
	
	/**
	 * Add link
	 *
	 * @return void
	 */
	$('#klick_slt_link_add').click(function (e) {
		e.preventDefault();
		show_slt_form();
		slt_add_form_reset();
	});
	
	/**
	 * Cancel link
	 *
	 * @return void
	 */
	$('#klick_slt_link_cancel').click(function (e) {
		e.preventDefault();
		$(".klick-notice-message").html("");
		$(".klick-notice-message").css('display','none');
		$(".klick-ajax-notice").html("");
		$(".klick-ajax-notice").css('display','none');
		show_slt_list();
	});
	
	/**
	 * Gathers the details from form
	 *
	 * @returns (string) - serialized row data
	 */
	function gather_row(command){
		if(is_name_valid() === false) {
			return false;	
		} else if (is_desc_valid() === false) {
			return false;
		} else if(is_link_url_valid() === false) {
			return false;
		} else {
			var form_data = $(".klick-slt-form-wrapper").serialize();
			return form_data;	
		}	
	}
	
	/**
	 * Save link details
	 *
	 * @return void
	 */
	$('#klick_slt_link_save').on('click',function (e) {
		e.preventDefault();
		var command = $("#klick_slt_command").val();
		var data = gather_row(command);
		if(data === false) return false;
		klick_slt_send_command('klick_slt_save_settings', data, function (resp) {
				if(resp.status['status'] == 1) { // Save
					slt_list_reload();
					show_slt_list();
					$('.klick-ajax-message').html(resp.status['messages']);
					$('.klick-ajax-message').slideDown();
					$('.fade').delay(2000).slideUp(200, function(){
					});
				}
				if(resp.status['status'] == 2) { // Alreday name exist
					$('.klick-ajax-message').html(resp.status['messages']);
					$('.klick-ajax-message').slideDown();
					$('.fade').delay(10000).slideUp(200, function(){
					});
				}
			});
	});

	/**
	 * Delete link
	 *
	 * @return void
	 */
	$(document).on('click', '.klick-slt-delete-row', function(e) {	
		e.preventDefault();
		$(".klick-slt-overlay").show();
		var id = $(this).attr('data-id');
		var data = 'id='+id;
		klick_slt_send_command('klick_slt_delete_row', data, function (resp) {
			$(".klick-slt-overlay").hide();
			if(resp.status === true) {

				slt_list_reload();
			}
		});
	});

	/**
	 * Edit link
	 *
	 * @return void
	 */
	$(document).on('click', '.klick-slt-edit-row', function(e) {	
		e.preventDefault();
		$(".klick-slt-overlay").show();
		var id = $(this).attr('data-id');
		var data = 'id='+id;
		klick_slt_send_command('klick_slt_edit_row', data, function (resp) {
			$(".klick-slt-overlay").hide();
			show_slt_form();
			slt_add_form_reset();
			$("#klick_slt_name").val(resp.data['name']);
			$("#klick_slt_desc").val(resp.data['description']);
			$("#klick_slt_url").val(resp.data['url']);
			$("#klick_slt_command").val("update");
			$("#klick_slt_id").val(resp.data['id']);
			$("#klick_slt_link_save").text("update");
		});
	});

	/**
	 * To reload link list
	 *
	 * @return void
	 */
	function slt_list_reload(){
		$(".klick-slt-overlay").show();
		var data = "reload=1";
		klick_slt_send_command('klick_slt_reload', data, function (resp) {
			$(".klick-slt-overlay").hide();
			$(".slt-list-table").html(resp.data);
		});
	}

	/**
	 * To reset add link form
	 *
	 * @return void
	 */
	function slt_add_form_reset(){
		$("#klick_slt_command").val("add");
		$('#klick_slt_name').val("");
		$('#klick_slt_desc').val("");
		$('#klick_slt_url').val("");
	}

	/**
	 * show link list
	 *
	 * @return void
	 */
	function show_slt_list(){
		$('#klick_slt_add_link').hide();
		$(".slt-list-container").show();
		$('#klick_slt_link_list').show();
	}

	/**
	 * Show add link form
	 *
	 * @return void
	 */
	function show_slt_form(){
		$('#klick_slt_link_list').hide();
		$(".slt-list-container").hide();
		$('#klick_slt_add_link').show();
	}

	/**
	 * Key up handler for name
	 *
	 * @return void
	 */
	$("#klick_slt_name").keyup(function(){
		$("#klick_slt_link_save").attr('disabled',false);
	});	

	/**
	 * Key up handler for desc
	 *
	 * @return void
	 */
	$("#klick_slt_desc").keyup(function(){
		$("#klick_slt_link_save").attr('disabled',false);
	});	

	/**
	 * Key up handler for url
	 *
	 * @return void
	 */
	$("#klick_slt_url").keyup(function(){
		$("#klick_slt_link_save").attr('disabled',false);
	});	

	/**
	 * Test expression if any non numeric or alpha is entered with space
	 *
	 * @return boolean
	 */
	function check_for_alphanumeric( str ) {
	 	return /^[A-Za-z0-9 _.-]+$/.test(str);
	}

	/**
	 * Check whether passed param is empty or not
	 *
	 * @return boolean
	 */
	function is_empty(name){
		var result = (name.length == "") ? true : false;
		return result;
	}

	/**
	 * Check valid url
	 *
	 * @return boolean
	 */
	function valid_URL(url) {
		return url.substring(0, 4) == 'http';
	}
	/**
	 * Create and render notice admin side
	 *
	 * @string string selecoter, e.g. #msg_area
	 * @msg string msg
	 * @return void
	 */
	function set_notice_message_generate(selector, msg){
		$(""+selector+"").addClass('klick-notice-message notice notice-error is-dismissible');
		$(""+selector+"").html("<p>" + msg + "</p>");
		$(""+selector+"").slideDown();
		$("#klick_slt_link_save").attr('disabled','disabled');
		return false;
	}

	/**
	 * Check valid link name by all defined rules
	 *
	 * @return boolean
	 */
	function is_name_valid(){
		var klick_slt_name = $.trim($("#klick_slt_name").val());
		if(is_empty(klick_slt_name) === true) {
			set_notice_message_generate('.klick-notice-message',klick_slt_admin.empty_status_name);
			return false;
		} else if(check_for_alphanumeric(klick_slt_name) === false) {
			set_notice_message_generate('.klick-notice-message', klick_slt_admin.invalid_name);
			return false;
		} else {
			$('.klick-notice-message').slideUp();
			return true;
		}
	}	


	/**
	 * Check valid link description by all defined rules
	 *
	 * @return boolean
	 */
	function is_desc_valid(){
		var klick_slt_desc = $.trim($("#klick_slt_desc").val());
		if(is_empty(klick_slt_desc) === true) {
			set_notice_message_generate('.klick-notice-message',klick_slt_admin.empty_status_desc);
			return false;
		} else if(check_for_alphanumeric(klick_slt_desc) === false) {
			set_notice_message_generate('.klick-notice-message',klick_slt_admin.invalid_desc);
			return false;
		} else {
			$('.klick-notice-message').slideUp();
			return true;
		}
	}

	/**
	 * Check valid link url by all defined rules
	 *
	 * @return boolean
	 */
	function is_link_url_valid(){
		var klick_slt_url = $.trim($("#klick_slt_url").val());
		if(is_empty(klick_slt_url) === true) {
			set_notice_message_generate('.klick-notice-message',klick_slt_admin.empty_status_url);
			return false;
		} else if(valid_URL(klick_slt_url) === false) {
			set_notice_message_generate('.klick-notice-message',klick_slt_admin.invalid_url);
			return false;
		} else {
			$('.klick-notice-message').slideUp();
			return true;
		}
	}
}
