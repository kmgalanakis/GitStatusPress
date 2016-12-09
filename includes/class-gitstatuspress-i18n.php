<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/kmgalanakis
 * @since      1.0.0
 *
 * @package    Gitstatuspress
 * @subpackage Gitstatuspress/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gitstatuspress
 * @subpackage Gitstatuspress/includes
 * @author     Konstantinos Galanakis <kmgalanakis@gmail.com>
 */
class Gitstatuspress_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gitstatuspress',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
