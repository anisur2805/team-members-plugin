<?php
/**
 * Plugin Name:       Team Members
 * Description:       A custom WordPress plugin to manage and display team members.
 * Version:           1.0
 * Author:            Anisur Rahman
 * Author URI:        https://github.com/anisur2805
 * Text Domain:       z7-team-members
 * License:           GPL2
 *
 * @package           Z7_Team_Members
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

// Define Plugin Constants.
define( 'Z7_TEAM_MEMBERS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'Z7_TEAM_MEMBERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Team Members Plugin Class.
 */
class Z7_Team_Members_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Z7_Team_Members_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @return Z7_Team_Members_Plugin
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->includes();
		$this->load_textdomain();

		add_action( 'admin_init', array( $this, 'z7_team_members_redirect' ) );
	}

	/**
	 * Redirects admin to the team members page on first activation.
	 */
	public function z7_team_members_redirect() {
		if ( get_option( 'z7_do_activation_redirect', false ) ) {
			delete_option( 'z7_do_activation_redirect' );

			if ( is_admin() && current_user_can( 'manage_options' ) ) {
				wp_safe_redirect( admin_url( 'edit.php?post_type=team_member' ) );
				exit;
			}
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'z7-team-members', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Include required files.
	 *
	 * @return void
	 */
	private function includes() {
		$includes = array(
			'includes/class-team-members.php',
			'includes/class-team-members-shortcode.php',
		);

		foreach ( $includes as $file ) {
			$file_path = Z7_TEAM_MEMBERS_PLUGIN_PATH . $file;
			if ( file_exists( $file_path ) ) {
				require_once $file_path;
			}
		}

		new Z7_Team_Members();
		new Z7_Team_Members_Shortcode();
	}
}

// Initialize the plugin.
function z7_team_members_init() {
	Z7_Team_Members_Plugin::get_instance();
}
add_action( 'plugins_loaded', 'z7_team_members_init' );

/**
 * Runs on plugin activation - Sets an option for redirection.
 */
function z7_team_members_activate() {
	add_option( 'z7_do_activation_redirect', true );
}
register_activation_hook( __FILE__, 'z7_team_members_activate' );
