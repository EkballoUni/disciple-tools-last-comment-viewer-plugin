<?php
/**
 *Plugin Name: Disciple.Tools - Last Comment
 * Plugin URI: https://github.com/DiscipleTools/disciple-tools-last-comment-plugin
 * Description: Disciple.Tools - Plugin Starter Template is intended to help developers and integrator jumpstart their extension of the Disciple.Tools system.
 * Text Domain: disciple-tools-last-comment-plugin
 * Domain Path: /languages
 * Version:  0.1
 * Author URI: https://github.com/DiscipleTools
 * GitHub Plugin URI: https://github.com/DiscipleTools/disciple-tools-last-comment-plugin
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Refactoring (renaming) this plugin as your own:
 * 1. @todo Rename the `disciple-tools-plugin-starter-template.php file.
 * 2. @todo Refactor all occurrences of the name Disciple_Tools_Plugin_Starter_Template, disciple_tools_plugin_starter_template, disciple-tools-plugin-starter-template, starter_post_type, and "Plugin Starter Template"
 * 3. @todo Update the README.md and LICENSE
 * 4. @todo Update the default.pot file if you intend to make your plugin multilingual. Use a tool like POEdit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Gets the instance of the `Disciple_Tools_Plugin_Starter_Template` class.
 *
 * @since  0.1
 * @access public
 * @return object|bool
 */
function disciple_tools_last_comment_plugin() {
    $disciple_tools_last_comment_plugin = '1.0';
    $wp_theme = wp_get_theme();
    $version = $wp_theme->version;

    /*
     * Check if the Disciple.Tools theme is loaded and is the latest required version
     */
    $is_theme_dt = strpos( $wp_theme->get_template(), "disciple-tools-theme" ) !== false || $wp_theme->name === "Disciple Tools";
    if ( $is_theme_dt && version_compare( $version, $disciple_tools_last_comment_plugin, "<" ) ) {
        add_action( 'admin_notices', 'disciple_tools_plugin_starter_template_hook_admin_notice' );
        add_action( 'wp_ajax_dismissed_notice_handler', 'dt_hook_ajax_notice_handler' );
        return false;
    }
    if ( !$is_theme_dt ){
        return false;
    }
    /**
     * Load useful function from the theme
     */
    if ( !defined( 'DT_FUNCTIONS_READY' ) ){
        require_once get_template_directory() . '/dt-core/global-functions.php';
    }

    return Disciple_Tools_Last_Comment_Plugin::instance();

}
add_action( 'after_setup_theme', 'disciple_tools_last_comment_plugin', 20 );

function last_comment_field_filter($data, $post_type) {
    foreach($data["posts"] as &$post) {
        $args = array(
            'post_id' => $post->ID,
            'orderby' => array('comment_date'),
            'order' => 'DESC',
            'number' => 1
        );
        $comment = get_comments( $args );

        $post['last_comment'] = $comment[0]->comment_content;
    }
    unset($post);

    return $data;
}
add_filter( "dt_list_posts_custom_fields", "last_comment_field_filter", 10, 2);


/**
 * Singleton class for setting up the plugin.
 *
 * @since  0.1
 * @access public
 */
class Disciple_Tools_Last_Comment_Plugin {

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $is_rest = dt_is_rest();
        /**
         * @todo Decide if you want to use the REST API example
         * To remove: delete this following line and remove the folder named /rest-api
         */
        if ( $is_rest && strpos( dt_get_url_path(), 'disciple_tools_plugin_starter_template_template' ) !== false ) {
            require_once( 'rest-api/rest-api.php' ); // adds starter rest api class
        }

        /**
         * @todo Decide if you want to create a new post type
         * To remove: delete the line below and remove the folder named /post-type
         */
        require_once( 'post-type/loader.php' ); // add starter post type extension to Disciple.Tools system

        /**
         * @todo Decide if you want to create a custom site-to-site link
         * To remove: delete the line below and remove the folder named /site-link
         */
        require_once( 'site-link/custom-site-to-site-links.php' ); // add site to site link class and capabilities

        /**
         * @todo Decide if you want to add new charts to the metrics section
         * To remove: delete the line below and remove the folder named /charts
         */
        if ( strpos( dt_get_url_path(), 'metrics' ) !== false || ( $is_rest && strpos( dt_get_url_path(), 'disciple-tools-last-comment-plugin' ) !== false ) ){
            require_once( 'charts/charts-loader.php' );  // add custom charts to the metrics area
        }

