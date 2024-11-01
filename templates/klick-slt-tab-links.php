<!-- First Tab content -->
<div id="klick_slt_tab_first">
	<div class="klick-notice-message"></div>
	<div class="klick-ajax-message"></div>

	<div class="klick-slt-data-box">
		<div class="klick-slt-overlay">
			<img class="loading-image" src="<?php echo KLICK_SLT_PLUGIN_URL . 'images/ajax-loader.gif' ?> " alt="Loading.." />'
		</div>
		<div class="slt-list-container">
			<article class="klick-slt-data-table">
				<h1>Short Links and Traffic</h1> <!-- Header tab-->
				<div id="klick_slt_link_list" class="klick-slt-add-btn">
					<div class="klick-slt-info">
					 	<button id = "klick_slt_link_add" class = "klick_btn button button-primary">Add New Redirection</button> 
					</div>
				</div>
				<div class="slt-list-table">
			 		<?php echo klick_slt()->get_redirection()->link_list(); ?>
				</div>
			</article>
		</div>
		 
		<div id="klick_slt_add_link">
			<form class="klick-slt-form-wrapper">
				<ul>
					<li>
						<label>Name :</label>
						<div class="slt-list-label-content">
							<input type="text" name="klick_slt_name" id="klick_slt_name" placeholder="e.g. abc">
						</div>
					</li>
					<li>
						<label>Description :</label>
						<div class="slt-list-label-content">
							<input type="text" name="klick_slt_desc" id="klick_slt_desc" placeholder="Any Desc.....">
						</div>
					</li>
					<li>
						<label>URL :</label>
						<div class="slt-list-label-content">
							<input type="text" name="klick_slt_url" id="klick_slt_url" placeholder="e.g. http://www.abc.com">
						</div>
					</li>
					<li>
						<div class="slt-list-label-command">
							<input name="klick_slt_command" id="klick_slt_command" value="add" type="hidden">
							<input name="klick_slt_id" id="klick_slt_id" value="" type="hidden">
						</div>
					</li>
				</ul>
			</form>
			<div class="slt-list-label-content">
				<button id = "klick_slt_link_save" class = "klick_btn button button-primary" >Add</button>
				<button id = "klick_slt_link_cancel" class = "klick_btn button button-primary" >Cancel</button>
			</div>
		</div>
	</div>	
</div>

<script type="text/javascript">
	var klick_slt_ajax_nonce ='<?php echo wp_create_nonce('klick_slt_ajax_nonce'); ?>';
</script>
