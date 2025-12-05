# Load Combined Core Block Assets

<!-- markdownlint-disable-next-line no-inline-html -->
<img src=".wordpress-org/banner.svg" alt="Banner for the Load Combined Core Block Assets plugin" width="1544" height="500">

Temporary workaround for sites experiencing issues with WordPress 6.9's new ability to load block styles on demand in classic themes.

**Contributors:** [westonruter](https://profile.wordpress.org/westonruter)  
**Tags:**         performance  
**Tested up to:** 6.9  
**Stable tag:**   0.1.0  
**License:**      [GPLv2 or later](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

## Description

This is a temporary workaround for sites experiencing issues with WordPress 6.9's new ability to [load block styles on demand in classic themes](https://make.wordpress.org/core/2025/11/18/wordpress-6-9-frontend-performance-field-guide/#load-block-styles-on-demand-in-classic-themes).

If running a classic theme (i.e. not a block theme), it filters `should_load_separate_core_block_assets` to be `false`. This has the effect of reverting a change introduced in 6.9 where classic themes now load separate core block assets on demand by default.

Note that this plugin should be considered temporary until any issues are resolved in 6.9.1. At any time, you can test whether any issues remain by adding `?should_load_separate_core_block_assets=true` to any frontend URL; this restores the default behavior in WP 6.9. 

This workaround is temporary because there are performance benefits to loading separate core block assets. They can be loaded on demand just when they are used, as opposed to loading the large single combined `wp-block-library` stylesheet. Loading separate block styles on demand reduces the amount of CSS which should improve page load time.

## Installation

### Automatic

1. Visit **Plugins > Add New** in the WordPress Admin.
2. Search for **Load Combined Core Block Assets**.
3. Install and activate the **Load Combined Core Block Assets** plugin.

You may also install and update via [Git Updater](https://git-updater.com/) using the [plugin's GitHub URL](https://github.com/westonruter/load-combined-core-block-assets).

### Manual

1. Download the plugin ZIP either [from WordPress.org](https://downloads.wordpress.org/plugin/load-combined-core-block-assets.zip) or [from GitHub](https://github.com/westonruter/load-combined-core-block-assets/archive/refs/heads/main.zip). Alternatively, if you have a local clone of the repo, run `npm run plugin-zip`.
2. Visit **Plugins > Add New Plugin** in the WordPress Admin.
3. Click **Upload Plugin**.
4. Select the `load-combined-core-block-assets.zip` file on your system from step 1 and click **Install Now**.
5. Click the **Activate Plugin** button.

## Changelog

### 0.1.0

* Initial release.
