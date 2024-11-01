<?php

/**
 * Plugin help functions.
 *
 * @link       https://www.fedegomez.es
 * @since      1.0.0
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 */

/**
 * Plugin help functions.
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 * @author     Fede Gómez <hola@fedegomez.es>
 */
class Super_Sitemap_For_Seo_Helper
{

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Get the amount of posts for a given term
     * 
     * @since   1.0.0
     * @access  private
     * @param   object  $term   The term object
     * @return  number          Amount of posts
     */
    private static function ss4seo_get_num_posts_from_term($term)
    {
        $data = self::ss4seo_get_items($term, -1);
        return count($data);
    }

    /**
     * Get the amount of pages that have a term to perform the sitemap pagination.
     * 
     * @since   1.0.0
     * @param   object  $term   The term object
     * @return  number          Amount of pages
     */
    public static function ss4seo_get_num_pages_for_term($term)
    {
        $max_entries = self::ss4seo_get_max_entries();
        $num_posts = self::ss4seo_get_num_posts_from_term($term);
        $num_pages = ceil($num_posts / $max_entries);
        return $num_pages;
    }

    /**
     * Get the custom taxonomies and save them in an option.
     * 
     * @since   1.0.0
     */
    public function ss4seo_get_taxonomies()
    {
        $args = [
            'public' => true,
            '_builtin' => false
        ];
        $taxonomies = get_taxonomies($args);
        update_option('ss4seo_custom_taxs', $taxonomies);
    }

    /**
     * Get the terms selected by the user to create your sitemap
     * 
     * @since   1.0.0
     * @return  array   $total_terms    Selected terms
     */
    public static function ss4seo_get_terms()
    {
        $all_categories_4_sitemap = Super_Sitemap_For_Seo_Carbon_Fields::crb_get_i18n_theme_option('all_categories_4_sitemap') ?: 'yes';
        $all_tags_4_sitemap = Super_Sitemap_For_Seo_Carbon_Fields::crb_get_i18n_theme_option('all_tags_4_sitemap') ?: 'yes';
        $custom_taxs = get_taxonomies(['public' => true, '_builtin' => false]);

        $category_terms = [];
        $post_tag_terms = [];
        $custom_tax_terms = [];

        $include = array();
        if ($all_categories_4_sitemap != 'no') {
            if ($all_categories_4_sitemap == 'selected') {
                array_walk_recursive(Super_Sitemap_For_Seo_Carbon_Fields::crb_get_i18n_theme_option('categories_4_sitemap'), function ($value, $key) use (&$include) {
                    if ($key == 'id') {
                        $include[] = (int) $value;
                    }
                }, $include);
            }

            $get_terms_args = array(
                'taxonomy' => 'category',
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC',
            );

            if (!empty($include)) {
                $get_terms_args['include'] = $include;
            }

            if (($all_categories_4_sitemap == 'selected' && !empty($include)) || $all_categories_4_sitemap == 'yes') {
                $query_terms = new WP_Term_Query($get_terms_args);
                $category_terms = $query_terms->get_terms();
            }
        }

        $include = array();
        if ($all_tags_4_sitemap != 'no') {
            if ($all_tags_4_sitemap == 'selected') {
                array_walk_recursive(Super_Sitemap_For_Seo_Carbon_Fields::crb_get_i18n_theme_option('tags_4_sitemap'), function ($value, $key) use (&$include) {
                    if ($key == 'id') {
                        $include[] = (int) $value;
                    }
                }, $include);
            }

            $get_terms_args = array(
                'taxonomy' => 'post_tag',
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC',
            );

            if (!empty($include)) {
                $get_terms_args['include'] = $include;
            }

            if (($all_tags_4_sitemap == 'selected' && !empty($include)) || $all_tags_4_sitemap == 'yes') {
                $query_terms = new WP_Term_Query($get_terms_args);
                $post_tag_terms = $query_terms->get_terms();
            }
        }

        foreach ($custom_taxs as $tax) {
            $all_custom_tax_4_sitemap = Super_Sitemap_For_Seo_Carbon_Fields::crb_get_i18n_theme_option("all_custom_tax_{$tax}_4_sitemap") ?: 'yes';
            $include = array();
            if ($all_custom_tax_4_sitemap != 'no') {
                if ($all_custom_tax_4_sitemap == 'selected') {
                    array_walk_recursive(Super_Sitemap_For_Seo_Carbon_Fields::crb_get_i18n_theme_option("custom_tax_{$tax}_4_sitemap"), function ($value, $key) use (&$include) {
                        if ($key == 'id') {
                            $include[] = (int) $value;
                        }
                    }, $include);
                }

                $get_terms_args = array(
                    'taxonomy' => $tax,
                    'hide_empty' => true,
                    'orderby' => 'name',
                    'order' => 'ASC',
                );

                if (!empty($include)) {
                    $get_terms_args['include'] = $include;
                }

                if (($all_custom_tax_4_sitemap == 'selected' && !empty($include)) || $all_custom_tax_4_sitemap == 'yes') {
                    $query_terms = new WP_Term_Query($get_terms_args);
                    $custom_tax_terms = array_merge($custom_tax_terms, $query_terms->get_terms());
                }
            }
        }

        $total_terms = array_merge($category_terms, $post_tag_terms, $custom_tax_terms);
        return $total_terms;
    }

