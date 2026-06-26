# Braftonium Legacy Layer

The `/legacy` folder contains the old ACF-based Braftonium implementation. It is no longer the primary plugin architecture. It exists so sites that already used the ACF blocks and ACF option fields can keep working while new content uses the native root plugin.

## How Legacy Loading Works

The main plugin includes `/legacy/legacy.php`.

That file:

1. Loads `/legacy/compat-settings.php`.
2. Loads `/legacy/blocks/blocks.php` only when ACF block functions are available.
3. Does not load the old files in `/legacy/general-settings`.

This keeps backward compatibility without bringing back duplicate admin menus or duplicate function names.

## Legacy ACF Blocks

Legacy blocks are still registered with their original ACF block names. This is the important backward compatibility rule: saved content must keep using the same block IDs.

Current legacy block IDs include:

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

The editor display has changed:

1. Legacy blocks are listed in the `Braftonium - Legacy` category.
2. Legacy block titles include `(legacy - don't use)`.
3. The original block names are unchanged.

That means old pages continue to render, but editors get a clear signal to use native blocks for new work.

## Legacy Block Templates

Legacy blocks still use the original template callback in `/legacy/blocks/blocks.php`.

Template resolution works like this:

1. First, Braftonium checks the active theme for `/braftonium/blocks/<block-name>.html.php`.
2. If no theme override exists, Braftonium uses `/legacy/blocks/<block-name>/<block-name>.html.php`.

This preserves old theme overrides.

## Legacy ACF Field JSON

Each legacy block can have a matching `*-fields.json` file in its block folder. The legacy block loader imports those field groups with `acf_add_local_field_group()`.

Example:

1. Block registration: `/legacy/blocks/banner/banner.acf.php`
2. Template: `/legacy/blocks/banner/banner.html.php`
3. ACF fields: `/legacy/blocks/banner/banner-fields.json`

The example block remains in the folder but is intentionally skipped by the legacy loader.

## Legacy Settings

The old ACF settings screens in `/legacy/general-settings` are not loaded by the current plugin.

They are kept as historical/reference files only because directly including them would conflict with the native settings implementation. Some of them register the same menu slugs and define functions with names that are now used by native settings.

Instead, `/legacy/compat-settings.php` provides fallbacks and mirroring for old ACF option data.

## Settings Compatibility

When native settings are empty, the compatibility layer can read old ACF option values.

Supported legacy fields include:

1. `admin-override`
2. `google-api-key`
3. `debug-on`
4. `custom_post_types_taxonomies`
5. `custom_post_types_new`
6. `braftonium_injector`

When native settings are saved and ACF is installed, key values are mirrored back into the old ACF fields. This keeps old templates and old legacy blocks that still call `get_field( ..., 'option' )` working.

## Scripts & Styles Compatibility

Legacy injector rules used method names like:

1. `css`
2. `js`
3. `stylesheet`
4. `js_script`
5. `js_script_async`
6. `js_script_defer`

The native injector normalizes those values into the current methods:

1. `inline_css`
2. `inline_js`
3. `enqueue`

The old `footer` location is still accepted as a legacy alias.

## Custom Posts Compatibility

Legacy custom post type and taxonomy values are used as fallbacks when native options are empty.

Legacy values:

1. `custom_post_types_taxonomies`
2. `custom_post_types_new`

Native values:

1. `braftonium_custom_post_types_taxonomies`
2. `braftonium_custom_post_types_new`

Saving the native Custom Posts & Taxonomies screen mirrors compatible data back to ACF when ACF is installed.

## Google Map Compatibility

The legacy Google Map block still reads the Google API key using:

```php
get_field( 'google-api-key', 'option' );
```

The native General Settings screen includes a Google API Key field and mirrors that value back into the legacy ACF option when ACF is installed.

## Creating New Work

Use the root plugin folders for new work:

1. New blocks go in `/blocks` as native Gutenberg blocks.
2. New settings go in `/general-settings` using WordPress options/meta.
3. New patterns go in `/patterns`.

Use `/legacy` only to maintain existing ACF content.

## Important Guardrails

1. Do not rename legacy ACF block `name` values. That would break existing saved content.
2. Do not include `/legacy/general-settings/*.php` from the main plugin.
3. Keep legacy blocks visually marked as legacy in the editor.
4. Keep the legacy category slug as `braftonium-legacy`.
5. Keep native blocks in the `braftonium/*` namespace.