        /**
         * @todo Decide if you want to add a custom tile or settings page tile
         * To remove: delete the lines below and remove the folder named /tile
         */
        require_once( 'tile/custom-tile.php' ); // add custom tile
        if ( 'settings' === dt_get_url_path() && ! $is_rest ) {
            require_once( 'tile/settings-tile.php' ); // add custom settings page tile
        }

        /**
         * @todo Decide if you want to create a magic link
         * To remove: delete the line below and remove the folder named /magic-link
         */
        require_once( 'magic-link/magic-link-post-type.php' );
        require_once( 'magic-link/magic-link-user-app.php' );

        /**
         * @todo Decide if you want to add a custom admin page in the admin area
         * To remove: delete the 3 lines below and remove the folder named /admin
         */
        if ( is_admin() ) {
            require_once( 'admin/admin-menu-and-tabs.php' ); // adds starter admin page and section for plugin
        }

        /**
         * @todo Decide if you want to support localization of your plugin
         * To remove: delete the line below and remove the folder named /languages
         */
        $this->i18n();

        /**
         * @todo Decide if you want to customize links for your plugin in the plugin admin area
         * To remove: delete the lines below and remove the function named
         */
        if ( is_admin() ) { // adds links to the plugin description area in the plugin admin list.
            add_filter( 'plugin_row_meta', [ $this, 'plugin_description_links' ], 10, 4 );
        }

    }

    /**
     * Filters the array of row meta for each/specific plugin in the Plugins list table.
     * Appends additional links below each/specific plugin on the plugins page.
     */
    public function plugin_description_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
        if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
            // You can still use `array_unshift()` to add links at the beginning.

            $links_array[] = '<a href="https://disciple.tools">Disciple.Tools Community</a>'; // @todo replace with your links.
            // @todo add other links here
        }

        return $links_array;
    }

    /**
     * Method that runs only when the plugin is activated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function activation() {
        // add elements here that need to fire on activation
        /*
        $post_types = get_post_types(['Groups', 'Contacts']);
        $contact_field_key = dt_create_field_key( 'last_comment' );
        $group_field_key = dt_create_field_key( 'last_comment' );
        $contact_fields = DT_Posts::get_post_field_settings( 'contact', false, true );
        $group_fields = DT_Posts::get_post_field_settings( 'group', false, true );

        $last_comment_field = [
            'name' => 'last_comment',
            'type' => 'text',
            'default' => '',
            'tile' => '',
            'customizable' => 'all',
            'private' => false
        ];
        $contact_last_comment_options['contact'][$contact_field_key] = $last_comment_field;
        $group_last_comment_options['contact'][$group_field_key] = $last_comment_field;

        update_option( "dt_field_customizations", $contact_last_comment_options );
        update_option( "dt_field_customizations", $group_last_comment_options );
        */
    }

    /**
     * Method that runs only when the plugin is deactivated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function deactivation() {
        // add functions here that need to happen on deactivation
        /*
        $field_customizations = dt_get_option( "dt_field_customizations" );
        $post_types = ['contact', 'group'];

        foreach($post_types as $post_type) {
            if ( isset( $field_customizations[$post_type]['last_comment'] ) ){
                unset( $field_customizations[$post_type]['last_comment'] );
            }
            update_option( "dt_field_customizations", $field_customizations );
            wp_cache_delete( $post_type . "_field_settings" );
        }
        */

        delete_option( 'dismissed-disciple-tools-last-comment-plugin' );
    }

    /**
     * Loads the translation files.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function i18n() {
        $domain = 'disciple-tools-last-comment-plugin';
        load_plugin_textdomain( $domain, false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ). 'languages' );
    }

    /**
     * Magic method to output a string if trying to use the object as a string.
     *
     * @since  0.1
     * @access public
     * @return string
     */
    public function __toString() {
        return 'disciple-tools-last-comment-plugin';
    }

    /**
     * Magic method to keep the object from being cloned.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, 'Whoah, partner!', '0.1' );
    }

    /**
     * Magic method to keep the object from being unserialized.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, 'Whoah, partner!', '0.1' );
    }

    /**
     * Magic method to prevent a fatal error when calling a method that doesn't exist.
     *
     * @param string $method
     * @param array $args
     * @return null
     * @since  0.1
     * @access public
     */
    public function __call( $method = '', $args = array() ) {
        _doing_it_wrong( "disciple_tools_last_comment_plugin::" . esc_html( $method ), 'Method does not exist.', '0.1' );
        unset( $method, $args );
        return null;
    }
}


