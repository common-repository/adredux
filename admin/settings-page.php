<?php
// Exit if File Accessed Directly
if ( ! defined( 'ABSPATH' ) ) {
	die( "Cheating! You are not allowed to access this page directly." );
}

/**
 * Welcome screen.
 */
class Adredux_Settings_Page {

	/**
	 * Class instance.
	 */
	private static $instance;

	/**
	 * Constructor method.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Adredux_Settings_Page ) ) {
			self::$instance = new Adredux_Settings_Page;
			self::$instance->setup_actions();
		}
		return self::$instance;
	}

	/**
	 * Sets up initial actions.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Add theme's info page to the Dashboard menu.
		add_action( 'admin_menu', array( self::$instance, 'register_menu_page' ) );
		// Add theme's info page scripts.
		add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_scripts' ) );

	}

	/**
	 * Load theme's info page styles.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_scripts() {
		global $pagenow;

		if ( 'admin.php' != $pagenow ) {
			return;
		}

		wp_enqueue_style( 'adredux-dashboard', ADREDUX_DIR_URI . '/admin/assets/dashboard-style.css', false, ADREDUX_VERSION );
	}

	/**
	 * Create theme's info page.
	 *
	 * @access public
	 * @return void
	 */
	public function register_menu_page() {


		// Add the menu item and page
		$page_title = __('Ad Redux', 'adredux');
		$menu_title = __('Ad Redux', 'adredux');
		$capability = 'manage_options';
		$slug = 'adredux';
		$callback = array( self::$instance, 'plugin_dashboard_page' );
		$icon = 'dashicons-admin-plugins';
		$position = 100;
		
		add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );

	}

	/**
	 * Display content of theme's dashabord page.
	 *
	 * @access public
	 * @return void
	 */
	public function plugin_dashboard_page() {
		require_once ADREDUX_DIR . '/admin/partials/dashboard-page.php';
	}

	/**
	 * Display tabs on the theme's dashabord page.
	 *
	 * @access public
	 * @param string
	 * @return void
	 */
	public function get_dashboard_page_tabs( $current_tab = '' ) {
		$tabs = array(
			array(
				'slug' => 'general',
				'title' => esc_html__( 'Settings', 'adredux' ),
			),
			array(
				'slug' => 'about',
				'title' => esc_html__( 'About', 'adredux' ),
			),
			array(
				'slug' => 'data',
				'title' => esc_html__( 'Settings Data', 'adredux' ),
			),
		);

		$tabs = apply_filters( 'adredux_dashboard_page_tabs', $tabs );

		foreach ( $tabs as $tab ) {
			if ( $current_tab === $tab['slug'] ) {
				$class = 'nav-tab nav-tab-active';
			} else {
				$class = 'nav-tab';
			}

			// Create URL for the current tab.
			$url = esc_url( admin_url( 'admin.php?page=adredux&tab=' . $tab['slug'] ) );

			/* translators: %1$s, %2$s and %3$s are a placeholders that will be replaced by variables passed as an argument. */
			printf( '<a class="%1$s" href="%2$s">%3$s</a>', $class, $url, $tab['title'] ); // WPCS: XSS OK.
		}
	}

	/**
	 * Display tabs content on the theme's dashabord page.
	 *
	 * @access public
	 * @param string
	 * @return void
	 */
	public function get_dashboard_page_tab_content( $current_tab = '' ) {
		$content = array(
			'general' => ADREDUX_DIR . '/admin/partials/dashboard-general.php',
			'about' => ADREDUX_DIR . '/admin/partials/dashboard-about.php',
			'data' => ADREDUX_DIR . '/admin/partials/dashboard-data.php',
		);

		$content = apply_filters( 'adredux_dashboard_page_tab_content', $content );

		if ( isset( $content[$current_tab] ) && file_exists( $content[$current_tab] ) ) {
			require_once $content[$current_tab];
		}
	}

	public function adredux_update_notice() { ?>

		<div class="notice notice-success is-dismissible">
			<p><?php _e('Your ad settings have been updated!', 'adredux'); ?></p>
		</div><?php

	}

}
Adredux_Settings_Page::instance();