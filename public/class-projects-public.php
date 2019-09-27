<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Projects
 * @subpackage SPG_Projects/public
 */
namespace SPG_Projects;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for managing the public front
 * end (including enqueuing the public-facing stylesheet and JavaScript). An
 * instance of this class should be passed to the run() function defined
 * in Projects_Loader as all of the hooks are actually defined in that
 * particular class. The Projects_Loader will then create the
 * relationship between the defined hooks and the functions defined in this
 * class.
 *
 * @package    SPG_Projects
 * @subpackage SPG_Projects/public
 * @author     Mitch Negus <mitchell.negus.57@gmail.com>
 */
class Projects_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version           The version of this plugin.
	 * @param    array     $options           An array of the options set and added to the database by the plugin.
	 * @param    array     $project_meta       An array of the meta fields for the custom project post type.
	 */
	public function __construct( $plugin_name, $version, $project_meta ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->project_meta = $project_meta;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * 
	 * (Executed by loader class)
	 * 
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/projects-public.css',
			array(),
			$this->version,
			'all'
	 	);

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/projects-public.js',
			array('jquery'),
			$this->version,
			true
		);

	}

 	/**
	 * Register the custom post type for a project.
	 *
	 * Each project has an individual post that stores its information (title,
	 * description, logo, contact, references, etc.). This post is also accessed
	 * for display on the general projects page, where all projects are listed.
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function register_project_post_type() {

		$labels = array(
			'name' 					=> __( 'Projects' ),
			'singular_name' => __( 'Project' ),
			'add_new_item' 	=> __( 'Add New Project' ),
			'edit_item' 		=> __( 'Edit Project' ),
			'view_item'     => __( 'View Project' ),
			'view_items'    => __( 'View Projects' ),
			'search_items'  => __( 'Search Projects' )
		);

		$args = array(
			'labels' 			=> $labels,
			'public'			=> true,
			'has_archive' => true,
			'rewrite' 		=> array( 'slug' => 'projects' ),
			'supports' 		=> array( 'title', 'editor', 'thumbnail' ),
			'menu_icon' 	=> 'dashicons-clipboard'
		);

		register_post_type( 'projects', $args );
		flush_rewrite_rules();

	}

	/**
	 * Set the custom post archive template for the 'Projects' page.
	 * 
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    string     $archive_template     The path to the current archive post template that is being used by Wordpress.
	 * @return   string                           The path to the replacement archive post template to be used instead.
	 */
	public function use_project_archive_template( $archive_template ) {

		if ( is_post_type_archive( 'projects' ) ) {
			$archive_template = WSP_PATH . 'public/templates/archive-projects.php';
	 	}
		return $archive_template;

	}

}

