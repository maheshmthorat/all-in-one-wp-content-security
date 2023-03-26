<?php
/**
 * Class All in one WP Content Protector
 * The file that defines the core plugin class
 *
 * @author Mahesh Thorat
 * @link https://maheshthorat.web.app
 * @version 0.1
 * @package All_in_one_WP_Content_Security
*/
class All_In_One_WP_Content_Security_Admin
{
	private $plugin_name;
	private $version;

	/**
	 * Return the tabs menu
	*/
	public function return_tabs_menu($tab)
	{
		$link = admin_url('options-general.php');
		$list = array
		(
			array('tab1', 'all-in-one-wp-content-security-admin', 'fa-cogs', __('<span class="dashicons dashicons-admin-tools"></span> Settings', AOWPCS_PLUGIN_IDENTIFIER)),
			array('tab2', 'all-in-one-wp-content-security-admin&con=about', 'fa-info-circle', __('<span class="dashicons dashicons-editor-help"></span> About', AOWPCS_PLUGIN_IDENTIFIER)),
			array('tab3', 'all-in-one-wp-content-security-admin&con=donate', 'fa-info-circle', __('<span class="dashicons dashicons-money-alt"></span> Donate', AOWPCS_PLUGIN_IDENTIFIER))
		);

		$menu = null;
		foreach($list as $item => $value)
		{
			$menu.='<div class="tab-label '.$value[0].' '.(($tab == $value[0]) ? 'active' : '').'"><a href="'.$link.'?page='.$value[1].'"><span>'.$value[3].'</span></a></div>';
		}

		echo wp_kses_post($menu);
	}

