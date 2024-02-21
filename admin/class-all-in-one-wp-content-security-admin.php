<?php

/**
 * Class All in one WP Content Protector
 * The file that defines the core plugin class
 *
 * @author Mahesh Thorat
 * @link https://maheshthorat.web.app
 * @version: 1.1
 * @package All_in_one_WP_Content_Security
 */
class All_In_One_WP_Content_Security_Admin
{
	private $plugin_name = AOWPCS_PLUGIN_VERSION;
	private $version = AOWPCS_PLUGIN_VERSION;
	private $notice = "";

	/**
	 * Return the tabs menu
	 */
	public function return_tabs_menu($tab)
	{
		$link = admin_url('options-general.php');
		$list = array(
			array('tab1', 'all-in-one-wp-content-security-admin', 'fa-cogs', __('<span class="dashicons dashicons-admin-tools"></span> Settings', 'all-in-one-wp-content-security')),
			array('tab2', 'all-in-one-wp-content-security-admin&con=about', 'fa-info-circle', __('<span class="dashicons dashicons-editor-help"></span> About', 'all-in-one-wp-content-security')),
			array('tab3', 'all-in-one-wp-content-security-admin&con=donate', 'fa-info-circle', __('<span class="dashicons dashicons-money-alt"></span> Say Thanks', 'all-in-one-wp-content-security'))
		);

		$menu = null;
		foreach ($list as $item => $value) {
			$menu .= '<div class="tab-label ' . $value[0] . ' ' . (($tab == $value[0]) ? 'active' : '') . '"><a href="' . $link . '?page=' . $value[1] . '"><span>' . $value[3] . '</span></a></div>';
		}

		echo wp_kses_post($menu);
	}

	/**
	 * Register the stylesheet file(s) for the dashboard area
	 */
	public function enqueue_backend_standalone()
	{
		wp_register_style($this->plugin_name . '-standalone', plugin_dir_url(__FILE__) . 'assets/styles/standalone.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-standalone');
	}

	/**
	 * Update `Options` on form submit
	 */
	public function return_update_options()
	{
		if ((isset($_POST['all-in-one-wp-content-security-update-option'])) && ($_POST['all-in-one-wp-content-security-update-option'] == 'true')
			&& check_admin_referer('pwm-referer-form', 'pwm-referer-option')
		) {
			$opts = array('block_selection' => 'off', 'block_image_dragging' => 'off', 'loadtime' => 'off', 'block_hacking_website' => 'off');

			if (isset($_POST['_all_in_one_wp_content_security']['block_selection'])) {
				$opts['block_selection'] = 'on';
			}
			if (isset($_POST['_all_in_one_wp_content_security']['block_image_dragging'])) {
				$opts['block_image_dragging'] = 'on';
			}
			if (isset($_POST['_all_in_one_wp_content_security']['block_right_clicking'])) {
				$opts['block_right_clicking'] = 'on';
			}
			if (isset($_POST['_all_in_one_wp_content_security']['block_hacking_website'])) {
				$opts['block_hacking_website'] = 'on';
			}

			update_option('_all_in_one_wp_content_security', $opts);
			$this->notice = array('success', __('Your settings have been successfully updated.', 'all-in-one-wp-content-security'));

			// header('location:' . admin_url('options-general.php?page=all-in-one-wp-content-security-admin') . '&status=updated');
			// die();
		}
	}

