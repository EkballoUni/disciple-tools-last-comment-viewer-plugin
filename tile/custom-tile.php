<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_Last_Comment_Plugin_Tile
{
    private static $_instance = null;
    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct(){
        add_filter( "dt_custom_fields_settings", [ $this, "dt_custom_fields" ], 1, 2 );
    }

    /**
     * @param array $fields
     * @param string $post_type
     * @return array
     */
    public function dt_custom_fields( array $fields, string $post_type = "" ) {
        if ( $post_type === "contacts" || $post_type === "groups"){
            $fields['last_comment'] = [
                'name'        => __( 'Last Comment', 'disciple-tools-last-comment-plugin' ),
                'description' => _x( 'Last Comment', 'Showing Last Comment', 'disciple-tools-last-comment-plugin' ),
                'type'        => 'text',
                'default'     => '',
                'tile' => '',
                'icon' => get_template_directory_uri() . '/dt-assets/images/comment.svg',
            ];
        }
        return $fields;
    }
}
Disciple_Tools_Last_Comment_Plugin_Tile::instance();
