<?php
/*
Plugin Name: Freelancer
Description: A Plugin for WordPress freelancers to use to manage clients sites
Plugin URI: https://github.com/andrewwoods/freelancer
Version: 0.1
Author: Andrew Woods
Author URI: http://andrewwoods.net
*/

/**
 * @todo Create a Site Administrator Role that with some Adminstrator privileges disabled
 *      - let Administrator modify the privileges of the new role.
 * @todo Hide the Adminstrator Role from non-Administrator users
 * @todo Form to change the 'admin' user to another name
 * @todo Provides you with a list of things to check before going live.
 */
class Freelancer
{
	private static $instance = null;

	private static $role_id = 'site_admin';
	private static $role_name = 'Site Administrator';

	/**
	 * constructor - manages all the actions and filters  
	 *
	 * @since 0.1
	 * @return void
	 */
	public function __construct() {

		register_activation_hook( __FILE__, array('Freelancer', 'register_activation_hook') );
		register_uninstall_hook( __FILE__, array('Freelancer', 'register_uninstall_hook') ); 

		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_head', array($this, 'admin_head'), 11 );

		add_filter( 'views_users', array(&$this, 'modify_views_users_remove_administrator_conditionally') );
	}

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return Freelancer A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}


	//--------------------------------------------------------------------------
	//	Hooks
	//--------------------------------------------------------------------------

	/**
	 * Performs these tasks when the plugin is activated.
	 *
	 * @since 0.1
	 * @todo update users that use site_admin role and set their role to editor 
	 *       - since site_admin role will no longer exist.
	 *
	 * @param  void
	 * @return void
	 */
	public static function register_activation_hook(){
		self::add_new_role();
	}

	/**
	 * Performs these tasks when the plugin is uninstalled.
	 *
	 * @since 0.1
	 * @todo update users that use site_admin role and set their role to editor 
	 *       - since site_admin role will no longer exist.
	 *
	 * @param  void
	 * @return void
	 */
	public static function register_uninstall_hook(){
		self::delete_roles();
	}

	/**
	* Disable the WordPress update nag for people without that capability
	*
	* Prevent the display of the WordPress update nag for people without that capability
	*
	* @since version 0.1
	*
	* @return void
	*/
	public function no_update_nag() {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}


	//--------------------------------------------------------------------------
	//	Actions
	//--------------------------------------------------------------------------


	public function admin_init(){
		if ( ! current_user_can( 'update_core' ) ) {
			$this->no_update_nag();
		}

	}

	public function admin_head(){
		$this->hide_admin_user_js();
	}


	//--------------------------------------------------------------------------

	public function add_new_role() {
		// Check if the role doesn't exist
		if (NULL === get_role( self::$role_id )) {
			// add the role 
			$admin_role = get_role( 'editor' ); 
			$capabilities = $admin_role->capabilities;

			$capabilities['activate_plugins'] = 0;
			$capabilities['create_users'] = 1;
			$capabilities['delete_plugins'] = 0;
			$capabilities['delete_themes'] = 0;
			$capabilities['delete_users'] = 1;
			$capabilities['edit_files'] = 1;
			$capabilities['edit_plugins'] = 0;
			$capabilities['edit_theme_options'] = 1;
			$capabilities['edit_themes'] = 0;
			$capabilities['edit_users'] = 1;
			$capabilities['export'] = 1;
			$capabilities['import'] = 0;

			$capabilities['install_plugins'] = 0;
			$capabilities['install_themes'] = 0;
			$capabilities['list_users'] = 1;
			$capabilities['manage_options'] = 1;
			$capabilities['promote_users'] = 1;
			$capabilities['remove_users'] = 1;
			$capabilities['switch_themes'] = 0;
			$capabilities['update_core'] = 0;
			$capabilities['update_plugins'] = 0;
			$capabilities['update_themes'] = 0;
			$capabilities['edit_dashboard'] = 1;

			add_role( self::$role_id, self::$role_name, $capabilities );
		}

	}

	public function delete_roles(){
		remove_role( self::$role_id );	
	}

	public function hide_admin_user_js(){
		if (! current_user_can('update_core')) {
		?>
		<script type="text/javascript">

		$(function() {
			$('.wp-admin #user-1').addClass('hidden');
		});

		</script>
		<?php
		}

	}

	//--------------------------------------------------------------------------
	//	Filters
	//--------------------------------------------------------------------------

	function modify_views_users_remove_administrator_conditionally( $views )
	{
		// Manipulate $views
		if (! current_user_can('update_core') ){
			unset( $views['administrator'] );
		}
		return $views;
	}


	//--------------------------------------------------------------------------
	//	Utility Methods
	//--------------------------------------------------------------------------




}

$freelancer_plugin = Freelancer::get_instance();

