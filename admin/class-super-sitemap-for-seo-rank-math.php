<?php

/**
 * The admin-specific functionality of the plugin for Rank Math.
 *
 * @link       https://www.fedegomez.es
 * @since      1.0.0
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 */

/**
 * The admin-specific functionality of the plugin for Rank Math.
 *
 * Defines the plugin name and version.
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 * @author     Fede Gómez <hola@fedegomez.es>
 */

namespace RankMath\Sitemap\Providers;

defined('ABSPATH') || exit;

if (\Super_Sitemap_For_Seo_Helper::ss4seo_active_plugin_seo() == 'rank math') {
	class Super_Sitemap_For_Seo_Rank_Math implements Provider
	{
		public function handles_type($type)
		{
			return true;
		}

		public function get_index_links($max_entries)
		{
			return [];
		}

		public function get_sitemap_links($type, $max_entries, $current_page)
		{
			return \Super_Sitemap_For_Seo_Helper::ss4seo_sitemap_generate();
		}
	}
}
