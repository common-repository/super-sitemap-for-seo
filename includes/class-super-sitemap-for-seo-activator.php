<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.fedegomez.es
 * @since      1.0.0
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/includes
 * @author     Fede Gómez <hola@fedegomez.es>
 */
class Super_Sitemap_For_Seo_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		flush_rewrite_rules();
	}
}
