<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fedegomez.es
 * @since      1.0.0
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/includes
 */

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
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/includes
 * @author     Fede Gómez <hola@fedegomez.es>
 */
class Super_Sitemap_For_Seo
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Super_Sitemap_For_Seo_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('SUPER_SITEMAP_FOR_SEO_VERSION')) {
			$this->version = SUPER_SITEMAP_FOR_SEO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'super-sitemap-for-seo';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Super_Sitemap_For_Seo_Loader. Orchestrates the hooks of the plugin.
	 * - Super_Sitemap_For_Seo_i18n. Defines internationalization functionality.
	 * - Super_Sitemap_For_Seo_Admin. Defines all hooks for the admin area.
	 * - Super_Sitemap_For_Seo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-super-sitemap-for-seo-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-super-sitemap-for-seo-i18n.php';

		/**
		 * The class responsible for defining helper functions.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-super-sitemap-for-seo-helper.php';

		/**
		 * The class responsible for create custom sitemaps in Rank Math
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-super-sitemap-for-seo-rank-math.php';

		/**
		 * The class responsible for create custom sitemaps in Yoast
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-super-sitemap-for-seo-yoast.php';

		/**
		 * The class responsible for loading carbon fields.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-super-sitemap-for-seo-carbon-fields.php';

		$this->loader = new Super_Sitemap_For_Seo_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Super_Sitemap_For_Seo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Super_Sitemap_For_Seo_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_helper = new Super_Sitemap_For_Seo_Helper($this->get_plugin_name(), $this->get_version());
		$plugin_yoast = new Super_Sitemap_For_Seo_Yoast();
		$plugin_carbon_fields = new Super_Sitemap_For_Seo_Carbon_Fields($this->get_plugin_name(), $this->get_version());

		//Get the custom taxonomies and save them in an option
		$this->loader->add_action('wp_loaded', $plugin_helper, 'ss4seo_get_taxonomies');
		//Checks that the plugin requirements are met for proper operation
		$this->loader->add_action('admin_notices', $plugin_helper, 'ss4seo_check_requeriments');
		$plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
		$this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_helper, 'ss4seo_add_plugin_page_settings_link');

		//Load the Carbon Fields library
		$this->loader->add_action('after_setup_theme', $plugin_carbon_fields, 'ss4seo_crb_load');
		//Register the options page and fields of the plugin
		$this->loader->add_action('carbon_fields_register_fields', $plugin_carbon_fields, 'ss4seo_crb_attach_theme_options');
		// Flush rewrite rules when saving options
		$this->loader->add_action('carbon_fields_theme_options_container_saved', $plugin_carbon_fields, 'ss4seo_flush_rewrite_rules');

		if ($plugin_helper->ss4seo_active_plugin_seo() == 'yoast') {
			//Register extra sitemaps
			$this->loader->add_action('init', $plugin_yoast, 'ss4seo_sitemap_register');
			//Adds the URL of the new sitemaps to the sitemap index
			$this->loader->add_filter('wpseo_sitemap_index', $plugin_helper, 'ss4seo_add_terms_sitemap_index');
		}

		if ($plugin_helper->ss4seo_active_plugin_seo() == 'rank math') {
			//Register extra sitemaps in Rank Math
			$this->loader->add_filter('rank_math/sitemap/index', $plugin_helper, 'ss4seo_add_terms_sitemap_index', 11);
			//Register custom provider for extra sitemaps in Rank Math
			$this->loader->add_filter('rank_math/sitemap/providers', $plugin_helper, 'ss4seo_rank_math_provider');
			//Stop Rank Math caching sitemap
			$this->loader->add_filter('rank_math/sitemap/enable_caching', $plugin_helper, 'ss4seo_stop_rank_math_caching');
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Super_Sitemap_For_Seo_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
