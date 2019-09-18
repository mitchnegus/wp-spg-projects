<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Projects
 * @subpackage SPG_Projects/admin
 */
namespace SPG_Projects;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for managing the admin area
 * (including enqueuing the admin-specific stylesheet and JavaScript). An
 * instance of this class should be passed to the run() function defined
 * in Projects_Loader as all of the hooks are actually defined in that
 * particular class. The Projects_Loader will then create the relationship
 * between the defined hooks and the functions defined in this class.
 *
 * @package    SPG_Projects
 * @subpackage SPG_Projects/admin
 * @author     Mitch Negus <mitchell.negus.57@gmail.com>
 */
class Projects_Admin {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version           The version of this plugin.
	 * @param      array     $options           An array of the options set and added to the database by the plugin.
	 * @param      array     $project_meta       An array of the meta fields for the custom project post type.
	 */
	public function __construct( $plugin_name, $version, $project_meta, $meta_titles ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->projects_custom_post_type = 'projects';
		$this->project_meta = $project_meta;
		$this->meta_titles = $meta_titles;
		// All functions prefixed with 'display_' come from `partials`
		require_once plugin_dir_path( __FILE__ ) . 'partials/projects-admin-display.php';
	}

	/**
	 * Add fields to the admin area corresponding to custom post metadata.
	 *
	 * Project information other than the project's title, logo, and description
	 * (e.g. project contact, references) are stored as post metadata. Input
	 * boxes for that metadata in the admin area are defined here.
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 */
	public function add_admin_fields() {

		add_meta_box(
			'project_info-meta',
			'Project Info',
			[$this, 'present_project_metabox'],
			$this->projects_custom_post_type,
			'normal',
			'low'
		);
		

	}

	/**
	 * Save project details to the database after an admin decides to update.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    int        $post_id             The ID of the custom project post.
	 */
	public function save_project_details( $post_id ) {

			// Only save meta data for project posts
			if ( get_post_type( $post_id ) == $this->projects_custom_post_type ) {

				foreach ( $this->project_meta as $meta ) {
					// Sanitize user input and update the post metadata
					$meta_key = $meta['meta_key'];
					$meta_value = sanitize_text_field($_POST[ $meta_key ]);
					// Make sure that a "Quick Edit" is not saving empty info
					if ( ! empty( $meta_value ) ) {
						update_post_meta( $post_id, $meta_key, $meta_value );
					}
				}

			}
	}

	/**
	 * Fill columns in the admin area with custom post information.
	 *
	 * In the admin area, an administrator can see a list of all projects
	 * currently listed on the site. This function populates columns in that
	 * list with relevant information about each project.
	 * (Executed by loader class)
	 */
	public function fill_project_columns( $column ) {

		$column1 = 'contact_name';
		$column2 = 'project_type';
		$custom = get_post_custom();
		switch ( $column ) {
			case $column1:
				echo $custom[ $column1 ][0];
				break;
			case $column2:
				echo $custom[ $column2 ][0];
				break;
		}

	}

	/**
	 * Show columns on the list of all projects in the admin area.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @return   array                            The columns to be displayed in the 'Projects' section of the admin area.
	 */
	public function set_project_columns() {

		$columns = array(
			'cb' 				       => '<input type="checkbox" />',
			'title' 		       => __( 'Project' ),
			'contact_name'     => __( $this->meta_titles['contact_name'] ),
			'project_type'     => __( $this->meta_titles['project_type'] )
		);
		return $columns;

	}

	/**
	 * Allow custom post columns in the admin area to be sortable.
	 *
	 * (Executed by loader class)
	 *
	 * @since    1.0.0
	 * @param    array                            The existing columns to be sorted in the 'Projects' section of the admin area.
	 * @return   array                            The columns to be sorted in the 'Projects' section of the admin area.
	 */
	public function set_project_sortable_columns( $columns ) {
		$columns['contact_name'] = 'contact_name';
		$columns['project_type'] = 'project_type';
		return $columns;
	}

	/**
	 * Define the ordering of the custom projects posts.
	 *
	 * (Executed by loader class)
	 *
	 * @param    WP_QUERY    $query 							The post query to sort by.
	 */
	public function project_posts_orderby( $query ) {
		
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->get( 'orderby' ) === 'contact_name' ) {
			$query->set( 'meta_key', 'contact_name' );
			$query->set( 'orderby', 'meta_value' );
		}
		if ( $query->get( 'orderby' ) === 'project_type' ) {
			$query->set( 'meta_key', 'project_type' );
			$query->set( 'orderby', 'meta_value' );
		}

	}

	/**
	 * Present a text input in an admin area metabox for managing project info.
	 *
	 * @since 1.0.0
	 * @param    WP_POST    $post                 The post associated with the current project.
	 */
	public function present_project_metabox( $post ) {

		$titles = $this->meta_titles;
		foreach ( $this->project_meta as $meta ) {
			// Get project meta parameters
			$meta_key = $meta['meta_key'];
			$custom = get_post_custom( $post->ID );
			$meta_value = $custom[ $meta_key ][0];
			// Show the selection interface
			display_label( $meta_key, $titles[ $meta_key ] );
			if ( $meta_key != 'project_type' ) {
				$required = $meta['required'];
				display_text_input( $meta_key, $meta_value, $required );
			} else {

				$defaults = array(
					'current' => '',
					'recurring' => '',
					'past'    => '',
					'oped'    => ''
				);
				switch ( $meta_value ) {
					case 'current':
						$defaults['current'] = 'checked';
						break;
					case 'recurring':
						$defaults['recurring'] = 'checked';
						break;
					case 'past':
						$defaults['past'] = 'checked';
						break;
					case 'oped':
						$defaults['oped'] = 'checked';
						break;
				}
				display_project_type_radio_button( $meta_key, $defaults );
			}

		}

	}

}
