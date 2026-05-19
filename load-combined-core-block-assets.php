<?php
/**
 * Load Combined Core Block Assets Plugin for WordPress
 *
 * @package   LoadCombinedCoreBlockAssets
 * @author    Weston Ruter
 * @license   GPL-2.0-or-later
 * @copyright Copyleft 2025, Weston Ruter
 *
 * @wordpress-plugin
 * Plugin Name: Load Combined Core Block Assets
 * Plugin URI: https://github.com/westonruter/load-combined-core-block-assets
 * Description: Temporary workaround for sites experiencing issues with WordPress 6.9's new ability to <a href="https://make.wordpress.org/core/2025/11/18/wordpress-6-9-frontend-performance-field-guide/#load-block-styles-on-demand-in-classic-themes">load block styles on demand in classic themes</a>.
 * Requires at least: 6.9
 * Requires PHP: 7.2
 * Version: 1.0.0
 * Author: Weston Ruter
 * Author URI: https://weston.ruter.net/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/westonruter/load-combined-core-block-assets
 * Primary Branch: main
 */

namespace LoadCombinedCoreBlockAssets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * Plugin version.
 *
 * @since 0.1.0
 * @var string
 */
const VERSION = '1.0.0';

/**
 * Inits plugin.
 *
 * @since 0.1.0
 *
 * @return void
 */
function init() { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
	if ( wp_is_block_theme() ) {
		return;
	}
	add_filter(
		'should_load_separate_core_block_assets',
		__NAMESPACE__ . '\filter_should_load_separate_core_block_assets',
		PHP_INT_MAX
	);
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\init' );

/**
 * Filters should_load_separate_core_block_assets.
 *
 * @since 0.1.0
 *
 * @return bool Whether should_load_separate_core_block_assets is enabled.
 */
function filter_should_load_separate_core_block_assets(): bool {
	if ( ! isset( $_GET['should_load_separate_core_block_assets'] ) || ! is_string( $_GET['should_load_separate_core_block_assets'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return false;
	}

	return rest_sanitize_boolean( $_GET['should_load_separate_core_block_assets'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Filters the HTML link tag of an enqueued style.
 *
 * @since 0.1.0
 *
 * @param string|mixed $tag    The link tag for the enqueued style.
 * @param string       $handle The style's registered handle.
 */
function filter_style_loader_tag( $tag, string $handle ): string {
	if ( ! is_string( $tag ) ) {
		return '';
	}
	if ( 'wp-block-library' === $handle && ! wp_should_load_separate_core_block_assets() ) {
		$comment = sprintf(
			/* translators: 1: wp-block-library, 2: should_load_separate_core_block_assets, 3: ?should_load_separate_core_block_assets=true */
			__( 'Note: This combined block library stylesheet (%1$s) is used instead of loading separate core block styles because the %2$s filter is returning false. Try loading the URL with %3$s to restore being able to load block styles on demand and see if there are any issues remaining.', 'load-combined-core-block-assets' ),
			'wp-block-library',
			'should_load_separate_core_block_assets',
			'?should_load_separate_core_block_assets=true'
		);

		$tag = sprintf( "\n<!-- %s -->\n%s", esc_html( $comment ), $tag );
	}
	return $tag;
}

add_filter( 'style_loader_tag', __NAMESPACE__ . '\filter_style_loader_tag', 10, 2 );

/**
 * Prints an inline info notice in the plugin's row on the Plugins screen.
 *
 * Since this plugin is intended to be a temporary workaround while the known
 * issues are ironed out during the 7.0 release cycle, this notice encourages
 * users to verify whether anything is still broken with the default 6.9
 * behavior and where to report any remaining issues. The notice is only shown
 * on WordPress 7.0-RC1 or later, by which point the known issues are fixed.
 *
 * @since 1.1.0
 *
 * @param string $plugin_file Plugin file path relative to the plugins directory.
 *
 * @return void
 */
function print_plugin_row_notice( string $plugin_file ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
	if ( plugin_basename( __FILE__ ) !== $plugin_file ) {
		return;
	}

	if ( version_compare( wp_get_wp_version(), '7.0-RC1', '<' ) ) {
		return;
	}

	$test_url    = add_query_arg( 'should_load_separate_core_block_assets', 'true', home_url( '/' ) );
	$support_url = 'https://wordpress.org/support/plugin/load-combined-core-block-assets/';

	$message = sprintf(
		/* translators: 1: URL to the site homepage with the query parameter, 2: ?should_load_separate_core_block_assets=true, 3: URL to the support forum */
		__( 'This plugin is intended to be a temporary workaround while issues are ironed out during the WordPress 7.0 release cycle. Now that the known issues are fixed, please <a href="%1$s">visit your site</a> with <code>%2$s</code> appended to the URL to check whether anything is still amiss without this workaround. If you still experience issues, please <a href="%3$s" target="_blank" rel="noopener">open a topic on the support forum</a>.', 'load-combined-core-block-assets' ),
		esc_url( $test_url ),
		'?should_load_separate_core_block_assets=true',
		esc_url( $support_url )
	);

	wp_admin_notice(
		wp_kses_post( $message ),
		array(
			'type'               => 'info',
			'additional_classes' => array( 'inline' ),
		)
	);
}

add_action( 'after_plugin_row_meta', __NAMESPACE__ . '\print_plugin_row_notice' );
