# Braftonium Plugin

Braftonium is now a native-first WordPress plugin. The current root plugin uses native WordPress admin fields and native Gutenberg blocks as the core experience. ACF Pro is no longer required for the main plugin features.

Legacy ACF blocks and ACF option data are still supported through the `/legacy` compatibility layer. This means existing pages that already use old `acf/*` Braftonium blocks should continue to render, while new work should use the native `braftonium/*` blocks and the native Braftonium settings screens.

## Installation

The plugin's CSS and editor JS are compiled at build time (the source uses SCSS and JSX, and `*.css` is intentionally git-ignored). For that reason you should always install a **built package**, not a raw source checkout. CI builds these packages for you.

### Option 1 — Manual upload (zip)

1. Download `braftonium.zip`:
   - **Production:** from the [Releases](https://github.com/braftonsupport/braftonium-new/releases) page (attached to each published release).
   - **Latest develop:** from the **Dev Build** workflow run under the repo's **Actions** tab → the `braftonium-<version>` artifact.
2. In WordPress admin go to **Plugins → Add New → Upload Plugin**, choose the zip, and click **Install Now**, then **Activate**.

The zip already contains a single top-level `braftonium/` folder, so it installs to `wp-content/plugins/braftonium/`.

### Option 2 — Install via Composer (production releases)

Each published release attaches a built `braftonium.zip` to the GitHub Release. Because the plugin is typed `wordpress-plugin`, [`composer/installers`](https://github.com/composer/installers) places it under `wp-content/plugins/`. Add the following to the **consuming project's** `composer.json`, pointing the `version` and the `dist.url` at the release you want:

```json
{
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "braftonsupport/braftonium",
                "version": "1.0.0",
                "type": "wordpress-plugin",
                "dist": {
                    "type": "zip",
                    "url": "https://github.com/braftonsupport/braftonium-new/releases/download/v1.0.0/braftonium.zip"
                },
                "require": {
                    "composer/installers": "^1.0 || ^2.0"
                }
            }
        }
    ],
    "require": {
        "composer/installers": "^1.0 || ^2.0",
        "braftonsupport/braftonium": "1.0.0"
    },
    "extra": {
        "installer-paths": {
            "wp-content/plugins/{$name}/": ["type:wordpress-plugin"]
        }
    }
}
```

Then run:

```bash
composer install
```

Composer installs the plugin to `wp-content/plugins/braftonium/` (the `{$name}` segment resolves to `braftonium`).

Notes:

- Bump both the `package.version` and the version in `dist.url` together for each upgrade (the package repository's version is what Composer resolves against).
- If this repository is **private**, Composer needs a GitHub token to download the release asset. Provide one with `composer config --global github-oauth.github.com <TOKEN>` (a token with `repo` scope).
- The `extra.installer-paths` block is only required if your WordPress lives somewhere other than the Composer project root; adjust the path to match your install.

## Current Architecture

1. Native blocks live in `/blocks` and register from each block's `block.json`.
2. Native settings live in `/general-settings` and store values in WordPress options/meta.
3. Legacy ACF code lives in `/legacy`.
4. `/legacy/legacy.php` is loaded by the main plugin and safely enables compatibility when ACF is available.
5. Old ACF settings pages are not loaded, because they use the same admin slugs and some of the same function names as the native settings.
6. Legacy option values are read through `/legacy/compat-settings.php` when native values are empty, and native saves mirror key values back into ACF when ACF is installed.

## Blocks

### Native Blocks

Native blocks are the blocks to use for all new content. They appear in the `Braftonium` block category and use the `braftonium/*` namespace.

Current native blocks include:

1. `braftonium/banner`
2. `braftonium/cta`
3. `braftonium/custom-row`
4. `braftonium/custom-list`
5. `braftonium/custom-list-item`
6. `braftonium/slider`
7. `braftonium/slide`

Native blocks are registered automatically from `/blocks/<block-name>/block.json`. Dynamic output is handled by the block's `render.php` file when present.

### Legacy ACF Blocks

Legacy ACF blocks are loaded only when ACF is available. Their saved block names are preserved so existing content keeps working.

Legacy blocks keep their original `acf/*` IDs, including:

1. `acf/banner`
2. `acf/cta`
3. `acf/custom-row`
4. `acf/custom-list`
5. `acf/custom-list-item`
6. `acf/slider`
7. `acf/slide`
8. `acf/swiper`
9. `acf/swiper-slide`
10. `acf/google-map`
11. `acf/contentlist`

In the editor, these blocks are listed under `Braftonium - Legacy`, and their titles include `(legacy - don't use)`. This is intentional: old content remains editable, but new pages should use native blocks.

## Settings

The native Braftonium settings pages are the source of truth for current installs.

Main settings include:

1. Admin Override
2. Google API Key, used by legacy Google Map blocks
3. Revisions toggle
4. Feature toggles

Feature pages include:

1. Custom Posts & Taxonomies
2. Scripts & Styles
3. Debug

### Legacy Settings Compatibility

If a native setting has not been saved yet, Braftonium can fall back to legacy ACF option values.

Current compatibility coverage includes:

1. `admin-override` to native `admin_override`
2. `google-api-key` to native `google_api_key`
3. `debug-on` to native debug settings
4. `custom_post_types_taxonomies` to native custom taxonomy settings
5. `custom_post_types_new` to native custom post type settings
6. `braftonium_injector` to native global and local script/style injection rules

When native settings are saved and ACF is installed, key values are mirrored back to the old ACF option fields so legacy templates that call `get_field( ..., 'option' )` can still work.

## Custom Posts & Taxonomies

Use `Braftonium > Posts/Taxonomies` to create custom taxonomies and custom post types without code.

The native settings store:

1. Taxonomies in `braftonium_custom_post_types_taxonomies`
2. Custom post types in `braftonium_custom_post_types_new`

Existing legacy ACF settings are used as fallbacks if the native options are empty.

Developer filters:

```php
apply_filters( 'braftonium_taxonomy_filter', $args );
apply_filters( 'braftonium_modify_custom_post_type', $args, $custom_post_type );
```

## Scripts & Styles

Use `Braftonium > Scripts & Styles` to add global injection rules, or use the post/page metabox for local rules.

Supported rule types:

1. Inline JS
2. Inline CSS
3. Enqueued JS/CSS URL

Supported locations:

1. Header
2. Body start
3. Body end
4. Footer, kept as a legacy alias of body end

Legacy ACF injector rules are normalized into the native rule shape at runtime.

## Debug

Use `Braftonium > Debug` to enable debug output for administrators and inspect the WordPress debug log. If the native debug option has not been saved, the old ACF `debug-on` value is used as a fallback.

## Patterns

Patterns live in `/patterns` and are loaded when the Block Patterns feature is enabled.

To add a theme pattern:

1. Create `braftonium/patterns` inside the active theme.
2. Add a file ending in `-pattern.php`.
3. Register the pattern in that file using WordPress block pattern APIs.

## MicroStyles

MicroStyles add focused class options to blocks from the block editor's Advanced panel.

Admins can manage MicroStyles from `Braftonium > General Settings`.

Each managed MicroStyle has:

1. Label, shown in the editor control
2. Class, added to the selected block
3. Block Types, either `All` or one or more registered block types
4. CSS Declarations, optional CSS for the class
5. Enabled toggle

Choose `All` to make the MicroStyle available on every block. CSS Declarations should be declarations only, without a selector or braces.

Developers can still register custom MicroStyles with:

```php
function modify_classes( $class_list, $block_type ) {
    $class_list[] = array(
        'label' => 'Readable name',
        'value' => 'your-classname',
    );

    return $class_list;
}
add_filter( 'braftonium_class_list', 'modify_classes', 10, 2 );
```

Built-in MicroStyles are available for Banner, CTA, Custom Row, Custom List and Slider:

1. `braftonium-bg-full` makes the block background span the viewport while content stays wrapped.
2. `braftonium-bg-wrap` constrains the background to the content width.

## Useful Functions

The plugin includes helper functions in `/general-settings/useful-functions.php`, including:

1. `consoleJS`
2. `readingTime`
3. `includeForAdmin`

## Development Notes

1. New blocks should be native Gutenberg blocks in `/blocks`.
2. Do not create new ACF blocks unless maintaining legacy content.
3. Do not load old files from `/legacy/general-settings` in the main plugin; they are kept for reference and backward compatibility context only.
4. Legacy block template overrides still use the old theme path: `/themes/current-theme/braftonium/blocks/<block-name>.html.php`.
5. The native plugin should keep working without ACF installed. ACF is only needed for editing/rendering legacy ACF blocks and reading/mirroring legacy ACF option data.
