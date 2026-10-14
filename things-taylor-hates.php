<?php
/**
 * Plugin Name: Things Taylor Hates
 * Description: A collection of toggles to disable WordPress features Taylor dislikes.
 * Version: 0.1.3
 * Author: SP4RKL
 * License: GPL2
 * Text Domain: tth
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define option name
define( 'TTH_OPTION_NAME', 'tth_settings' );

// Load translations.
function tth_load_textdomain() {
    load_plugin_textdomain( 'tth', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'tth_load_textdomain' );

// Get settings with defaults
function tth_get_settings() {
    $defaults = array(
        'disable_rank_math_toc_pages' => true, // default enabled (feature disabled)
    );
    $options = get_option( TTH_OPTION_NAME, array() );
    // Merge defaults with existing options, preserving existing keys
    return wp_parse_args( $options, $defaults );
}

// Initialize settings on activation
function tth_install() {
    $defaults = array(
        'disable_rank_math_toc_pages' => true,
    );
    // Only add if not already set
    if ( false === get_option( TTH_OPTION_NAME ) ) {
        add_option( TTH_OPTION_NAME, $defaults );
    }
}
register_activation_hook( __FILE__, 'tth_install' );

// Add admin menu and settings page
function tth_admin_menu() {
    add_options_page(
        __( 'Things Taylor Hates', 'tth' ),
        __( 'Things Taylor Hates', 'tth' ),
        'manage_options',
        'tth-settings',
        'tth_settings_page'
    );
}
add_action( 'admin_menu', 'tth_admin_menu' );

// Settings page callback
function tth_settings_page() {
    // Defense in depth: the menu already gates on manage_options, but the
    // callback must verify the current user too.
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Handle form submission
    if ( isset( $_POST['tth_submit'] ) && check_admin_referer( 'tth-settings-update' ) ) {
        $options    = tth_get_settings();
        $known_keys = array_keys( $options );
        foreach ( $known_keys as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                $options[ $key ] = true;
            } else {
                $options[ $key ] = false;
            }
        }
        update_option( TTH_OPTION_NAME, $options );
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'tth' ) . '</p></div>';
    }
    $options = tth_get_settings();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Things Taylor Hates Settings', 'tth' ); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field( 'tth-settings-update' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Disable RankMath Table of Contents on Pages', 'tth' ); ?></th>
                    <td>
                        <label for="disable_rank_math_toc_pages">
                            <input type="checkbox" name="disable_rank_math_toc_pages" id="disable_rank_math_toc_pages" value="1" <?php checked( $options['disable_rank_math_toc_pages'], true ); ?> />
                            <?php esc_html_e( 'Enable this feature to disable RankMath TOC scoring on Pages', 'tth' ); ?>
                        </label>
                        <p class="description"><?php esc_html_e( 'When checked, RankMath will not score Table of Contents presence on Page post types.', 'tth' ); ?></p>
                    </td>
                </tr>
                <!-- Add more toggles here -->
            </table>
            <?php submit_button( null, 'primary', 'tth_submit' ); ?>
        </form>
    </div>
    <?php
}

// Function to disable RankMath TOC test on pages based on toggle.
//
// Rank Math exposes its content-analysis tests via the
// `rank_math/researches/tests` filter, keyed by test ID. The Table of Contents
// test ID is `contentHasTOC`. `$type` is the object type (`post`, `user`,
// `term`) in the post editor, or the post type slug (e.g. `page`) in Rank
// Math's AI content-analysis path.
function tth_disable_rank_math_toc_on_pages( $tests, $type ) {
    $options = tth_get_settings();

    // Toggle is off: leave every test intact.
    if ( empty( $options['disable_rank_math_toc_pages'] ) ) {
        return $tests;
    }

    // Resolve the post type. In the editor $type is the object type ('post');
    // in the AI analysis path it is already the post type slug.
    $post_type = ( 'post' === $type ) ? get_post_type() : $type;

    // Only Pages are affected.
    if ( 'page' !== $post_type ) {
        return $tests;
    }

    unset( $tests['contentHasTOC'] );

    return $tests;
}
add_filter( 'rank_math/researches/tests', 'tth_disable_rank_math_toc_on_pages', 10, 2 );

// Placeholder for other features Taylor hates
function tth_placeholder_feature() {
    // Future code
}
