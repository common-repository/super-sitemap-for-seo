<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.fedegomez.es
 * @since      1.0.0
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/includes
 * @author     Fede Gómez <hola@fedegomez.es>
 */
class Super_Sitemap_For_Seo_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		flush_rewrite_rules();
	}
}
