<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Projects
 * @subpackage SPG_Projects/includes
 */
namespace SPG_Projects;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SPG_Projects
 * @subpackage SPG_Projects/includes
 * @author     Mitch Negus <mitchell.negus.57@gmail.com>
 */
class Projects {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Projects_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies and set the hooks for the admin area and public-facing side
	 * of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Set plugin overhead details
		if ( defined( 'SPG_PROJECTS_VERSION' ) ) {
			$this->version = SPG_PROJECTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'spg-projects';
		// Create arrays of meta keys that are assigned to custom project posts
		$this->project_meta = array(
			array('meta_key' => 'contact_name', 'required' => false),
		 	array('meta_key' => 'contact_email', 'required'   => false),
			array('meta_key' => 'references',	'required'   => false),
			array('meta_key' => 'project_type', 'required' => false)
		);
		$this->meta_titles = array(
			'project_type'  => 'Project Type',
			'contact_name'  => 'Contact Name',
			'contact_email' => 'Contact Email',
			'references'    => 'References'
		);		

		// Load plugin dependencies and set actions and filters for hooks
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Projects_Loader. Orchestrates the hooks of the plugin.
	 * - Projects_Admin. Defines all hooks for the admin area.
	 * - Projects_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-projects-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-projects-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-projects-public.php';

		$this->loader = new Projects_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Projects_Admin( 
			$this->get_plugin_name(),
			$this->get_version(),
			$this->get_project_meta(),
			$this->get_meta_titles()
	 	);

		// Provide admin area controls for project custom posts
		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_admin_fields');
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_project_details');
		// Update the columns on the browse projects page
		$this->loader->add_action( 'manage_projects_posts_custom_column', $plugin_admin, 'fill_project_columns', 10, 2 );
		$this->loader->add_filter( 'manage_projects_posts_columns', $plugin_admin, 'set_project_columns' );
		$this->loader->add_filter( 'manage_edit-projects_sortable_columns', $plugin_admin, 'set_project_sortable_columns');
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'project_posts_orderby' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Projects_Public( 
			$this->get_plugin_name(),
			$this->get_version(),
			$this->get_project_meta()
	 	);

		// Set public-facing styles and JavaScript
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// Hook up our custom post to theme setup
		$this->loader->add_action( 'init', $plugin_public, 'register_project_post_type' );
		// Use custom templates for the project pages
		$this->loader->add_filter( 'archive_template', $plugin_public, 'use_project_archive_template' );
		// Format the 'Projects' page properly
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'show_all_projects' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		// Add theme support for thumbnails if not already included
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 200, 200 );
		// Run the loader (with hooks for actions and filters)
		$this->loader->run();

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Projects_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the custom project post meta keys.
	 *
	 * @since     1.0.0
	 * @return    string    An array of custom post meta keys used by the plugin.
	 */
	public function get_project_meta() {
		return $this->project_meta;
	}

	/**
	 * Retrieve titles for the custom post meta keys.
	 *
	 * @since     1.0.0
	 * @return    string    An array of titles for custom post meta keys.
	 */
	public function get_meta_titles() {
		return $this->meta_titles;
	}

}
