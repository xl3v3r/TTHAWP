<?php
/**
 * Plugin Name: Things Taylor Hates
 * Description: A collection of toggles to disable WordPress features Taylor dislikes.
 * Version: 0.1.2
 * Author: SP4RKL
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define option name
define( 'TTH_OPTION_NAME', 'tth_settings' );

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
    // Handle form submission
    if ( isset( $_POST['tth_submit'] ) && check_admin_referer( 'tth-settings-update' ) ) {
        $options = tth_get_settings();
        $known_keys = array_keys( $options );
        foreach ( $known_keys as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                $options[ $key ] = true;
            } else {
                $options[ $key ] = false;
            }
        }
        update_option( TTH_OPTION_NAME, $options );
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
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
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Function to disable RankMath TOC on pages based on toggle
function tth_disable_rank_math_toc_on_pages( $enabled ) {
    $options = tth_get_settings();
    // If toggle is enabled and we are on a singular page, disable
    if ( ! empty( $options['disable_rank_math_toc_pages'] ) && is_singular( 'page' ) ) {
        return false;
    }
    return $enabled;
}
// Add filter with priority 10 (default) but we can try later priority to ensure it runs after RankMath's filter
add_filter( 'rank_math/analyze/test/has_toc', 'tth_disable_rank_math_toc_on_pages', 10, 1 );

// Placeholder for other features Taylor hates
function tth_placeholder_feature() {
    // Future code
}
?>