	/**
	 * Return the `Options` page
	 */
	public function return_options_page()
	{
		$opts = get_option('_all_in_one_wp_content_security');

		// if ((isset($_GET['status'])) && ($_GET['status'] == 'updated')) {
		// 	$notice = array('success', __('Your settings have been successfully updated.', AOWPCS_PLUGIN_IDENTIFIER));
		// }
		$nonce = wp_create_nonce('all-in-one-wp-content-security');

		if (isset($_GET['con']) && $_GET['con'] == 'about' && wp_verify_nonce($nonce, 'all-in-one-wp-content-security')) {
			$this->return_about_page();
		} else if (isset($_GET['con']) && $_GET['con'] == 'donate' && wp_verify_nonce($nonce, 'all-in-one-wp-content-security')) {
			$this->return_donate_page();
		} else {
?>
			<div class="wrap">
				<section class="wpbnd-wrapper">
					<div class="wpbnd-container">
						<div class="wpbnd-tabs">
							<?php echo wp_kses_post($this->return_plugin_header()); ?>
							<main class="tabs-main">
								<?php echo wp_kses_post($this->return_tabs_menu('tab1')); ?>
								<section class="tab-section">
									<?php if (isset($this->notice) && !empty($this->notice)) { ?>
										<div class="wpbnd-notice <?php echo esc_attr($this->notice[0]); ?>">
											<span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
											<span><?php echo esc_attr($this->notice[1], AOWPCS_PLUGIN_IDENTIFIER); ?></span>
										</div>
									<?php } elseif ((!isset($opts['block_selection']) || ($opts['block_selection']) == 'off')) { ?>
										<div class="wpbnd-notice warning">
											<span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
											<span><?php echo esc_html(__('You have not set up your WP Content Security options ! In order to do so, please use the below form.', 'all-in-one-wp-content-security')); ?></span>
										</div>
									<?php } else { ?>
										<div class="wpbnd-notice info">
											<span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
											<span><?php echo esc_html(__('Your plugin is properly configured! You can change your WP Content Security options using the below settings.', 'all-in-one-wp-content-security')); ?></span>
										</div>
									<?php } ?>
									<form method="POST">
										<input type="hidden" name="all-in-one-wp-content-security-update-option" value="true" />
										<?php wp_nonce_field('pwm-referer-form', 'pwm-referer-option'); ?>
										<div class="wpbnd-form">
											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<label class="label"><span class="dashicons dashicons-clipboard"></span> <?php echo esc_html(__('Block Selection', 'all-in-one-wp-content-security')); ?></label>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_selection]" class="onoffswitch-checkbox" <?php if ((isset($opts['block_selection'])) && ($opts['block_selection'] == 'on')) {
																																																															echo 'checked="checked"';
																																																														} ?> />
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo esc_html(__('All text related content will be restricted from selection.', 'all-in-one-wp-content-security')); ?></small>
												</div>
											</div>

											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<span class="label"><span class="dashicons dashicons-format-image"></span> <?php echo esc_html(__('Block Image Dragging', 'all-in-one-wp-content-security')); ?></span>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_image_dragging]" class="onoffswitch-checkbox" <?php if ((isset($opts['block_image_dragging'])) && ($opts['block_image_dragging'] == 'on')) {
																																																																	echo 'checked="checked"';
																																																																} ?> />
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo esc_html(__('It will prevent users to stop image Drag and Drop outside website?', 'all-in-one-wp-content-security')); ?></small>
												</div>
											</div>

											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<span class="label"><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php echo esc_html(__('Block Right Clicking', 'all-in-one-wp-content-security')); ?></span>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_right_clicking]" class="onoffswitch-checkbox" <?php if ((isset($opts['block_right_clicking'])) && ($opts['block_right_clicking'] == 'on')) {
																																																																	echo 'checked="checked"';
																																																																} ?> />
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo esc_html(__('It will prevent users for being clicked using right or restricted for context menu', 'all-in-one-wp-content-security')); ?></small>
												</div>
											</div>

											<div class="field">
												<?php $fieldID = uniqid(); ?>
												<span class="label"><span class="dashicons dashicons-lock"></span> <?php echo esc_html(__('Block Console / Inspect Element', 'all-in-one-wp-content-security')); ?></span>
												<label class="switchContainer">
													<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_all_in_one_wp_content_security[block_hacking_website]" class="onoffswitch-checkbox" <?php if ((isset($opts['block_hacking_website'])) && ($opts['block_hacking_website'] == 'on')) {
																																																																	echo 'checked="checked"';
																																																																} ?> />
													<span for="<?php echo esc_attr($fieldID); ?>" class="sliderContainer round"></span>
												</label>
												<div class="small">
													<small><?php echo esc_html(__('** Prevents users from hacking website it blocks Console / Inspect Element', 'all-in-one-wp-content-security')); ?></small>
												</div>
											</div>

											<div class="form-footer">
												<input type="submit" class="button button-primary button-theme" value="<?php echo esc_html(__('Update Settings', 'all-in-one-wp-content-security')); ?>">
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
		$html = '<div class="header-plugin"><span class="header-icon"><span class="dashicons dashicons-admin-settings"></span></span> <span class="header-text">' . AOWPCS_PLUGIN_FULLNAME . '</span></div>';
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
								<img alt="Mahesh Thorat" src="https://secure.gravatar.com/avatar/13ac2a68e7fba0cc0751857eaac3e0bf?s=100&amp;d=mm&amp;r=g" srcset="https://secure.gravatar.com/avatar/13ac2a68e7fba0cc0751857eaac3e0bf?s=200&amp;d=mm&amp;r=g 2x" class="avatar avatar-100 photo profile-image" height="100" width="100">

								<div class="profile-by">
									<p>© <?php echo esc_attr(gmdate('Y')); ?> - created by <a class="link" href="https://maheshthorat.web.app/" target="_blank"><b>Mahesh Mohan Thorat</b></a></p>
								</div>
							</section>
							<section class="helpful-links">
								<b>Other Plugins</b>
								<ul>
									<li>
										<a href="//wordpress.org/plugins/ajax-loading/">
											<img srcset="https://ps.w.org/ajax-loading/assets/icon-128x128.png?rev=2838964, https://ps.w.org/ajax-loading/assets/icon-256x256.png?rev=2838964 2x" src="https://ps.w.org/ajax-loading/assets/icon-256x256.png?rev=2838964"> </a>

										<div class="plugin-info-container">
											<h4>
												<a href="//wordpress.org/plugins/ajax-loading/">AJAX Loading</a>
											</h4>
										</div>
									</li>
									<li>
										<a href="//wordpress.org/plugins/all-in-one-minifier/">
											<img srcset="https://ps.w.org/all-in-one-minifier/assets/icon-128x128.png?rev=2707658, https://ps.w.org/all-in-one-minifier/assets/icon-256x256.png?rev=2707658 2x" src="https://ps.w.org/all-in-one-minifier/assets/icon-256x256.png?rev=2707658"> </a>

										<div class="plugin-info-container">
											<h4>
												<a href="//wordpress.org/plugins/all-in-one-minifier/">All in one Minifier</a>
											</h4>
										</div>
									</li>
									<li>
										<a href="//wordpress.org/plugins/all-in-one-wp-content-security/">
											<img srcset="https://ps.w.org/all-in-one-wp-content-security/assets/icon-128x128.png?rev=2712431, https://ps.w.org/all-in-one-wp-content-security/assets/icon-256x256.png?rev=2712431 2x" src="https://ps.w.org/all-in-one-wp-content-security/assets/icon-256x256.png?rev=2712431"> </a>

										<div class="plugin-info-container">
											<h4>
												<a href="//wordpress.org/plugins/all-in-one-wp-content-security/">All in one WP Content Protector</a>
											</h4>
										</div>
									</li>
									<li>
										<a href="//wordpress.org/plugins/better-smooth-scroll/">
											<img srcset="https://ps.w.org/better-smooth-scroll/assets/icon-128x128.png?rev=2829532, https://ps.w.org/better-smooth-scroll/assets/icon-256x256.png?rev=2829532 2x" src="https://ps.w.org/better-smooth-scroll/assets/icon-256x256.png?rev=2829532"> </a>

										<div class="plugin-info-container">
											<h4>
												<a href="//wordpress.org/plugins/better-smooth-scroll/">Better Smooth Scroll</a>
											</h4>
										</div>
									</li>
								</ul>
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
											<td><a href="https://rzp.io/l/maheshmthorat" target="_blank"><img width="160" src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__))); ?>admin/assets/img/razorpay.svg" /></a></td>
										</tr>
										<tr>
											<td>
												<h3>Scan below code</h3>
												<img width="350" src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__))); ?>admin/assets/img/qr.svg" />
												<br>
												<img width="350" src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__))); ?>admin/assets/img/upi.png" />
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
		$opts = get_option('_all_in_one_wp_content_security');
		if (isset($opts) && !empty($opts)) {
			if (@$opts['block_selection'] == 'on' || @$opts['block_image_dragging'] == 'on' || @$opts['block_hacking_website'] == 'on') {
		?>
				<style type="text/css">
					<?php
					if (@$opts['block_selection'] == 'on') {
					?>* {
						-moz-user-select: none;
						-webkit-user-select: none;
						-ms-user-select: none;
						user-select: none;
						-o-user-select: none;
					}

					<?php
					}
					if (@$opts['block_image_dragging'] == 'on') {
					?>img {
						pointer-events: none
					}

					<?php
					}
					if (@$opts['block_image_dragging'] == 'on') {
					?>.console-open {
						background: #fafafa;
						width: 100%;
					}

					.console-open h1 {
						text-align: center;
						vertical-align: middle;
					}

					.console-open a {
						cursor: pointer;
					}

					<?php
					}
					?>
				</style>
			<?php
			}

			if (@$opts['block_right_clicking'] == 'on' || @$opts['block_hacking_website'] == 'on') {
				global $wp;
				$currentPageURL = home_url($wp->request);
				$printHTML = '<h1>We strongly not supporting to Inspect Element in Browser.<br/>Please <a href="' . esc_url($currentPageURL) . '">reload page</a>';
			?>
				<script>
					<?php
					if (@$opts['block_right_clicking'] == 'on') {
					?>
						window.oncontextmenu = function() {
							return false;
						};
						document.addEventListener('contextmenu', function(e) {
							e.preventDefault();
						});
					<?php
					}
					if (@$opts['block_hacking_website'] == 'on') {
					?>
							"use strict";
						let printHTML = '<?php echo wp_kses_post($printHTML); ?>';
						! function() {
							function detectDevTool(allow) {
								if (isNaN(+allow)) allow = 100;
								var start = +new Date();
								debugger;
								var end = +new Date();
								if (isNaN(start) || isNaN(end) || end - start > allow) {
									consoleCheck();
								}
							}
							if (window.attachEvent) {
								if (document.readyState === "complete" || document.readyState === "interactive") {
									detectDevTool();
									window.attachEvent('onresize', detectDevTool);
									window.attachEvent('onmousemove', detectDevTool);
									window.attachEvent('onfocus', detectDevTool);
									window.attachEvent('onblur', detectDevTool);
								} else {
									setTimeout(argument.callee, 0);
								}
							} else {
								window.addEventListener('load', detectDevTool);
								window.addEventListener('resize', detectDevTool);
								window.addEventListener('mousemove', detectDevTool);
								window.addEventListener('focus', detectDevTool);
								window.addEventListener('blur', detectDevTool);
							}
						}();

						function consoleCheck() {
							document.querySelector('body').classList.add('console-open');
							document.querySelector('body').innerHTML = printHTML;
						}

						document.onkeydown = function(e) {
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
					<?php
					}
					?>
				</script>
<?php
			}
		}
	}


	public function aowpcs_settings_link($links)
	{
		$url = get_admin_url() . 'options-general.php?page=all-in-one-wp-content-security-admin';
		$settings_link = ["<a href='$url'>" . __('Settings') . '</a>', "<a href='https://rzp.io/l/maheshmthorat' target='_blank'>Say Thanks</a>"];
		$links = array_merge(
			$settings_link,
			$links
		);
		return $links;
	}
}

?>