// Register activation hook.
register_activation_hook( __FILE__, [ 'Disciple_Tools_Last_Comment_Plugin', 'activation' ] );
register_deactivation_hook( __FILE__, [ 'Disciple_Tools_Last_Comment_Plugin', 'deactivation' ] );


if ( ! function_exists( 'disciple_tools_plugin_starter_template_hook_admin_notice' ) ) {
    function disciple_tools_plugin_starter_template_hook_admin_notice() {
        global $disciple_tools_plugin_starter_template_required_dt_theme_version;
        $wp_theme = wp_get_theme();
        $current_version = $wp_theme->version;
        $message = "'Disciple.Tools - Plugin Starter Template' plugin requires 'Disciple.Tools' theme to work. Please activate 'Disciple.Tools' theme or make sure it is latest version.";
        if ( $wp_theme->get_template() === "disciple-tools-theme" ){
            $message .= ' ' . sprintf( esc_html( 'Current Disciple.Tools version: %1$s, required version: %2$s' ), esc_html( $current_version ), esc_html( $disciple_tools_plugin_starter_template_required_dt_theme_version ) );
        }
        // Check if it's been dismissed...
        if ( ! get_option( 'dismissed-disciple-tools-last-comment-plugin', false ) ) { ?>
            <div class="notice notice-error notice-disciple-tools-last-comment-plugin is-dismissible" data-notice="disciple-tools-last-comment-plugin">
                <p><?php echo esc_html( $message );?></p>
            </div>
            <script>
                jQuery(function($) {
                    $( document ).on( 'click', '.notice-disciple-tools-last-comment-plugin .notice-dismiss', function () {
                        $.ajax( ajaxurl, {
                            type: 'POST',
                            data: {
                                action: 'dismissed_notice_handler',
                                type: 'disciple-tools-last-comment-plugin',
                                security: '<?php echo esc_html( wp_create_nonce( 'wp_rest_dismiss' ) ) ?>'
                            }
                        })
                    });
                });
            </script>
        <?php }
    }
}

/**
 * AJAX handler to store the state of dismissible notices.
 */
if ( ! function_exists( "dt_hook_ajax_notice_handler" )){
    function dt_hook_ajax_notice_handler(){
        check_ajax_referer( 'wp_rest_dismiss', 'security' );
        if ( isset( $_POST["type"] ) ){
            $type = sanitize_text_field( wp_unslash( $_POST["type"] ) );
            update_option( 'dismissed-' . $type, true );
        }
    }
}

/**
 * Plugin Releases and updates
 * @todo Uncomment and change the url if you want to support remote plugin updating with new versions of your plugin
 * To remove: delete the section of code below and delete the file called version-control.json in the plugin root
 *
 * This section runs the remote plugin updating service, so you can issue distributed updates to your plugin
 *
 * @note See the instructions for version updating to understand the steps involved.
 * @link https://github.com/DiscipleTools/disciple-tools-plugin-starter-template/wiki/Configuring-Remote-Updating-System
 *
 * @todo Enable this section with your own hosted file
 * @todo An example of this file can be found in (version-control.json)
 * @todo Github is a good option for delivering static json.
 */
/**
 * Check for plugin updates even when the active theme is not Disciple.Tools
 *
 * Below is the publicly hosted .json file that carries the version information. This file can be hosted
 * anywhere as long as it is publicly accessible. You can download the version file listed below and use it as
 * a template.
 * Also, see the instructions for version updating to understand the steps involved.
 * @see https://github.com/DiscipleTools/disciple-tools-version-control/wiki/How-to-Update-the-Starter-Plugin
 */
//add_action( 'plugins_loaded', function (){
//    if ( is_admin() ){
//        // Check for plugin updates
//        if ( ! class_exists( 'Puc_v4_Factory' ) ) {
//            if ( file_exists( get_template_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' )){
//                require( get_template_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' );
//            }
//        }
//        if ( class_exists( 'Puc_v4_Factory' ) ){
//            Puc_v4_Factory::buildUpdateChecker(
//                'https://raw.githubusercontent.com/DiscipleTools/disciple-tools-plugin-starter-template/master/version-control.json',
//                __FILE__,
//                'disciple-tools-plugin-starter-template'
//            );
//
//        }
//    }
//} );
