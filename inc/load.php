<?php

/**
 * Plugin Name: WordPress Settings API
 * Plugin URI: http://tareq.wedevs.com/2012/06/wordpress-settings-api-php-class/
 * Description: WordPress Settings API testing
 * Author: Tareq Hasan
 * Author URI: http://tareq.weDevs.com
 * Version: 0.1
 */
require_once dirname( __FILE__ ) . '/class.settings-api.php';

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
if ( !class_exists('baEddGalleriesLoader' ) ):
class baEddGalleriesLoader {

    private $settings_api;

    const version = '1.0';

    function __construct() {

        $this->dir                  = plugin_dir_path( __FILE__ );
        $this->url                  = plugins_url( '', __FILE__ );
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_init', array($this,'plugin_admin_init' ));
        add_action( 'admin_menu', array($this,'menu_page' ));
        add_action( 'admin_menu', array($this,'submenu_page'));

    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function menu_page(){
        $menu = add_menu_page( 'EDD Catalog', 'EDD Catalog', 'manage_options', 'edd-catalog', array($this,'draw_menu_page'), plugins_url( '/icon.png', __FILE__ ),100 );
        add_action( 'admin_print_styles-' . $menu, array($this,'admin_custom_css' ));
    }

    function draw_menu_page(){

            $site         = $this->get_opt( 'site', 'ba_edd_catalog_settings', 'http://easydigitaldownloads.com' );

            ?><div class="ba-edd-catalog-head row">

                <div class="col-md-4 ba-edd-catalog-welcome">
                   <h2 class="ba-edd-catalog-title">Product Catalog</h2>
                </div>

                <div class="col-md-8 ba-edd-catalog-news-feed">
                    <h2 class="ba-edd-news-title">Latest news</h2>
                    <a class="ba-edd-news-all" href="http://nickhaskins.co/news" target="_blank">More News &rsaquo;</a>
                    <?php echo ba_edd_catalog_news_feed();?>
                </div>

            </div>

            <div class="ba-edd-catalog-wrap">

            <?php if(function_exists('ba_edd_catalog_data')) {
                    echo ba_edd_catalog_data($site);
            } ?>

        </div>

        <?php


    }

    function plugin_admin_init() {
        wp_register_style( 'edd-catalog-style', $this->url.'/../css/style.css', self::version, true );
    }

    function submenu_page() {
       	add_submenu_page( 'edd-catalog', 'Settings', 'Settings', 'manage_options', 'edd-catalog-settings', array($this,'submenu_page_callback') );
    }

    function submenu_page_callback() {

        echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
                echo '<h2>EDD Catalog Settings</h2>';

        $this->settings_api->show_forms();

        echo '</div>';

    }

   	function admin_custom_css() {
       wp_enqueue_style( 'edd-catalog-style' );

    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'ba_edd_catalog_settings',
                'title' => __( 'Setup', 'edd-catalogs' )
            )
        );
        return $sections;
    }

    function get_settings_fields() {
        $settings_fields = array(
            'ba_edd_catalog_settings' => array(
                array(
                    'name' => 'site',
                    'label' => __( 'Shop Website', 'edd-catalogs' ),
                    'desc' => __( 'This should be the URL where the shop is installed.', 'edd-catalogs' ),
                    'type' => 'text',
                    'default' => '',
                    'sanitize_callback' => 'callback_text'
                ),
                array(
                    'name' => 'feed',
                    'label' => __( 'Shop News Feed', 'edd-catalogs' ),
                    'desc' => __( 'This should be the news feed URL for the shop.', 'edd-catalogs' ),
                    'type' => 'text',
                    'default' => '',
                    'sanitize_callback' => 'callback_text'
                )
            )
        );

        return $settings_fields;
    }
    function get_opt( $option, $section, $default = '' ) {

        $options = get_option( $section );

        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }

        return $default;
    }
}
endif;

$settings = new baEddGalleriesLoader();