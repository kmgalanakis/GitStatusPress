<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/kmgalanakis
 * @since      1.0.0
 *
 * @package    Gitstatuspress
 * @subpackage Gitstatuspress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gitstatuspress
 * @subpackage Gitstatuspress/admin
 * @author     Konstantinos Galanakis <kmgalanakis@gmail.com>
 */
class Gitstatuspress_Admin {

    const PARENT_NODE_ID = 'gitstatuspress';
    const TEXT_DOMAIN = 'gitstatuspress';

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
     * The git helper.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The instance of the git helper.
     */
    private $githelper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->githelper = new Gitstatuspress_Helper();

        add_action( 'admin_bar_menu', array( $this, 'add_gitstatuspress_to_toolbar' ), 999 );
	}

    /**
     * Create the admin bar node.
     *
     * @since    1.0.0
     */
    function add_gitstatuspress_to_toolbar( $wp_admin_bar ) {

	    $plugins_git = array();

        if ( is_admin() ) {
	        $plugins_git = self::get_plugins_git();
	        update_option( 'gitstatuspress_plugins_git', serialize( $plugins_git ), '', 'yes' );
        }
        else {
	        $plugins_git = unserialize( get_option( 'gitstatuspress_plugins_git' ) );
        }

        // Add the main node in the Admin Bar
        $args = array(
            'id' => self::PARENT_NODE_ID,
            'title' => '<span class="ab-icon"></span>' . $this->get_gitstatuspress_string(),
            'meta'   => array( 'class' => 'gsp-parent-node' )
        );
        $wp_admin_bar->add_node($args);

        if (count($plugins_git)) {
            ksort($plugins_git);

            foreach ($plugins_git as $plugin_name => $plugin_info) {

                $title =    '<span class="gsp-status-icon gsp-status-icon-' . $plugin_info['status'] . '" title="' . __( ucfirst($plugin_info['status'] ), self::TEXT_DOMAIN ) . '"></span>' .
                            '<span class="gsp-plugin-title" title="' . $plugin_info['path'] . '">' . $plugin_name . ' ('. $plugin_info['version'] . ') ' . $plugin_info['branch_info'] . '</span>';

                // add a child item to our parent item
                $args = array(
                    'id' => $plugin_name,
                    'title' => $title,
                    'parent' => 'gitstatuspress',
                );
                $wp_admin_bar->add_node($args);
            }
        }
    }

    /**
     * Retrieve the list of plugins that use git and create the records for the toolbar bar.
     *
     * @since    1.0.0
     */

    private function get_plugins_git() {
        $plugins_dir = dirname( dirname( dirname(__FILE__) . '..' . DIRECTORY_SEPARATOR ) );
        $plugins_git = array();

        $plugins = scandir($plugins_dir);
        foreach ($plugins as $plugin_dir) {

            $rel_plugin_dir = $plugin_dir;
            $plugin_dir = $plugins_dir . '/' . $plugin_dir;
            if (!is_dir($plugin_dir) || in_array($plugin_dir, array('.', '..'))) continue;


            $git_dir = $plugin_dir . '/' . '.git';

            if (file_exists($git_dir)) {
            	$files = scandir($plugin_dir);
                foreach ($files as $file) {

                    if (!stristr($file, '.php')) {
                        continue;
                    }

                    $file_path = $plugin_dir . '/' . $file;
                    $rel_file_path = $rel_plugin_dir . '/' . $file;
                    $plugin_info = get_plugin_data($file_path);

                    if (strlen($plugin_info['Name'])) {
                        break;
                    }
                }

                $stringfromfile = file($git_dir . '/HEAD', FILE_USE_INCLUDE_PATH);

                $plugin_path = dirname($file_path);

                $plugins_git[$plugin_info['Name']] = array(
                    'version' => $plugin_info['Version'],
                    'git' => $stringfromfile[0],
                    'path' => $plugin_path,
                    'status' => is_plugin_active( $rel_file_path ) ? 'active' : 'inactive',
                    'branch_info' => $this->githelper->get_branch_info($plugin_path)
                );
            }
        }

        return $plugins_git;
    }

    /**
     * Get the toolbar parent node name.
     *
     * @since    1.0.0
     */

    private function get_gitstatuspress_string() {
        return __( 'GitStatusPress', self::TEXT_DOMAIN );
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gitstatuspress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gitstatuspress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gitstatuspress-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gitstatuspress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gitstatuspress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gitstatuspress-admin.js', array( 'jquery' ), $this->version, false );

	}





}
