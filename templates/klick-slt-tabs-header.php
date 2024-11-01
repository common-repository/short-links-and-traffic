<?php if (!defined('KLICK_SLT_VERSION')) die('No direct access allowed'); ?>

<?php 
	// To display the notices at admin page
	klick_slt()->get_notifier()->do_notice('top');
?>

<div class="klick-logo-and-title">
		<img src='<?php echo KLICK_SLT_PLUGIN_URL ?>images/slt-banner.png' height='100px'>
</div>	

<!-- Render tabs -->
<div id="klick_slt_nav_tab_wrapper" class="nav-tab-wrapper wp-clearfix">
	<?php foreach ($tabs as $tab_id => $tab_title) { ?>
		<a id="klick_slt_nav_tab_<?php echo $tab_id; ?>" href="<?php esc_attr_e($options->admin_page_url()); ?>&amp;tab=klick_slt_<?php echo $tab_id; ?>" class="nav-tab <?php if ($active_tab == $tab_id) echo 'nav-tab-active'; ?>"><?php echo $tab_title; ?></span></a>
	<?php } ?>
</div>

<script type="text/javascript">
	var klick_slt_ajax_nonce ='<?php echo wp_create_nonce('klick_slt_ajax_nonce'); ?>';
</script>