	/**
	 * Register the stylesheet file(s) for the dashboard area
	*/
	public function enqueue_backend_standalone()
	{
		wp_register_style($this->plugin_name.'-standalone', plugin_dir_url(__FILE__).'assets/styles/standalone.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name.'-standalone');
	}

	/**
	 * Update `Options` on form submit
	*/
	public function return_update_options()
	{
		if((isset($_POST['all-in-one-wp-content-security-update-option'])) && ($_POST['all-in-one-wp-content-security-update-option'] == 'true')
			&& check_admin_referer('pwm-referer-form', 'pwm-referer-option'))
		{
			$opts = array('block_selection' => 'off', 'block_image_dragging' => 'off', 'loadtime' => 'off', 'block_hacking_website' => 'off');

			if(isset($_POST['_all_in_one_wp_content_security']['block_selection']))
			{
				$opts['block_selection'] = 'on';
			}
			if(isset($_POST['_all_in_one_wp_content_security']['block_image_dragging']))
			{
				$opts['block_image_dragging'] = 'on';
			}
			if(isset($_POST['_all_in_one_wp_content_security']['block_right_clicking']))
			{
				$opts['block_right_clicking'] = 'on';
			}
			if(isset($_POST['_all_in_one_wp_content_security']['block_hacking_website']))
			{
				$opts['block_hacking_website'] = 'on';
			}
			
			$data = update_option('_all_in_one_wp_content_security', $opts);
			header('location:'.admin_url('options-general.php?page=all-in-one-wp-content-security-admin').'&status=updated');
			die();
		}
	}

	/**
	 * Return the `Options` page
	*/
	public function return_options_page()
	{
		$opts = get_option('_all_in_one_wp_content_security');

		if((isset($_GET['status'])) && ($_GET['status'] == 'updated'))
		{
			$notice = array('success', __('Your settings have been successfully updated.', AOWPCS_PLUGIN_IDENTIFIER));
		}
		if(@$_GET['con'] == 'about')
		{
			$this->return_about_page();
		}
		else if(@$_GET['con'] == 'donate')
		{
			$this->return_donate_page();
		}
		else
		{
			?>
			<div class="wrap">
				<section class="wpbnd-wrapper">
					<div class="wpbnd-container">
						<div class="wpbnd-tabs">
							<?php echo wp_kses_post($this->return_plugin_header()); ?>
							<main class="tabs-main">
								<?php echo wp_kses_post($this->return_tabs_menu('tab1')); ?>
								<section class="tab-section">
									<?php if(isset($notice)) { ?>
										<div class="wpbnd-notice <?php echo esc_attr($notice[0]); ?>">
											<span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
											<span><?php echo esc_attr($notice[1], AOWPCS_PLUGIN_IDENTIFIER); ?></span>
										</div>
									<?php } elseif((!isset($opts['block_selection']) || ($opts['block_selection']) == 'off')) { ?>
										<div class="wpbnd-notice warning">
											<span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
											<span><?php echo _e('You have not set up your WP Content Security options ! In order to do so, please use the below form.', AOWPCS_PLUGIN_IDENTIFIER); ?></span>
										</div>
									<?php } else { ?>
										<div class="wpbnd-notice info">
											<span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
											<span><?php echo _e('Your plugin is properly configured! You can change your WP Content Security options using the below settings.', AOWPCS_PLUGIN_IDENTIFIER); ?></span>
										</div>
									<?php } ?>
									<form method="POST">
										<input type="hidden" name="all-in-one-wp-content-security-update-option" value="true" />
										<?php wp_nonce_field('pwm-referer-form', 'pwm-referer-option'); ?>
										<div class="wpbnd-form">
											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<label class="label"><span class="dashicons dashicons-clipboard"></span> <?php echo _e('Block Selection', AOWPCS_PLUGIN_IDENTIFIER); ?></label>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_selection]" class="onoffswitch-checkbox" <?php if((isset($opts['block_selection'])) && ($opts['block_selection'] == 'on')) { echo 'checked="checked"'; } ?>/>
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo _e('All text related content will be restricted from selection.', AOWPCS_PLUGIN_IDENTIFIER); ?></small>
												</div>
											</div>

											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<span class="label"><span class="dashicons dashicons-format-image"></span> <?php echo _e('Block Image Dragging', AOWPCS_PLUGIN_IDENTIFIER); ?></span>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_image_dragging]" class="onoffswitch-checkbox" <?php if((isset($opts['block_image_dragging'])) && ($opts['block_image_dragging'] == 'on')) { echo 'checked="checked"'; } ?>/>
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo _e('It will prevent users to stop image Drag and Drop outside website?', AOWPCS_PLUGIN_IDENTIFIER); ?></small>
												</div>
											</div>

											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<span class="label"><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php echo _e('Block Right Clicking', AOWPCS_PLUGIN_IDENTIFIER); ?></span>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_right_clicking]" class="onoffswitch-checkbox" <?php if((isset($opts['block_right_clicking'])) && ($opts['block_right_clicking'] == 'on')) { echo 'checked="checked"'; } ?>/>
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo _e('It will prevent users for being clicked using right or restricted for context menu', AOWPCS_PLUGIN_IDENTIFIER); ?></small>
												</div>
											</div>

											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<span class="label"><span class="dashicons dashicons-lock"></span> <?php echo _e('Block Console / Inspect Element', AOWPCS_PLUGIN_IDENTIFIER); ?></span>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_hacking_website]" class="onoffswitch-checkbox" <?php if((isset($opts['block_hacking_website'])) && ($opts['block_hacking_website'] == 'on')) { echo 'checked="checked"'; } ?>/>
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo _e('** Prevents users from hacking website it blocks Console / Inspect Element', AOWPCS_PLUGIN_IDENTIFIER); ?></small>
												</div>
											</div>

											<div class="form-footer">
												<input type="submit" class="button button-primary button-theme" value="<?php _e('Update Settings', AOWPCS_PLUGIN_IDENTIFIER); ?>">
											</div>
										</div>
									</form>
								</section>
							</main>
						</div>
					</div>
				</section>
			</div>
			<?php
		}
	}

	/**
	 * Return the plugin header
	*/
	public function return_plugin_header()
	{
		$html = '<div class="header-plugin"><span class="header-icon"><span class="dashicons dashicons-admin-settings"></span></span> <span class="header-text">'.__(AOWPCS_PLUGIN_FULLNAME, AOWPCS_PLUGIN_IDENTIFIER).'</span></div>';
		return $html;
	}

	/**
	 * Return the `About` page
	*/
	public function return_about_page()
	{
		?>
		<div class="wrap">
			<section class="wpbnd-wrapper">
				<div class="wpbnd-container">
					<div class="wpbnd-tabs">
						<?php echo wp_kses_post($this->return_plugin_header()); ?>
						<main class="tabs-main about">
							<?php echo wp_kses_post($this->return_tabs_menu('tab2')); ?>
							<section class="tab-section">
								<img alt="Mahesh Thorat" src="https://secure.gravatar.com/avatar/13ac2a68e7fba0cc0751857eaac3e0bf?s=100&amp;d=mm&amp;r=g" srcset="https://secure.gravatar.com/avatar/13ac2a68e7fba0cc0751857eaac3e0bf?s=200&amp;d=mm&amp;r=g 2x" class="avatar avatar-100 photo profile-image" height="100" width="100" >

								<div class="profile-by">
									<p>© <?php echo date('Y'); ?> - created by <a class="link" href="https://maheshthorat.web.app/" target="_blank"><b>Mahesh Mohan Thorat</b></a></p>
								</div>
							</section>
							<section class="helpful-links">
								<b>helpful links</b>
								<ul>
									<li><a href="https://pagespeed.web.dev/" target="_blank">PageSpeed</a></li>
									<li><a href="https://gtmetrix.com/" target="_blank">GTmetrix</a></li>
									<li><a href="https://www.webpagetest.org" target="_blank">Web Page Test</a></li>
									<li><a href="https://http3check.net/" target="_blank">http3check</a></li>
									<li><a href="https://sitecheck.sucuri.net/" target="_blank">Sucuri - security check</a></li>
								</ul>
							</section>
						</main>
					</div>
				</div>
			</section>
		</div>
		<?php
	}

	public function return_donate_page()
	{
		?>
		<div class="wrap">
			<section class="wpbnd-wrapper">
				<div class="wpbnd-container">
					<div class="wpbnd-tabs">
						<?php echo wp_kses_post($this->return_plugin_header()); ?>
						<main class="tabs-main about">
							<?php echo wp_kses_post($this->return_tabs_menu('tab3')); ?>
							<section class="">
								<table class="wp-list-table widefat fixed striped table-view-list">
									<tbody id="the-list">
										<tr>
											<td><a href="https://rzp.io/l/maheshmthorat" target="_blank"><img width="160" src="<?php echo esc_url(plugin_dir_url(dirname( __FILE__ ))); ?>admin/assets/img/razorpay.svg" /></a></td>
										</tr>
										<tr>
											<td>
												<h3>Scan below code</h3>
												<img width="350" src="<?php echo esc_url(plugin_dir_url(dirname( __FILE__ ))); ?>admin/assets/img/qr.svg" />
												<br>
												<img width="350" src="<?php echo esc_url(plugin_dir_url(dirname( __FILE__ ))); ?>admin/assets/img/upi.png" />
												<br>
												<b>Mr Mahesh Mohan Thorat</b>
												<h3>UPI - maheshmthorat@oksbi</h3>
											</td>
										</tr>
									</tbody>
								</table>
							</section>
							<section class="helpful-links">
								<b>helpful links</b>
								<ul>
									<li><a href="https://pagespeed.web.dev/" target="_blank">PageSpeed</a></li>
									<li><a href="https://gtmetrix.com/" target="_blank">GTmetrix</a></li>
									<li><a href="https://www.webpagetest.org" target="_blank">Web Page Test</a></li>
									<li><a href="https://http3check.net/" target="_blank">http3check</a></li>
									<li><a href="https://sitecheck.sucuri.net/" target="_blank">Sucuri - security check</a></li>
								</ul>
							</section>
						</main>
					</div>
				</div>
			</section>
		</div>
	<?php	}

	/**
	 * Return Backend Menu
	*/
	public function return_admin_menu()
	{
		add_options_page(AOWPCS_PLUGIN_FULLNAME, AOWPCS_PLUGIN_FULLNAME, 'manage_options', 'all-in-one-wp-content-security-admin', array($this, 'return_options_page'));
	}

	public function call_action_block_selection()
	{
		?>
		<style type="text/css">*{-moz-user-select: none; -webkit-user-select: none; -ms-user-select:none; user-select:none;-o-user-select:none;}</style>
		<?php
	}

	public function call_action_block_image_dragging()
	{
		?>
		<style type="text/css">img { pointer-events: none }</style>
		<?php
	}

	public function call_action_block_right_clicking()
	{
		?>
		<script type="text/javascript">
			window.oncontextmenu = function () {
				return false;
			};
			document.addEventListener('contextmenu', function (e) {
				e.preventDefault();
			});
		</script>
		<?php
	}

	public function call_action_block_hacking_website()
	{
		global $wp;
		$currentPageURL = home_url( $wp->request );
		$printHTML = '<h1>We strongly not supporting to Inspect Element in Browser.<br/>Please <a href="'.esc_url($currentPageURL).'">reload page</a>';
		?>
		<style type="text/css">
			.console-open{
				background: #fafafa;
			}
			.console-open h1{
				text-align: center;
			}
			.console-open a{
				cursor: pointer;
			}
		</style>
		<script type="text/javascript">
			"use strict";
			!function() {
				function detectDevTool(allow) {
					if(isNaN(+allow)) allow = 100;
					var start = +new Date();
					debugger;
					var end = +new Date();
					if(isNaN(start) || isNaN(end) || end - start > allow) {
						consoleCheck();
					}
				}
				if(window.attachEvent)
				{
					if (document.readyState === "complete" || document.readyState === "interactive")
					{
						detectDevTool();
						window.attachEvent('onresize', detectDevTool);
						window.attachEvent('onmousemove', detectDevTool);
						window.attachEvent('onfocus', detectDevTool);
						window.attachEvent('onblur', detectDevTool);
					}
					else
					{
						setTimeout(argument.callee, 0);
					}
				}
				else
				{
					window.addEventListener('load', detectDevTool);
					window.addEventListener('resize', detectDevTool);
					window.addEventListener('mousemove', detectDevTool);
					window.addEventListener('focus', detectDevTool);
					window.addEventListener('blur', detectDevTool);
				}
			}();

			function consoleCheck()
			{
				jQuery('body').addClass('console-open');
				jQuery('body').html('<?php echo wp_kses_post($printHTML); ?>');
			}

			document.onkeydown = function (e) {
				if (event.keyCode == 123) {
					return false;
				}
				if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
					return false;
				}
				if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
					return false;
				}
				if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
					return false;
				}
				if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
					return false;
				}
			}
		</script>
		<?php
	}

	public function aowpcs_settings_link($links)
	{
		$url = get_admin_url().'options-general.php?page=all-in-one-wp-content-security-admin';
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
		array_unshift(
			$links,
			$settings_link
		);
		return $links;
	}
}

?>