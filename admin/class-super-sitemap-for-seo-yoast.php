<?php

/**
 * The admin-specific functionality of the plugin for Yoast.
 *
 * @link       https://www.fedegomez.es
 * @since      1.0.0
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 */

/**
 * The admin-specific functionality of the plugin for Yoast.
 *
 * Defines the plugin name and version.
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 * @author     Fede Gómez <hola@fedegomez.es>
 */
class Super_Sitemap_For_Seo_Yoast
{
	/**
	 * Register extra sitemaps and their callback function
	 * @since	1.0.0
	 */
	public function ss4seo_sitemap_register()
	{
		if (Super_Sitemap_For_Seo_Helper::ss4seo_active_plugin_seo() == 'yoast') {
			global $wpseo_sitemaps;
			if (isset($wpseo_sitemaps) && !empty($wpseo_sitemaps)) {
				$terms = Super_Sitemap_For_Seo_Helper::ss4seo_get_terms();
				foreach ($terms as $term) {
					$wpseo_sitemaps->register_sitemap($term->taxonomy . '-' . $term->slug, "Super_Sitemap_For_Seo_Helper::ss4seo_sitemap_generate");
				}
			}
		}
	}
}
