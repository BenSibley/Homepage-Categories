<?php

class Homepage_Categories_Class {

	/**
	 * Instance of this class.
	 *
	 * @since    0.1
	 *
	 * @var      object
	 */
	private static $instance = null;

	/**
	 * Initialize main plugin functions and add appropriate hooks/filter calls
	 *
	 * @since 0.1
	 *
	 */
	private function __construct() {

		// Setup constants.
		$this->setup_constants();

		// Load plugin text domain
		add_action( 'plugins_loaded', array( $this, 'plugin_textdomain' ) );

		// Add settings page
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

		// Add settings section, settings, and register settings
		add_action( 'admin_init', array( $this, 'settings_api_init' ) );

		// Remove the categories on the front-end
		add_action( 'pre_get_posts', array( $this, 'remove_categories' ), 99 );

		// Enqueue the CSS & JS files needed
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add the "Settings" link to the Plugins page
		add_filter('plugin_action_links_homepage-categories/homepage-categories.php', array( $this, 'settings_link' ) );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @since     0.1
	 */
	private function setup_constants() {

		// main file
		if ( ! defined( 'HOME_CAT_FILE' ) ) {
			define( 'HOME_CAT_FILE', __FILE__ );
		}

		// plugin directory
		if ( ! defined( 'HOME_CAT_PATH' ) ) {
			define( 'HOME_CAT_PATH', plugin_dir_path( HOME_CAT_FILE ) );
		}

		// url
		if ( ! defined( 'HOME_CAT_URL' ) ) {
			define( 'HOME_CAT_URL', plugin_dir_url( __FILE__ ) );
		}

		// basename
		if ( ! defined( 'HOME_CAT_BASENAME' ) ) {
			define( 'HOME_CAT_BASENAME', plugin_basename( HOME_CAT_FILE ) );
		}
	}

	/**
	 * Load plugin text domain
	 *
	 * @since     0.1
	 */
	public function plugin_textdomain() {
		load_plugin_textdomain(
			'homepage-categories',
			false,
			dirname( plugin_basename( HOME_CAT_FILE ) ) . '/languages'
		);
	}

	/**
	 * Add Settings menu page
	 *
	 * @since     0.1
	 */
	public function add_settings_page() {
		add_options_page(
			__('Homepage Categories', 'homepage-categories'),
			__('Home Categories', 'homepage-categories'),
			'manage_options',
			'homepage-categories',
			array( $this, 'settings_page_content')
		);
	}

	/**
	 * Add content to Settings menu page
	 *
	 * @since     0.1
	 */

	public function settings_page_content() {

		// Only allow admins to access
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		<div id="homepage-categories-wrap" class="wrap homepage-categories-wrap">
			<h2><?php _e('Homepage Categories', 'homepage-categories' ); ?></h2>
			<form action="options.php" method="POST">
				<?php settings_fields( 'homepage-categories-group' ); ?>
				<?php do_settings_sections( 'homepage-categories' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Settings API. Add settings sections, fields, and register them.
	 *
	 * @since     0.1
	 */
	public function settings_api_init() {

		// Add the section
		add_settings_section(
			'home_cat_main_section_id',
			__('Remove Categories from Your Homepage', 'homepage-categories'),
			array( $this, 'main_setting_section_callback' ),
			'homepage-categories'
		);

		// Register one setting for all fields (array)
		register_setting( 'homepage-categories-group', 'homepage-categories' );

		// Get the site's cateogires
		$categories = get_categories();

		// Add the Category Name / Select All field
		add_settings_field(
			'select_all',
			__('Category Name', 'homepage-categories'),
			array( $this, 'settings_fields_content' ),
			'homepage-categories',
			'home_cat_main_section_id',
			array(
				'context' => 'select_all'
			)
		);

		// Add a setting for each category
		foreach ( $categories as $category ) {

			add_settings_field(
				$category->cat_ID,
				$category->name,
				array( $this, 'settings_fields_content' ),
				'homepage-categories',
				'home_cat_main_section_id',
				array(
					'context' => $category->cat_ID
				)
			);
		}
	}

	/**
	 * Add content for main section
	 *
	 * @since     0.1
	 */
	public function main_setting_section_callback() {
		echo '<p>' . __("Check a box to remove the category from your homepage.", "homepage-categories") . '</p>';
	}

	/**
	 * Add settings fields
	 *
	 * @since     0.1
	 */
	public function settings_fields_content($args) {

		// Get all categories
		$categories = get_categories();

		// Get the plugin's saved option
		$options = (array) get_option( 'homepage-categories' );

		// If current setting calling this function is the select/all setting...
		if ( 'select_all' === $args[ 'context' ] ) {

			// Set it to 0 (unchecked) if not saved
			if ( ! isset( $options['select_all'] ) ) $options['select_all'] = 0;

			// Output checkbox
			echo '<input name="homepage-categories[select_all]" id="select_all" type="checkbox" value="1" class="code" ' . checked( 1, $options['select_all'], false ) . ' />';

			// Close td and open a new one in the same tr for the post count
			echo '</td><td><span id="post-count" class="post-count">Posts</span></td>';
		}

		// For each of the site's categories
		foreach ( $categories as $category ) {

			// Limit each category to being called once since this function is called for each category
			if ( $category->cat_ID === $args[ 'context' ] ) {

					// Set it to 0 (unchecked) if not saved
				if ( !isset( $options[$category->cat_ID] ) ) $options[$category->cat_ID] = 0;

				// Output checkbox
				echo '<input name="homepage-categories[' . $category->cat_ID . ']" id="' . $options[$category->cat_ID] . '" type="checkbox" value="1" class="code" ' . checked( 1, $options[$category->cat_ID], false ) . ' />';

				// Output post count in same row
				echo '</td><td><span>' . $category->count . '</span></td>';
			}
		}
	}

	/**
	 * Remove categories from the homepage
	 *
	 * @since     0.1
	 */
	public function remove_categories( $query ) {

		// If is the main loop on a blog page
		if ( $query->is_home() && $query->is_main_query() ) {

			// Get the plugin's saved option
			$categories = get_option( 'homepage-categories' );

			// If an array is saved
			if ( is_array( $categories ) ) {

				// Initiate string for ID integers
				$removal_string = '';
				// Get the last ID
				$last_id        = end( $categories );
				// Get the key of the last ID
				$last_id        = key( $categories );

				// For each ID saved..
				foreach ( $categories as $id => $value  ) {
					// Add the id to list of categories to remove (e.g. -3)
					$removal_string .= '-' . $id;
					// If it's not the last ID in the array
					if ( $id != $last_id ) {
						// Add a trailing comma (e.g. -3,)
						$removal_string .= ',';
					}
				}
				// Update the category with the categories to remove
				$query->set( 'cat', $removal_string );
			}
		}
	}

	/**
	 * Registers and enqueues CSS & JS files
	 *
	 * @since     0.1
	 *
	 */
	public function enqueue_scripts() {

		// register normal or RTL stylesheet
		if ( is_rtl() ) {
			// Register main stylesheet
			wp_register_style( 'homepage-categories-style', HOME_CAT_URL . 'css/rtl.min.css' );
		} else {
			// Register main stylesheet
			wp_register_style( 'homepage-categories-style', HOME_CAT_URL . 'css/style.min.css' );
		}

		// Enqueue main stylesheet
		wp_enqueue_style( 'homepage-categories-style' );

		// Register main script
		wp_register_script( 'homepage-categories-script', HOME_CAT_URL . 'js/functions.min.js', array('jquery'),'', true );
		// Enqueue main script
		wp_enqueue_script( 'homepage-categories-script' );
	}

	/**
	 * Adds "Settings" link on Plugins page
	 *
	 * @since     0.1
	 *
	 */
	public function settings_link($links) {

		// set url
		$link = admin_url('options-general.php?page=homepage-categories.php');

		// set link markup
		$settings_link = '<a href="' . esc_url( $link ) . '">Settings</a>';

		// add settings link to plugin's links
		array_unshift($links, $settings_link);

		// return the links
		return $links;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
