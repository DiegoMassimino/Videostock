<?php
/*
Plugin Name: myStickymenu
Plugin URI: https://premio.io/
Description: Simple sticky (fixed on top) menu implementation for navigation menu. After install go to Settings / myStickymenu and change Sticky Class to .your_navbar_class or #your_navbar_id.
Version: 2.1.7
Author: Premio
Author URI: https://premio.io/downloads/mystickymenu/
Text Domain: mystickymenu
Domain Path: /languages
License: GPLv2 or later
*/

defined('ABSPATH') or die("Cannot access pages directly.");
define( 'MYSTICKY_VERSION', '2.1.7' );

class MyStickyMenuBackend
{
    private $options;

	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'mysticky_load_transl') );

		add_action( 'admin_init', array( $this, 'mysticky_default_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'mysticky_admin_script' ) );

		add_filter( 'plugin_action_links_mystickymenu/mystickymenu.php', array( $this, 'mystickymenu_settings_link' )  );
		
		add_action( 'activated_plugin', array( $this, 'mystickymenu_activation_redirect' ) );
    }

	public function mystickymenu_settings_link($links){
		$settings_link = '<a href="options-general.php?page=my-stickymenu-settings">Settings</a>';
		$links['go_pro'] = '<a href="'.admin_url("options-general.php?page=my-stickymenu-settings&type=upgrade").'" style="color: #FF5983;font-weight: bold;">'.__( 'Upgrade', 'stars-testimonials' ).'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	
	public function mystickymenu_activation_redirect( $plugin) {
		if( $plugin == plugin_basename( __FILE__ ) ) {
			wp_redirect( admin_url( 'options-general.php?page=my-stickymenu-settings' ) ) ;
			exit;
		}
	}

    public function mysticky_admin_script($hook) {
		if ($hook != 'settings_page_my-stickymenu-settings') {
			return;
		}

		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-slider');
		wp_register_script('mystickymenuAdminScript', plugins_url('/js/mystickymenu-admin.js', __FILE__), array( 'jquery' ), MYSTICKY_VERSION);
		wp_enqueue_script('mystickymenuAdminScript');
		
		wp_register_style('mystickymenuAdminStyle', plugins_url('/css/mystickymenu-admin.css', __FILE__), array(), MYSTICKY_VERSION );
	    wp_enqueue_style('mystickymenuAdminStyle');

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'my-script-handle', plugins_url('js/iris-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}

	public function mysticky_load_transl(){
		load_plugin_textdomain('mystickymenu', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	}

	function sanitize_options($value) {
		$value = stripslashes($value);
		$value = filter_var($value, FILTER_SANITIZE_STRING);
		return $value;
	}

	public function add_plugin_page(){
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'myStickymenu',
			'manage_options',
			'my-stickymenu-settings',
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page(){
		// Set class property
		if (isset($_POST['mysticky_option_name']) && !empty($_POST['mysticky_option_name']) && isset($_POST['nonce'])) {
			if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'mysticky_option_backend_update')) {
				$post = $_POST['mysticky_option_name'];
				foreach($post as $key=>$value) {
					$post[$key] = self::sanitize_options($value);
				}
				update_option( 'mysticky_option_name', $post);
				echo '<div class="updated settings-error notice is-dismissible "><p><strong>' . esc_html__('Settings saved.','mystickymenu'). '</p></strong></div>';
			} else {
				wp_verify_nonce($_GET['nonce'], 'wporg_frontend_delete');
				echo '<div class="error settings-error notice is-dismissible "><p><strong>' . esc_html__('Unable to complete your request','mystickymenu'). '</p></strong></div>';
			}
		}

		$mysticky_options = get_option( 'mysticky_option_name');
		$is_old = get_option("has_sticky_header_old_version");
		$is_old = ($is_old == "no")?false:true;
		$nonce = wp_create_nonce('mysticky_option_backend_update');

		?>
		<style>
			div#wpcontent {
				background: rgba(101,114,219,1);
				background: -moz-linear-gradient(-45deg, rgba(101,114,219,1) 0%, rgba(238,134,198,1) 67%, rgba(238,134,198,1) 100%);
				background: -webkit-gradient(left top, right bottom, color-stop(0%, rgba(101,114,219,1)), color-stop(67%, rgba(238,134,198,1)), color-stop(100%, rgba(238,134,198,1)));
				background: -webkit-linear-gradient(-45deg, rgba(101,114,219,1) 0%, rgba(238,134,198,1) 67%, rgba(238,134,198,1) 100%);
				background: -o-linear-gradient(-45deg, rgba(101,114,219,1) 0%, rgba(238,134,198,1) 67%, rgba(238,134,198,1) 100%);
				background: -ms-linear-gradient(-45deg, rgba(101,114,219,1) 0%, rgba(238,134,198,1) 67%, rgba(238,134,198,1) 100%);
				background: linear-gradient(135deg, rgba(101,114,219,1) 0%, rgba(238,134,198,1) 67%, rgba(238,134,198,1) 100%);
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#6572db', endColorstr='#ee86c6', GradientType=1 );
			}
		</style>
		<div id="mystickymenu" class="wrap mystickymenu">
			<div class="sticky-header-menu">
				<ul>
					<li><a href="#sticky-header-settings" class="<?php echo (isset($_GET['type'])&&$_GET['type']=="upgrade")?"":"active" ?>"><?php _e('Settings', 'mystickymenu'); ?></a></li>
					<li><a href="#sticky-header-upgrade" class="<?php echo (isset($_GET['type'])&&$_GET['type']=="upgrade")?"active":"" ?>"><?php _e('Upgrade to Pro', 'mystickymenu'); ?></a></li>
				</ul>
			</div>
			<div style="display: <?php echo (isset($_GET['type'])&&$_GET['type']=="upgrade")?"none":"block" ?>" id="sticky-header-settings" class="sticky-header-content">
				<div class="mystickymenu-heading">
					<div class="myStickymenu-header-title">
						<h3><?php _e('How To Make a Sticky Header', 'mystickymenu'); ?></h3>
					</div>
					<p><?php _e("Add sticky menu / header to any theme. <br />Simply change 'Sticky Class' to HTML element class desired to be sticky (div id can be used as well).", 'mystickymenu'); ?></p>
				</div>

				<form class="mysticky-form" method="post" action="#">
				<div class="mystickymenu-content-section sticky-class-sec">
					<table>
						<tr>
							<td>
								<label class="mysticky_title"><?php _e("Sticky Class", 'mystickymenu')?></label>
								<br /><br />
								<?php $nav_menus  = wp_get_nav_menus();
								$menu_locations = get_nav_menu_locations();
								$locations      = get_registered_nav_menus();
								?>
								<select name="mysticky_option_name[mysticky_class_id_selector]" id="mystickymenu-select">
									<option value=""><?php _e( 'Select Sticky Menu', 'mystickymenu' ); ?></option>

									<?php foreach ( (array) $nav_menus as $_nav_menu ) : ?>
										<option value="<?php echo esc_attr( $_nav_menu->slug ); ?>" <?php selected( $_nav_menu->slug, $mysticky_options['mysticky_class_id_selector'] ); ?>>
											<?php
											echo esc_html( $_nav_menu->name );

											if ( ! empty( $menu_locations ) && in_array( $_nav_menu->term_id, $menu_locations ) ) {
												$locations_assigned_to_this_menu = array();
												foreach ( array_keys( $menu_locations, $_nav_menu->term_id ) as $menu_location_key ) {
													if ( isset( $locations[ $menu_location_key ] ) ) {
														$locations_assigned_to_this_menu[] = $locations[ $menu_location_key ];
													}
												}

												/**
												 * Filters the number of locations listed per menu in the drop-down select.
												 *
												 * @since 3.6.0
												 *
												 * @param int $locations Number of menu locations to list. Default 3.
												 */
												$assigned_locations = array_slice( $locations_assigned_to_this_menu, 0, absint( apply_filters( 'wp_nav_locations_listed_per_menu', 3 ) ) );

												// Adds ellipses following the number of locations defined in $assigned_locations.
												if ( ! empty( $assigned_locations ) ) {
													printf(
														' (%1$s%2$s)',
														implode( ', ', $assigned_locations ),
														count( $locations_assigned_to_this_menu ) > count( $assigned_locations ) ? ' &hellip;' : ''
													);
												}
											}
											?>
										</option>
									<?php endforeach; ?>
									<option value="custom" <?php selected( 'custom', $mysticky_options['mysticky_class_id_selector'] ); ?>><?php esc_html_e( 'Other Class Or ID', 'mystickymenu' );?></option>
								</select>

								<input type="text" size="18" id="mysticky_class_selector" class="mystickyinput" name="mysticky_option_name[mysticky_class_selector]" value="<?php echo $mysticky_options['mysticky_class_selector'];?>"  />

								<p class="description"><?php _e("menu or header element class or id.", 'mystickymenu')?></p>
							</td>
							<td>
								<div class="mysticky_device_upgrade">
									<label class="mysticky_title"><?php _e("Devices", 'mystickymenu')?></label>
									<span class="myStickymenu-upgrade"><a class="sticky-header-upgrade-now" href="#" target="_blank"><?php _e( 'Upgrade Now', 'mystickymenu' );?></a></span>
									<ul class="mystickymenu-input-multicheckbox">
										<li>
										<label>
											<input id="disable_css" name="mysticky_option_name[device_desktop]" type="checkbox"  checked  disabled />
											<?php _e( 'Desktop', 'mystickymenu' );?>
										<label>
										</li>
										<li>
										<label>
											<input id="disable_css" name="mysticky_option_name[device_mobile]" type="checkbox" checked disabled />
											<?php _e( 'Mobile', 'mystickymenu' );?>
										<label>
										</li>
									</ul>
								</div>
							</td>
						</tr>
					</table>
				</div>


				<div class="mystickymenu-content-section">
					<h3><?php esc_html_e( 'Settings', 'mystickymenu' );?></h3>
					<table class="form-table">
						<tr>
							<td>
								<label for="myfixed_zindex" class="mysticky_title"><?php _e("Sticky z-index", 'mystickymenu')?></label>
							</td>
							<td>
								<input type="number" min="0" max="2147483647" step="1" class="mysticky-number" id="myfixed_zindex" name="mysticky_option_name[myfixed_zindex]" value="<?php echo $mysticky_options['myfixed_zindex'];?>" />
							</td>
							<td>
								<label class="mysticky_title myssticky-remove-hand"><?php _e("Fade or slide effect", 'mystickymenu')?></label>
							</td>
							<td>
								<label>
								<input name="mysticky_option_name[myfixed_fade]" value= "slide" type="radio" <?php checked( @$mysticky_options['myfixed_fade'], 'slide' );?> />
								<?php _e("Slide", 'mystickymenu'); ?>
								</label>
								<label>
								<input name="mysticky_option_name[myfixed_fade]" value="fade" type="radio"  <?php checked( @$mysticky_options['myfixed_fade'], 'fade' );?> />
								<?php _e("Fade", 'mystickymenu'); ?>
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<label for="myfixed_disable_small_screen" class="mysticky_title"><?php _e("Disable at Small Screen Sizes", 'mystickymenu')?></label>
								<p class="description"><?php _e('Less than chosen screen width, set 0 to disable','mystickymenu');?></p>
							</td>
							<td>
								<div class="px-wrap">
									<input type="number" class="" min="0" step="1" id="myfixed_disable_small_screen" name="mysticky_option_name[myfixed_disable_small_screen]" value="<?php echo $mysticky_options['myfixed_disable_small_screen'];?>" />
									<span class="input-px">PX</span>
								</div>
							</td>
							<td>
								<label for="mysticky_active_on_height" class="mysticky_title"><?php _e("Make visible on Scroll", 'mystickymenu')?></label>
								<p class="description"><?php _e('If set to 0 auto calculate will be used.','mystickymenu');?></p>
							</td>
							<td>
								<div class="px-wrap">
									<input type="number" class="small-text" min="0" step="1" id="mysticky_active_on_height" name="mysticky_option_name[mysticky_active_on_height]" value="<?php echo $mysticky_options['mysticky_active_on_height'];?>" />
									<span class="input-px">PX</span>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="mysticky_active_on_height_home" class="mysticky_title"><?php _e("Make visible on Scroll at homepage", 'mystickymenu')?></label>
								<p class="description"><?php _e( 'If set to 0 it will use initial Make visible on Scroll value.', 'mystickymenu' );?></p>
							</td>
							<td>
								<div class="px-wrap">
									<input type="number" class="small-text" min="0" step="1" id="mysticky_active_on_height_home" name="mysticky_option_name[mysticky_active_on_height_home]" value="<?php echo $mysticky_options['mysticky_active_on_height_home'];?>" />
									<span class="input-px">PX</span>
								</div>
							</td>
							<td>
								<label for="myfixed_bgcolor" class="mysticky_title myssticky-remove-hand"><?php _e("Sticky Background Color", 'mystickymenu')?></label>
							</td>
							<td>
								<input type="text" id="myfixed_bgcolor" name="mysticky_option_name[myfixed_bgcolor]" class="my-color-field" value="<?php echo $mysticky_options['myfixed_bgcolor'];?>" />

							</td>
						</tr>
						<tr>
							<td>
								<label for="myfixed_transition_time" class="mysticky_title"><?php _e("Sticky Transition Time", 'mystickymenu')?></label>
							</td>
							<td>
								<input type="number" class="small-text" min="0" step="0.1" id="myfixed_transition_time" name="mysticky_option_name[myfixed_transition_time]" value="<?php echo $mysticky_options['myfixed_transition_time'];?>" />
							</td>
							<td>
								<label for="myfixed_opacity" class="mysticky_title myssticky-remove-hand"><?php _e("Sticky Opacity", 'mystickymenu')?></label>
								<p class="description"><?php _e( 'numbers 1-100.', 'mystickymenu');?></p>
							</td>
							<td>
								<input type="hidden" class="small-text mysticky-slider" min="0" step="1" max="100" id="myfixed_opacity" name="mysticky_option_name[myfixed_opacity]"  value="<?php echo $mysticky_options['myfixed_opacity'];?>"  />
								<div id="slider">
								  <div id="custom-handle" class="ui-slider-handle"><?php //echo $mysticky_options['myfixed_opacity'];?></div>
								</div>

							</td>
						</tr>
					</table>
				</div>

				<div class="mystickymenu-content-section <?php echo !$is_old?"mystickymenu-content-upgrade":""?>" >

					<div class="mystickymenu-content-option">
						<label class="mysticky_title css-style-title"><?php _e("Hide on Scroll Down", 'mystickymenu'); ?></label>
						<?php if(!$is_old) { ?><span class="myStickymenu-upgrade"><a class="sticky-header-upgrade-now" href="#" target="_blank"><?php _e( 'Upgrade Now', 'mystickymenu' );?></a></span><?php } ?>
						<p>
						<label class="mysticky_text">
							<input id="myfixed_disable_scroll_down" name="mysticky_option_name[myfixed_disable_scroll_down]" type="checkbox" <?php checked( @$mysticky_options['myfixed_disable_scroll_down'], 'on' );?> <?php echo !$is_old?"disabled":"" ?> />
							<?php _e("Disable sticky menu at scroll down", 'mystickymenu'); ?>
							</label>
						</p>
					</div>
					<div class="mystickymenu-content-option">
						<label class="mysticky_title css-style-title"><?php _e("CSS style", 'mystickymenu'); ?></label>
						<span class="mysticky_text"><?php _e( 'Add/edit CSS style. Leave it blank for default style.', 'mystickymenu');?></span>
						<div class="mystickymenu-input-section">
							<textarea type="text" rows="4" cols="60" id="myfixed_cssstyle" name="mysticky_option_name[myfixed_cssstyle]"  <?php echo !$is_old?"disabled":"" ?> ><?php echo @$mysticky_options['myfixed_cssstyle'];?></textarea>
						</div>
						<p><?php esc_html_e( "CSS ID's and Classes to use:", "mystickymenu" );?></p>
						<p>
							#mysticky-wrap { }<br/>
							#mysticky-nav.wrapfixed { }<br/>
							#mysticky-nav.wrapfixed.up { }<br/>
							#mysticky-nav.wrapfixed.down { }<br/>
							#mysticky-nav .navbar { }<br/>
							#mysticky-nav .navbar.myfixed { }<br/>
						</p>
					</div>

					<div class="mystickymenu-content-option">
						<label class="mysticky_title" for="disable_css"><?php _e("Disable CSS style", 'mystickymenu'); ?></label>
						<div class="mystickymenu-input-section">
							<label>
							<input id="disable_css" name="mysticky_option_name[disable_css]" type="checkbox"   <?php echo !$is_old?"disabled":"" ?> <?php checked( @$mysticky_options['disable_css'], 'on' );?> />
							<?php _e( 'Use this option if you plan to include CSS Style manually', 'mystickymenu' );?>
							<label>
						</div>
						<p></p>
					</div>

					<div class="mystickymenu-content-option">
						<label class="mysticky_title"><?php _e("Disable at", 'mystickymenu'); ?></label>
						<?php if(!$is_old) { ?><span class="myStickymenu-upgrade"><a class="sticky-header-upgrade-now" href="#" target="_blank"><?php _e( 'Upgrade Now', 'mystickymenu' );?></a></span><?php } ?>
						<div class="mystickymenu-input-section">
							<ul class="mystickymenu-input-multicheckbox">
								<li>
									<label>
										<input id="mysticky_disable_at_front_home" name="mysticky_option_name[mysticky_disable_at_front_home]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?>  <?php checked( @$mysticky_options['mysticky_disable_at_front_home'], 'on' );?>/>
										<span><?php _e('front page', 'mystickymenu' );?></span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_blog" name="mysticky_option_name[mysticky_disable_at_blog]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?>  <?php checked( @$mysticky_options['mysticky_disable_at_blog'], 'on' );?>/>
										<span><?php _e('blog page', 'mystickymenu' );?></span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_page" name="mysticky_option_name[mysticky_disable_at_page]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?> <?php checked( @$mysticky_options['mysticky_disable_at_page'], 'on' );?> />
										<span><?php _e('pages', 'mystickymenu' );?> </span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_tag" name="mysticky_option_name[mysticky_disable_at_tag]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?> <?php checked( @$mysticky_options['mysticky_disable_at_tag'], 'on' );?> />
										<span><?php _e('tags', 'mystickymenu' );?> </span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_category" name="mysticky_option_name[mysticky_disable_at_category]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?>  <?php checked( @$mysticky_options['mysticky_disable_at_category'], 'on' );?>/>
										<span><?php _e('categories', 'mystickymenu' );?></span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_single" name="mysticky_option_name[mysticky_disable_at_single]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?> <?php checked( @$mysticky_options['mysticky_disable_at_single'], 'on' );?> />
										<span><?php _e('posts', 'mystickymenu' );?> </span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_archive" name="mysticky_option_name[mysticky_disable_at_archive]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?> <?php checked( @$mysticky_options['mysticky_disable_at_archive'], 'on' );?> />
										<span><?php _e('archives', 'mystickymenu' );?> </span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_search" name="mysticky_option_name[mysticky_disable_at_search]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?> <?php checked( @$mysticky_options['mysticky_disable_at_search'], 'on' );?> />
										<span><?php _e('search', 'mystickymenu' );?> </span>
									</label>
								</li>
								<li>
									<label>
										<input id="mysticky_disable_at_404" name="mysticky_option_name[mysticky_disable_at_404]" type="checkbox"  <?php echo !$is_old?"disabled":"" ?>  <?php checked( @$mysticky_options['mysticky_disable_at_404'], 'on' );?>/>
										<span><?php _e('404', 'mystickymenu' );?> </span>
									</label>
								</li>
							</ul>
							
							<?php 
							if  (isset ( $mysticky_options['mysticky_disable_at_page'] ) == true )  {			
								echo '<div class="mystickymenu-input-section">';
								_e('<span class="description"><strong>Except for this pages:</strong> </span>', 'mystickymenu');
						
								printf(
									'<input type="text" size="26" class="mystickymenu_normal_text" id="mysticky_enable_at_pages" name="mysticky_option_name[mysticky_enable_at_pages]" value="%s"  /> ',  
									isset( $mysticky_options['mysticky_enable_at_pages'] ) ? esc_attr( $mysticky_options['mysticky_enable_at_pages']) : '' 
								); 
								
								_e('<span class="description">Comma separated list of pages to enable. It should be page name, id or slug. Example: about-us, 1134, Contact Us. Leave blank if you realy want to disable sticky menu for all pages.</span>', 'mystickymenu');
								echo '</div>';								
							}
							
							if  (isset ( $mysticky_options['mysticky_disable_at_single'] ) == true )  {
			
								echo '<div class="mystickymenu-input-section">';
								_e('<span class="description"><strong>Except for this posts:</strong> </span>', 'mystickymenu');
						
								printf(
									'<input type="text" size="26" class="mystickymenu_normal_text" id="mysticky_enable_at_posts" name="mysticky_option_name[mysticky_enable_at_posts]" value="%s" /> ',  
									isset( $mysticky_options['mysticky_enable_at_posts'] ) ? esc_attr( $mysticky_options['mysticky_enable_at_posts']) : '' 
								); 
								
								_e('<span class="description">Comma separated list of posts to enable. It should be post name, id or slug. Example: about-us, 1134, Contact Us. Leave blank if you realy want to disable sticky menu for all posts.</span>', 'mystickymenu');
								echo '</div>';								
								
							}
							?>
							<p></p>
						</div>
					</div>
				</div>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save', 'mystickymenu');?>">
				</p>
				<input type="hidden" name="nonce" value="<?php echo $nonce ?>">
				</form>
				<form class="mysticky-hideformreset" method="post" action="">
					<input name="reset_mysticky_options" class="button button-secondary confirm" type="submit" value="<?php _e('Reset', 'mystickymenu');?>" >
					<input type="hidden" name="action" value="reset" />
					<?php $nonce = wp_create_nonce('mysticky_option_backend_reset_nonce'); ?>
					<input type="hidden" name="nonce" value="<?php echo $nonce ?>">
				</form>
				<p class="myStickymenu-review"><a href="https://wordpress.org/support/plugin/mystickymenu/reviews/" target="_blank"><?php _e('Leave a review','mystickymenu'); ?></a></p>
			</div>
			<div style="display: <?php echo (isset($_GET['type'])&&$_GET['type']=="upgrade")?"block":"none" ?>" id="sticky-header-upgrade" class="sticky-header-content">
				<div id="rpt_pricr" class="rpt_plans rpt_3_plans  rpt_style_basic">
					<p class="udner-title">
						<strong class="text-primary">Unlock All Features</strong>
					</p>
					<div class="">
						<div class="rpt_plan  rpt_plan_0  ">
							<div style="text-align:left;" class="rpt_title rpt_title_0">Basic</div>
							<div class="rpt_head rpt_head_0">
								<div class="rpt_recurrence rpt_recurrence_0">For small website owners</div>
								<div class="rpt_price rpt_price_0">$9</div>
								<div class="rpt_description rpt_description_0">Per year. Renewals for 25% off</div>
								<div style="clear:both;"></div>
							</div>
							<div class="rpt_features rpt_features_0">
								<div class="rpt_feature rpt_feature_0-0"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Use myStickymenu on 1 domain</span>1 website<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_0-1"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>You can show the menu when scrolling up, down or both</span>Show on scroll up/down<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_0-2"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>You can disable the sticky effect on desktop or mobile</span>Devices<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_0-3"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Add CSS of your own to the sticky menu</span>CSS style<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_0-4"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Exclude pages you don't want to have sticky menu</span>Page targeting<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_0-5"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Fade/Slide, opacity, background color, transition time and more</span>Effects and more<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_0-6">Updates and support for 1 year</div>
							</div>
							<div style="clear:both;"></div>
							<a target="_blank" href="https://go.premio.io/?edd_action=add_to_cart&amp;download_id=2199&amp;edd_options[price_id]=1" class="rpt_foot rpt_foot_0">Buy now</a>
						</div>
						<div class="rpt_plan  rpt_plan_1 rpt_recommended_plan ">
							<div style="text-align:left;" class="rpt_title rpt_title_1">Pro<img class="rpt_recommended" src="<?php echo plugins_url("") ?>/mystickymenu/images/rpt_recommended.png" style="top: 27px;"></div>
							<div class="rpt_head rpt_head_1">
								<div class="rpt_recurrence rpt_recurrence_1">For businesses with multiple websites</div>
								<div class="rpt_price rpt_price_1">$25</div>
								<div class="rpt_description rpt_description_1">Per year. Renewals for 25% off</div>
								<div style="clear:both;"></div>
							</div>
							<div class="rpt_features rpt_features_1">
								<div class="rpt_feature rpt_feature_1-0"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Use myStickymenu on 5 domains</span>5 websites<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_1-1"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>You can show the menu when scrolling up, down or both</span>Show on scroll up/down<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_1-2"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>You can disable the sticky effect on desktop or mobile</span>Devices<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_1-3"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Add CSS of your own to the sticky menu</span>CSS style<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_1-4"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Exclude pages you don't want to have sticky menu</span>Page targeting<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_1-5"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Fade/Slide, opacity, background color, transition time and more</span>Effects and more<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_1-6">Updates and support for 1 year</div>
							</div>
							<div style="clear:both;"></div>
							<a target="_blank" href="https://go.premio.io/?edd_action=add_to_cart&amp;download_id=2199&amp;edd_options[price_id]=2" class="rpt_foot rpt_foot_1">Buy now</a>
						</div>
						<div class="rpt_plan  rpt_plan_2  ">
							<div style="text-align:left;" class="rpt_title rpt_title_2">Agency</div>
							<div class="rpt_head rpt_head_2">
								<div class="rpt_recurrence rpt_recurrence_2">For agencies who manage clients</div>
								<div class="rpt_price rpt_price_2">$49</div>
								<div class="rpt_description rpt_description_2">Per year. Renewals for 25% off</div>
								<div style="clear:both;"></div>
							</div>
							<div class="rpt_features rpt_features_2">
								<div class="rpt_feature rpt_feature_2-0"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Use myStickymenu on 20 domains</span>20 websites<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_2-1"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>You can show the menu when scrolling up, down or both</span>Show on scroll up/down<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_2-2"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>You can disable the sticky effect on desktop or mobile</span>Devices<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_2-3"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Add CSS of your own to the sticky menu</span>CSS style<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_2-4"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Exclude pages you don't want to have sticky menu</span>Page targeting<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_2-5"><a href="javascript:;" class="rpt_tooltip"><span class="intool"><b></b>Fade/Slide, opacity, background color, transition time and more</span>Effects and more<span class="rpt_tooltip_plus" > +</span></a></div>
								<div class="rpt_feature rpt_feature_2-6">Updates and support for 1 year</div>
							</div>
							<div style="clear:both;"></div>
							<a target="_blank" href="https://go.premio.io/?edd_action=add_to_cart&amp;download_id=2199&amp;edd_options[price_id]=3" class="rpt_foot rpt_foot_2">Buy now</a>
						</div>
					</div>
					<div style="clear:both;"></div>
					<div class="client-testimonial">
						<p class="text-center"><span class="dashicons dashicons-yes"></span> 30 days money back guaranteed</p>
						<p class="text-center"><span class="dashicons dashicons-yes"></span> The plugin will always keep working even if you don't renew your license</p>
						<div class="payment">
							<img src="<?php echo plugins_url("") ?>/mystickymenu/images/payment.png" alt="Payment" class="payment-img" />
						</div>
						<div class="testimonial-box">
							<div class="testimonial-image">
								<img src="<?php echo plugins_url("") ?>/mystickymenu/images/testimonial.png" style="top: 27px;">
							</div>
							<div class="testimonial-content">
								This plugin does exactly what it should. It is simple but powerful. I would suggest to anyone who wants to make their menu sticky! I especially love the hide header on scroll down, show on scroll up feature that is built it. Great work!
								<div class="author">Clayton Chase</div>
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
				</div>
			</div>
        </div>
		<?php
	}
	public function mysticky_default_options() {

		global $options;
		$menu_locations = get_nav_menu_locations();		
		$menu_object = isset($menu_locations['menu-1']) ? wp_get_nav_menu_object( $menu_locations['menu-1'] ) : array();
		
		if ( is_object($menu_object) && $menu_object->slug != '' ) {
			$mysticky_class_id_selector = $menu_object->slug;
		} else {
			$mysticky_class_id_selector = 'custom';
		}
		$default = array(
				'mysticky_class_id_selector'	=> $mysticky_class_id_selector,
				'mysticky_class_selector' 		=> '.navbar',
				'device_desktop' 				=> 'on',
				'device_mobile' 				=> 'on',
				'myfixed_zindex' 				=> '99990',
				'myfixed_bgcolor' 				=> '#f7f5e7',
				'myfixed_opacity' 				=> '90',
				'myfixed_transition_time' 		=> '0.3',
				'myfixed_disable_small_screen' 	=> '0',
				'myfixed_disable_large_screen' 	=> '0',
				'mysticky_active_on_height' 	=> '0',
				'mysticky_active_on_height_home'=> '0',
				'myfixed_fade' 					=> 'slide',
				'myfixed_cssstyle' 				=> '#mysticky-nav .myfixed { margin:0 auto; float:none; border:0px; background:none; max-width:100%; }'
			);

		if ( get_option('mysticky_option_name') == false ) {
			$status = get_option("sticky_header_status");
			if($status == false) {
				update_option("sticky_header_status", "done");
				update_option("has_sticky_header_old_version", "no");
			}
			update_option( 'mysticky_option_name', $default );
		} else {
			$status = get_option("sticky_header_status");
			if($status == false) {
				update_option("sticky_header_status", "done");
				update_option("has_sticky_header_old_version", "yes");
			}
		}

		if(isset($_POST['reset_mysticky_options'])) {
			if(isset($_REQUEST['nonce']) && !empty($_REQUEST['nonce'])  && wp_verify_nonce($_REQUEST['nonce'], 'mysticky_option_backend_reset_nonce')) {
				update_option('mysticky_option_name', $default);
			} else {

			}
		}
		
		if ( !get_option( 'update_mysticky_version_2_6') ) {
			$mysticky_option_name = get_option( 'mysticky_option_name' );
			$mysticky_option_name['mysticky_class_id_selector'] = 'custom';
			if ($mysticky_option_name['myfixed_fade'] == 'on'){
				$mysticky_option_name['myfixed_fade'] = 'slide';
			}else{
				$mysticky_option_name['myfixed_fade'] = 'fade';
			}
			update_option( 'mysticky_option_name', $mysticky_option_name );
			update_option( 'update_mysticky_version_2_6', true );
		}
	}
}



class MyStickyMenuFrontend
{

	public function __construct()
	{
		add_action( 'wp_head', array( $this, 'mysticky_build_stylesheet_content' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'mysticky_disable_at' ) );
	}

	public function mysticky_build_stylesheet_content() {

		$mysticky_options = get_option( 'mysticky_option_name' );

		if (isset($mysticky_options['disable_css'])) {
			//do nothing
		} else {
			$mysticky_options['disable_css'] = false;
		}

		if  ($mysticky_options ['disable_css'] == false ) {

			echo '<style id="mystickymenu" type="text/css">';
			echo '#mysticky-nav { width:100%; position: static; }';
			echo '#mysticky-nav.wrapfixed { position:fixed; left: 0px; margin-top:0px;  z-index: '. $mysticky_options ['myfixed_zindex'] .'; -webkit-transition: ' . $mysticky_options ['myfixed_transition_time'] . 's; -moz-transition: ' . $mysticky_options ['myfixed_transition_time'] . 's; -o-transition: ' . $mysticky_options ['myfixed_transition_time'] . 's; transition: ' . $mysticky_options ['myfixed_transition_time'] . 's; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=' . $mysticky_options ['myfixed_opacity'] . ')"; filter: alpha(opacity=' . $mysticky_options ['myfixed_opacity'] . '); opacity:' . $mysticky_options ['myfixed_opacity'] / 100 . '; background-color: ' . $mysticky_options ['myfixed_bgcolor'] . ';}';


			if  ($mysticky_options ['myfixed_disable_small_screen'] > 0 ){
			//echo '@media (max-width: '.$mysticky_options['myfixed_disable_small_screen'].'px) {#mysticky-nav.wrapfixed {position: static;} }';
			};
			if ( !isset( $mysticky_options['myfixed_cssstyle'] ) )  {
				echo '#mysticky-nav .myfixed { margin:0 auto; float:none; border:0px; background:none; max-width:100%; }';
			}
			if ( isset( $mysticky_options['myfixed_cssstyle'] ) && $mysticky_options['myfixed_cssstyle'] != '' )  {
				echo $mysticky_options ['myfixed_cssstyle'];
			}
			echo '</style>';
		}
	}

	public function mystickymenu_script() {

		$mysticky_options = get_option( 'mysticky_option_name' );

		if ( is_admin_bar_showing() ) {
			$top = "true";
		} else {
			$top = "false";
		}


		// needed for update 1.7 => 1.8 ... will be removed in the future ()
		if (isset($mysticky_options['mysticky_active_on_height_home'])) {
			//do nothing
		} else {
			$mysticky_options['mysticky_active_on_height_home'] = $mysticky_options['mysticky_active_on_height'];
		}


		if  ($mysticky_options['mysticky_active_on_height_home'] == 0 ) {
			$mysticky_options['mysticky_active_on_height_home'] = $mysticky_options['mysticky_active_on_height'];
		}


		if ( is_front_page() && is_home() ) {

			$mysticky_options['mysticky_active_on_height'] = $mysticky_options['mysticky_active_on_height_home'];

		} elseif ( is_front_page()){

			$mysticky_options['mysticky_active_on_height'] = $mysticky_options['mysticky_active_on_height_home'];

		}
		wp_register_script('detectmobilebrowser', plugins_url( 'js/detectmobilebrowser.js', __FILE__ ), array('jquery'), MYSTICKY_VERSION, true);
		wp_enqueue_script( 'detectmobilebrowser' );
		
		wp_register_script('mystickymenu', plugins_url( 'js/mystickymenu.min.js', __FILE__ ), array('jquery'), MYSTICKY_VERSION, true);
		wp_enqueue_script( 'mystickymenu' );

		$myfixed_disable_scroll_down = isset($mysticky_options['myfixed_disable_scroll_down']) ? $mysticky_options['myfixed_disable_scroll_down'] : 'false';
		$mystickyTransition = isset($mysticky_options['myfixed_fade']) ? $mysticky_options['myfixed_fade'] : 'fade';
		$mystickyDisableLarge = isset($mysticky_options['myfixed_disable_large_screen']) ? $mysticky_options['myfixed_disable_large_screen'] : '0';

		$mystickyClass = ( $mysticky_options['mysticky_class_id_selector'] != 'custom') ? '.menu-' . $mysticky_options['mysticky_class_id_selector'] .'-container' : $mysticky_options['mysticky_class_selector'];

		$mysticky_translation_array = array(
		    'mystickyClass' 			=> $mystickyClass,
			'activationHeight' 			=> $mysticky_options['mysticky_active_on_height'],
			'disableWidth' 				=> $mysticky_options['myfixed_disable_small_screen'],
			'disableLargeWidth' 		=> $mystickyDisableLarge,
			'adminBar' 					=> $top,
			'device_desktop'			=> true,
			'device_mobile' 			=> true,
			'mystickyTransition' 		=> $mystickyTransition,
			'mysticky_disable_down' 	=> $myfixed_disable_scroll_down,


		);
		wp_localize_script( 'mystickymenu', 'option', $mysticky_translation_array );		
	}

	public function mysticky_disable_at() {


		$mysticky_options = get_option( 'mysticky_option_name' );

		$mysticky_disable_at_front_home = isset($mysticky_options['mysticky_disable_at_front_home']);
		$mysticky_disable_at_blog = isset($mysticky_options['mysticky_disable_at_blog']);
		$mysticky_disable_at_page = isset($mysticky_options['mysticky_disable_at_page']);
		$mysticky_disable_at_tag = isset($mysticky_options['mysticky_disable_at_tag']);
		$mysticky_disable_at_category = isset($mysticky_options['mysticky_disable_at_category']);
		$mysticky_disable_at_single = isset($mysticky_options['mysticky_disable_at_single']);
		$mysticky_disable_at_archive = isset($mysticky_options['mysticky_disable_at_archive']);
		$mysticky_disable_at_search = isset($mysticky_options['mysticky_disable_at_search']);
		$mysticky_disable_at_404 = isset($mysticky_options['mysticky_disable_at_404']);
		$mysticky_enable_at_pages = isset($mysticky_options['mysticky_enable_at_pages']) ? $mysticky_options['mysticky_enable_at_pages'] : '';
		$mysticky_enable_at_posts = isset($mysticky_options['mysticky_enable_at_posts']) ? $mysticky_options['mysticky_enable_at_posts'] : '';

		// Trim input to ignore empty spaces
		$mysticky_enable_at_pages_exp = array_map('trim', explode(',', $mysticky_enable_at_pages));
		$mysticky_enable_at_posts_exp = array_map('trim', explode(',', $mysticky_enable_at_posts));




		if ( is_front_page() && is_home() ) { /* Default homepage */

			if ( $mysticky_disable_at_front_home == false ) {
				$this->mystickymenu_script();
			}
		} elseif ( is_front_page()){ /* Static homepage */

			if ( $mysticky_disable_at_front_home == false ) {
				$this->mystickymenu_script();
			}

		} elseif ( is_home()){ /* Blog page */

			if ( $mysticky_disable_at_blog == false ) {
				$this->mystickymenu_script();
			}

		} elseif ( is_page() ){ /* Single page*/

			if ( $mysticky_disable_at_page == false ) {
				$this->mystickymenu_script();
			}
			if ( is_page( $mysticky_enable_at_pages_exp  )  ){
				$this->mystickymenu_script();
			}

		} elseif ( is_tag()){ /* Tag page */

			if ( $mysticky_disable_at_tag == false ) {
				$this->mystickymenu_script();
			}

		} elseif ( is_category()){ /* Category page */

			if ( $mysticky_disable_at_category == false ) {
				$this->mystickymenu_script();
			}

		} elseif ( is_single()){ /* Single post */

			if ( $mysticky_disable_at_single == false ) {
				$this->mystickymenu_script();
			}

			if ( is_single( $mysticky_enable_at_posts_exp  )  ){
				$this->mystickymenu_script();
			}

		} elseif ( is_archive()){ /* Archive */

			if ( $mysticky_disable_at_archive == false ) {
				$this->mystickymenu_script();
			}

		} elseif ( is_search()){ /* Search */

			if ( $mysticky_disable_at_search == false ) {
				$this->mystickymenu_script();
			}

		} elseif ( is_404()){ /* 404 */

			if ( $mysticky_disable_at_404 == false ) {
				$this->mystickymenu_script();
			}
		}

	}

}

if( is_admin() ) {
	new MyStickyMenuBackend();
} else {
	new MyStickyMenuFrontend();
}