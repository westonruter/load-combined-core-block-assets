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
 * Requires at least: 6.8
 * Requires PHP: 7.2
 * Version: 0.1.0
 * Author: Weston Ruter
 * Author URI: https://weston.ruter.net/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Update URI: https://github.com/westonruter/load-combined-core-block-assets
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
 * @var string
 */
const VERSION = '0.1.0';

add_action(
	'after_setup_theme',
	static function () { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
		if ( wp_is_block_theme() ) {
			return;
		}
		add_filter(
			'should_load_separate_core_block_assets',
			static function (): bool {
				if ( ! isset( $_GET['should_load_separate_core_block_assets'] ) || ! is_string( $_GET['should_load_separate_core_block_assets'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					return false;
				}

				return rest_sanitize_boolean( $_GET['should_load_separate_core_block_assets'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			},
			PHP_INT_MAX
		);
	}
);
