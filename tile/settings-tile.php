<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Plugin_Starter_Template_Settings_Tile
 *
 * This class will add navigation and a custom section to the Settings page in Disciple.Tools.
 * The dt_profile_settings_page_menu function adds a navigation link to the bottom of the nav section in Settings.
 * The dt_profile_settings_page_sections function adds a custom content tile to the bottom of the page.
 *
 * It is likely modifications through this section will leverage a custom REST end point to process changes.
 * @see /rest-api/ in this plugin for a custom REST endpoint
 */

class Disciple_Tools_Plugin_Starter_Template_Settings_Tile
{
    private static $_instance = null;
    public static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        if ( 'settings' === dt_get_url_path() ) {
            add_action( 'dt_profile_settings_page_menu', [ $this, 'dt_profile_settings_page_menu' ], 100, 4 );
            add_action( 'dt_profile_settings_page_sections', [ $this, 'dt_profile_settings_page_sections' ], 100, 4 );
            add_action( 'dt_modal_help_text', [ $this, 'dt_modal_help_text' ], 100 );
        }
    }

    /**
     * Adds menu item
     *
     * @param $dt_user WP_User object
     * @param $dt_user_meta array Full array of user meta data
     * @param $dt_user_contact_id bool/int returns either id for contact connected to user or false
     * @param $contact_fields array Array of fields on the contact record
     */
    public function dt_profile_settings_page_menu( $dt_user, $dt_user_meta, $dt_user_contact_id, $contact_fields ) {
        ?>
        <li><a href="#disciple_tools_plugin_starter_template_settings_id"><?php esc_html_e( 'Custom Settings Section', 'disciple-tools-plugin-starter-template' )?></a></li>
        <?php
    }

    /**
     * Adds custom tile
     *
     * @param $dt_user WP_User object
     * @param $dt_user_meta array Full array of user meta data
     * @param $dt_user_contact_id bool/int returns either id for contact connected to user or false
     * @param $contact_fields array Array of fields on the contact record
     */
    public function dt_profile_settings_page_sections( $dt_user, $dt_user_meta, $dt_user_contact_id, $contact_fields ) {
        ?>
        <div class="cell bordered-box" id="disciple_tools_plugin_starter_template_settings_id" data-magellan-target="disciple_tools_plugin_starter_template_settings_id">
            <button class="help-button float-right" data-section="disciple-tools-plugin-starter-template-help-text">
                <img class="help-icon" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/help.svg' ) ?>"/>
            </button>
            <span class="section-header"><?php esc_html_e( 'Custom Settings Section', 'disciple-tools-plugin-starter-template' )?></span>
            <hr/>

            <!-- replace with your custom content -->
            <p>Replace with your custom content aa</p>

            <div style="margin-bottom: 20px; background: #eee; padding: 10px;">
                <h3>Workflow</h3>
                <ul>
                    <li>generate field when activation</li>
                    <li>delete field when deactivation</li>
                    <li>get last comment</li>
                    <li>and somehow hook to field and get last comment data</li>
                </ul>
            </div>

                <div style="max-height: 500px; overflow-y: auto">

                <?php
                    global $wp_post_types;
                    $field_key = dt_create_field_key( 'last_comment' );
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

                    foreach (DT_Posts::get_post_types() as $post_type) {
                ?>
                <div style="margin-bottom: 20px; background: #eee; padding: 10px;">
                    <?php print_r($post_type) ?>
                    <?php print_r($last_comment_field) ?>

                    <hr>
                    <h4>Contact Post Fields</h4>

                    
                    <?php
                        print_r($contact_fields['name']);
                        ?>

                        <hr>

                        <?php
                        foreach ($contact_fields as $key => $value) {
                            print_r($key);
                            ?>
                            <br>
                            <?php
                            print_r($value);
                            ?>
                            <br>
                            <br>
                            <?php
                        }
                     ?>

                    <hr>
                    <h4>Group Post Fields</h4>
                    <?php print_r($group_fields); ?>

                </div>
                
                <?php
                    }
                ?>
                </div>
            <script>
                var x = "<?php echo get_post_types() ?>";
                console.group('v : post types');
                console.log(x);
                console.groupEnd();
            </script>

        </div>
        <?php
    }

    /**
     * @see disciple-tools-theme/dt-assets/parts/modals/modal-help.php
     */
    public function dt_modal_help_text(){
        ?>
        <div class="help-section" id="disciple-tools-plugin-starter-template-help-text" style="display: none">
            <h3><?php echo esc_html_x( "Custom Settings Section", 'Optional Documentation', 'disciple-tools-plugin-starter-template' ) ?></h3>
            <p><?php echo esc_html_x( "Add your own help information into this modal.", 'Optional Documentation', 'disciple-tools-plugin-starter-template' ) ?></p>
        </div>
        <?php
    }
}

Disciple_Tools_Plugin_Starter_Template_Settings_Tile::instance();
