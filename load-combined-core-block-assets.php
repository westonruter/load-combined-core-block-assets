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
 * Version: 0.1.0
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
const VERSION = '0.1.0';

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

		$tag = sprintf( "\n<!-- %s -->\n%s", $comment, $tag );
	}
	return $tag;
}

add_filter( 'style_loader_tag', __NAMESPACE__ . '\filter_style_loader_tag', 10, 2 );
