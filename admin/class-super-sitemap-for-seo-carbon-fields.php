<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Load Carbon Fields library
 *
 * @link       https://www.fedegomez.es
 * @since      1.0.0
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 */

/**
 * Load Carbon Fields library
 *
 * Defines the plugin name and version.
 *
 * @package    Super_Sitemap_For_Seo
 * @subpackage Super_Sitemap_For_Seo/admin
 * @author     Fede Gómez <hola@fedegomez.es>
 */
class Super_Sitemap_For_Seo_Carbon_Fields
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
     * Load the library
     * 
     * @since   1.0.0
     */
    public function ss4seo_crb_load()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/autoload.php';
        \Carbon_Fields\Carbon_Fields::boot();
    }

    /**
     * Creates the plugin options page and its fields
     * 
     * @since   1.0.0
     */
    public function ss4seo_crb_attach_theme_options()
    {
        $custom_taxs = get_option('ss4seo_custom_taxs');
        $set_parent = Super_Sitemap_For_Seo_Helper::ss4seo_active_plugin_seo() == 'yoast' ? 'wpseo_dashboard' : 'rank-math';
        $fields = [];
        $fields[] = Field::make('html', 'ss4seo_donation')
            ->set_html('<h3 style="text-align: center">' . __('Support my work', $this->plugin_name) . '</h3><p style="text-align: center;font-weight: bold;font-size: 1.1em">' . __("Ideas to develop free tools like this plugin occur to me when I'm enjoying a good coffee.", $this->plugin_name) . '</p><p style="text-align: center"><style>.bmc-button img{width: 27px !important;margin-bottom: 1px !important;box-shadow: none !important;border: none !important;vertical-align: middle !important;}.bmc-button{line-height: 36px !important;height:37px !important;text-decoration: none !important;display:inline-flex !important;color:#ffffff !important;background-color:#FF813F !important;border-radius: 3px !important;border: 1px solid transparent !important;padding: 0px 9px !important;font-size: 17px !important;letter-spacing:-0.08px !important;box-shadow: 0px 1px 2px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5) !important;margin: 0 auto !important;font-family:\'Lato\', sans-serif !important;-webkit-box-sizing: border-box !important;box-sizing: border-box !important;-o-transition: 0.3s all linear !important;-webkit-transition: 0.3s all linear !important;-moz-transition: 0.3s all linear !important;-ms-transition: 0.3s all linear !important;transition: 0.3s all linear !important;}.bmc-button:hover, .bmc-button:active, .bmc-button:focus {-webkit-box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5) !important;text-decoration: none !important;box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5) !important;opacity: 0.85 !important;color:#ffffff !important;}</style><link href="https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext" rel="stylesheet"><a class="bmc-button" target="_blank" href="https://www.buymeacoffee.com/fedegomez"><img src="https://bmc-cdn.nyc3.digitaloceanspaces.com/BMC-button-images/BMC-btn-logo.svg" alt="' . __('Buy me a coffee', $this->plugin_name) . '"><span style="margin-left:5px">' . __('Buy me a coffee', $this->plugin_name) . '</span></a></p>');
        $fields[] = Field::make('select', 'all_categories_4_sitemap' . self::crb_get_i18n_suffix(), __('Create sitemap for categories?', $this->plugin_name))
            ->add_options(array(
                'yes' => __('Yes', $this->plugin_name),
                'no' => __('No', $this->plugin_name),
                'selected' => __('Only for selected terms', $this->plugin_name),
            ));
        $fields[] = Field::make('association', 'categories_4_sitemap' . self::crb_get_i18n_suffix(), __('Select the categories you want to create a sitemap for', $this->plugin_name))
            ->set_conditional_logic(array(
                'relation' => 'AND',
                array(
                    'field' => 'all_categories_4_sitemap' . self::crb_get_i18n_suffix(),
                    'value' => 'selected',
                    'compare' => '=',
                )
            ))
            ->set_types(array(
                array(
                    'type'      => 'term',
                    'taxonomy' => 'category',
                )
            ));
        $fields[] = Field::make('select', 'all_tags_4_sitemap' . self::crb_get_i18n_suffix(), __('Create sitemap for tags?', $this->plugin_name))
            ->add_options(array(
                'yes' => __('Yes', $this->plugin_name),
                'no' => __('No', $this->plugin_name),
                'selected' => __('Only for selected terms', $this->plugin_name),
            ));
        $fields[] = Field::make('association', 'tags_4_sitemap' . self::crb_get_i18n_suffix(), __('Select the tags you want to create a sitemap for', $this->plugin_name))
            ->set_conditional_logic(array(
                'relation' => 'AND',
                array(
                    'field' => 'all_tags_4_sitemap' . self::crb_get_i18n_suffix(),
                    'value' => 'selected',
                    'compare' => '=',
                )
            ))
            ->set_types(array(
                array(
                    'type'      => 'term',
                    'taxonomy' => 'post_tag',
                )
            ));
        if ($custom_taxs) {
            foreach ($custom_taxs as $tax) {
                $fields[] = Field::make('select', "all_custom_tax_{$tax}_4_sitemap" . self::crb_get_i18n_suffix(), sprintf(__('Create sitemap for %s terms?', $this->plugin_name), $tax))
                    ->add_options(array(
                        'yes' => __('Yes', $this->plugin_name),
                        'no' => __('No', $this->plugin_name),
                        'selected' => __('Only for selected terms', $this->plugin_name),
                    ));
                $fields[] = Field::make('association', "custom_tax_{$tax}_4_sitemap" . self::crb_get_i18n_suffix(), __('Select the custom terms you want to create a sitemap for', $this->plugin_name))
                    ->set_conditional_logic(array(
                        'relation' => 'AND',
                        array(
                            'field' => "all_custom_tax_{$tax}_4_sitemap" . self::crb_get_i18n_suffix(),
                            'value' => 'selected',
                            'compare' => '=',
                        )
                    ))
                    ->set_types(array(
                        array(
                            'type'      => 'term',
                            'taxonomy' => $tax,
                        )
                    ));
            }
        }
        Container::make('theme_options', __('Super Sitemap for SEO', $this->plugin_name))
            ->set_page_file($this->plugin_name)
            ->set_page_parent($set_parent)
            ->add_fields($fields);
    }

    public static function crb_get_i18n_suffix()
    {
        $suffix = '';
        if (!defined('ICL_LANGUAGE_CODE')) {
            return $suffix;
        }
        $suffix = '_' . ICL_LANGUAGE_CODE;
        return $suffix;
    }

    public static function crb_get_i18n_theme_option($option_name)
    {
        $suffix = self::crb_get_i18n_suffix();
        return carbon_get_theme_option($option_name . $suffix);
    }

    /**
     * Flush rewrite rules when saving options
     */
    public function ss4seo_flush_rewrite_rules()
    {
        flush_rewrite_rules();
    }
}