    /**
     * Get the posts of the provided term for the corresponding page that are open to indexing.
     * 
     * @since   1.0.0
     * @param   object  $term   The term object
     * @param   number  $page   Current page
     * @return  array   $posts  Array of posts objects
     */
    public static function ss4seo_get_items($term, $page)
    {
        $max_entries = self::ss4seo_get_max_entries();
        $numberposts = $page == -1 ? $page : $max_entries;
        $offset = $page == -1 ? 0 : $max_entries * ($page - 1);
        $post_type = get_taxonomy($term->taxonomy)->object_type[0];

        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'offset' => $offset,
            'numberposts' => $numberposts,
            'tax_query' => array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                    'include_children' => false
                )
            ),
            'order' => 'ASC',
            'orderby' => 'title'
        );

        $noindex_global = false;
        $noindex_post_type = false;

        if (self::ss4seo_active_plugin_seo() == 'yoast') {
            $robots = get_option('wpseo_titles');
            if ($robots['noindex-' . $post_type] == 1) {
                $noindex_post_type = true;
            }
            if (!$noindex_post_type) {
                $meta_query = array(
                    'relation' => 'OR',
                    array(
                        'key' => '_yoast_wpseo_meta-robots-noindex',
                        'value' => '1',
                        'compare' => '!=',
                    ),
                    array(
                        'key' => '_yoast_wpseo_meta-robots-noindex',
                        'compare' => 'NOT EXISTS',
                    )
                );
            } else {
                $meta_query = array(
                    array(
                        'key' => '_yoast_wpseo_meta-robots-noindex',
                        'value' => '2',
                        'compare' => '=',
                    )
                );
            }
        } else {
            $robots = get_option('rank-math-options-titles');
            if (in_array('noindex', $robots['robots_global'])) {
                $noindex_global = true;
            }
            if (in_array('noindex', $robots['pt_' . $post_type . '_robots'])) {
                $noindex_post_type = true;
            }

            if (!$noindex_global && !$noindex_post_type) {
                $meta_query = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'rank_math_robots',
                        'value' => 'noindex',
                        'compare' => 'NOT LIKE',
                    ),
                    array(
                        'key' => 'rank_math_robots',
                        'compare' => 'NOT EXISTS',
                    )
                );
            } else {
                $meta_query = array(
                    'relation' => 'AND',
                    array(
                        'key' => 'rank_math_robots',
                        'value' => 'index',
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key' => 'rank_math_robots',
                        'value' => 'noindex',
                        'compare' => 'NOT LIKE',
                    )
                );
            }
        }

        $args['meta_query'] = $meta_query;

        $posts = get_posts($args);
        return $posts;
    }

    /**
     * Checks that the plugin requirements are met for proper operation
     * 
     * @since   1.0.0
     */
    public function ss4seo_check_requeriments()
    {
        $error = false;
        $active_plugin_seo = self::ss4seo_active_plugin_seo();

        if ($active_plugin_seo == 'yoast') {
            $yoast_settings = get_option('wpseo');
            if ($yoast_settings && !$yoast_settings['enable_xml_sitemap']) {
                $error_description = __('Super Sitemap for SEO requires the sitemap option of Yoast SEO (or Premium) to be enabled to generate the extra sitemaps.', $this->plugin_name);
                $error = true;
            }
        } elseif ($active_plugin_seo == 'rank math') {
            $rank_math_modules = get_option('rank_math_modules');
            if ($rank_math_modules && !in_array('sitemap', $rank_math_modules)) {
                $error_description = __('Super Sitemap for SEO requires the sitemap option of Rank Math SEO (or Pro) to be enabled to generate the extra sitemaps.', $this->plugin_name);
                $error = true;
            }
        }

        if ($active_plugin_seo == '') {
            $error_description = __('Super Sitemap for SEO requires Yoast SEO (or Premium) or Rank Math SEO (or Pro) to be installed and active to generate the extra sitemaps.', $this->plugin_name);
            $error = true;
        }

        if ($error) {
            $class = 'notice notice-error is-dismissible';
            $message = __($error_description, $this->plugin_name);
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        }
    }

    /**
     * Get the maximum number of entries to display in a sitemap
     * 
     * @since   1.0.0
     * @access  private
     * @return  number  $entries    Max. number of entries
     */
    private static function ss4seo_get_max_entries()
    {
        $active_plugin_seo = self::ss4seo_active_plugin_seo();
        if ($active_plugin_seo == 'yoast') {
            $entries = (int) apply_filters('wpseo_sitemap_entries_per_page', 1000);
            return $entries;
        } elseif ($active_plugin_seo == 'rank math') {
            $rank_math_options_sitemap = get_option('rank-math-options-sitemap');
            $entries = $rank_math_options_sitemap['items_per_page'];
            return $entries;
        }
    }

    /**
     * Whether to include images in the sitemap or not
     * 
     * @since   1.0.0
     * @return  boolean
     */
    public static function ss4seo_include_images()
    {
        $active_plugin_seo = self::ss4seo_active_plugin_seo();
        if ($active_plugin_seo == 'yoast') {
            $include_images = apply_filters('wpseo_xml_sitemap_img', true);
            return $include_images;
        } elseif ($active_plugin_seo == 'rank math') {
            $rank_math_options_sitemap = get_option('rank-math-options-sitemap');
            $include_images = $rank_math_options_sitemap['include_images'] == 'on' ? true : false;
            return $include_images;
        }
    }

    /**
     * Return the active SEO plugin
     * 
     * @since   1.0.0
     * @return  string  $plugin_active  Name of active SEO plugin
     */
    public static function ss4seo_active_plugin_seo()
    {
        $plugin_active = '';
        if (self::ss4seo_is_plugin_active('wordpress-seo/wp-seo.php') || self::ss4seo_is_plugin_active('wordpress-seo-premium/wp-seo-premium.php')) {
            return $plugin_active = 'yoast';
        }

        if (self::ss4seo_is_plugin_active('seo-by-rank-math/rank-math.php') || self::ss4seo_is_plugin_active('seo-by-rank-math-pro/rank-math-pro.php')) {
            $plugin_active = 'rank math';
        }

        return $plugin_active;
    }

    /**
     * Register custom provider for Rank Math
     * 
     * @since   1.0.0
     * @param   array   $external_providers Array of external providers
     * @return  array   $external_providers Array of external providers
     */
    public function ss4seo_rank_math_provider($external_providers)
    {
        if (self::ss4seo_active_plugin_seo() == 'rank math') {
            $external_providers['custom_taxonomies'] = new \RankMath\Sitemap\Providers\Super_Sitemap_For_Seo_Rank_Math();
            return $external_providers;
        }
    }

    /**
     * Adds the URL of the new sitemaps to the sitemap index
     * 
     * @since	1.0.0
     * @param	string	$sitemap_index	The string containing the URLs of the sitemaps
     * @return	string	$sitemap_index	The string containing the URLs of the sitemaps
     */
    public function ss4seo_add_terms_sitemap_index($sitemap_index)
    {
        $terms = self::ss4seo_get_terms();
        foreach ($terms as $term) {
            if (defined('ICL_LANGUAGE_CODE')) {
                $term_language_code = apply_filters('wpml_element_language_code', null, array('element_id' => (int)$term->term_taxonomy_id, 'element_type' => $term->taxonomy));
                if (ICL_LANGUAGE_CODE != $term_language_code) {
                    continue;
                }
            }

            $num_pages = Super_Sitemap_For_Seo_Helper::ss4seo_get_num_pages_for_term($term);
            for ($page = 1; $page <= $num_pages; $page++) {
                $page_number = $num_pages < 2 ? '' : $page;
                $sitemap_url = home_url("{$term->taxonomy}-{$term->slug}-sitemap{$page_number}.xml");
                $sitemap_date = date(DATE_W3C);
                $custom_sitemap = '
				<sitemap>
					<loc>%s</loc>
					<lastmod>%s</lastmod>
				</sitemap>';
                $sitemap_index .= sprintf($custom_sitemap, $sitemap_url, $sitemap_date);
            }
        }

        return $sitemap_index;
    }

    /**
     * The callback function that generates the sitemap particular to each taxonomy
     * 
     * @since	1.0.0
     */
    public static function ss4seo_sitemap_generate()
    {
        global $wp;
        global $wpseo_sitemaps;

        preg_match('/(.*)-(.*)-sitemap(\d*).xml/U', $wp->request, $matches);
        $taxonomy = $matches[1];
        $slug = $matches[2];
        $page = $matches[3] == '' ? -1 : $matches[3];

        $term = get_term_by('slug', $slug, $taxonomy);

        $images = [];

        $include_images = self::ss4seo_include_images();

        if ($term) {
            $data = self::ss4seo_get_items($term, $page);
            $urls = array();
            $active_plugin_seo = self::ss4seo_active_plugin_seo();
            foreach ($data as $item) {
                $images = array();

                if ($include_images) {
                    $images = array();
                    if ($active_plugin_seo == 'yoast') {
                        $image_parser = new WPSEO_Sitemap_Image_Parser();
                    } elseif ($active_plugin_seo == 'rank math') {
                        $image_parser = new RankMath\Sitemap\Image_Parser();
                    }
                    $images = $image_parser->get_images($item);
                }

                $url = array(
                    "mod" => $item->post_modified,
                    "loc" => get_permalink($item->ID),
                );

                if ($include_images) {
                    $url["images"] = $images;
                }

                if ($active_plugin_seo == 'yoast') {
                    $urls[] = $wpseo_sitemaps->renderer->sitemap_url($url);
                } elseif ($active_plugin_seo == 'rank math') {
                    $urls[] = $url;
                }
            }

            if ($active_plugin_seo == 'yoast') {
                $sitemap_body = '
            <urlset
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd"
                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                %s
            </urlset>';
                $sitemap = sprintf($sitemap_body, implode("\n", $urls));
                $wpseo_sitemaps->set_sitemap($sitemap);
            } elseif ($active_plugin_seo == 'rank math') {
                return $urls;
            }
        }
    }

    /**
     * Checks if a plugin is active
     * 
     * @since   1.0.0
     * @access  private
     * @param   string  $plugin Plugin to check
     * @return  boolean
     */
    private static function ss4seo_is_plugin_active($plugin)
    {
        return in_array($plugin, (array) get_option('active_plugins', array()), true) || self::ss4seo_is_plugin_active_for_network($plugin);
    }

    /**
     * Checks if a plugin is active for network
     * 
     * @since   1.0.0
     * @access  private
     * @param   string  $plugin Plugin to check
     * @return  boolean
     */
    private static function ss4seo_is_plugin_active_for_network($plugin)
    {
        if (!is_multisite()) {
            return false;
        }

        $plugins = get_site_option('active_sitewide_plugins');
        if (isset($plugins[$plugin])) {
            return true;
        }

        return false;
    }

    /**
     * Disable sitemap caching in Rank Math
     * 
     * @since   1.0.0
     */
    public function ss4seo_stop_rank_math_caching()
    {
        return false;
    }

    /**
     * Add a link to the plugin settings
     * 
     * @since   1.0.0
     * @param   array   $links  Array of links
     * @return  array   $links  Array of links
     */
    public function ss4seo_add_plugin_page_settings_link($links)
    {
        $settings = array(
            'settings' => '<a href="' .
                admin_url('admin.php?page=' . $this->plugin_name) .
                '">' . __('Settings', $this->plugin_name) . '</a>'
        );
        return array_merge($settings, $links);
    }
}
