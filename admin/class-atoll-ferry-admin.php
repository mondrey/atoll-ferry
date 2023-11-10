<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Atoll_Ferry
 * @subpackage Atoll_Ferry/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Atoll_Ferry
 * @subpackage Atoll_Ferry/admin
 * @author     Your Name <email@example.com>
 */
class Atoll_Ferry_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $atoll_ferry    The ID of this plugin.
	 */
	private $atoll_ferry;

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
	 * @param      string    $atoll_ferry       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $atoll_ferry, $version ) {

		$this->atoll_ferry = $atoll_ferry;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Atoll_Ferry_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Atoll_Ferry_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->atoll_ferry, plugin_dir_url( __FILE__ ) . 'css/atoll-ferry-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Atoll_Ferry_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Atoll_Ferry_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->atoll_ferry, plugin_dir_url( __FILE__ ) . 'js/atoll-ferry-admin.js', array( 'jquery' ), $this->version, false );

	}

}
