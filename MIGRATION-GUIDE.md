# Braftonium 2026 - ACF to Native WordPress Blocks Migration

## Overview

This document outlines the migration from ACF (Advanced Custom Fields) blocks to native WordPress blocks while maintaining **backward compatibility** with existing content.

## Migration Strategy

### Phase 1: Infrastructure Setup ✅

1. **Build System**
   - Updated `blocks/package.json` with @wordpress/scripts
   - Created `webpack.config.js` for multi-block builds
   - Configured automatic block discovery and registration

2. **Backward Compatibility Layer**
   - Created `blocks/includes/backward-compat.php`
   - Helper functions read from both ACF fields and native attributes
   - Automatic fallback ensures existing content continues to work

3. **Dual Block Registration**
   - Updated `blocks/blocks.php` to support both ACF and native blocks
   - ACF blocks remain functional (prefixed with `acf/`)
   - Native blocks use `braftonium/` namespace

### Phase 2: Block Conversion (In Progress)

#### Banner Block ✅ (Proof of Concept)

**Files Created:**
- `blocks/banner/block.json` - Block metadata and attributes
- `blocks/banner/src/index.js` - Block registration
- `blocks/banner/src/edit.js` - React edit component
- `blocks/banner/src/editor.scss` - Editor styles
- `blocks/banner/banner.render.php` - Server-side rendering with ACF fallback

**Features:**
- Background image selector with media library
- RGBA overlay color with opacity control
- Content alignment (left/center/right)
- InnerBlocks for flexible content (headings, paragraphs)
- Full backward compatibility with ACF data

**Attributes:**
```json
{
  "backgroundImage": number (attachment ID),
  "backgroundImageUrl": string,
  "backgroundImageAlt": string,
  "overlayColor": { r, g, b, a },
  "alignContent": "left" | "center" | "right"
}
```

#### Remaining Blocks (Pending)

**Simple Blocks:**
- [ ] CTA Block
- [ ] Example Block
- [ ] Custom List Item
- [ ] Slide Block
- [ ] Swiper Slide Block

**Container Blocks:**
- [ ] Slider Block (with InnerBlocks for slides)
- [ ] Swiper Block (with InnerBlocks for swiper-slides)
- [ ] Custom Row Block
- [ ] Custom List Block

**Complex Blocks:**
- [ ] Content List Block (dynamic query with REST API)
- [ ] Google Map Block

### Phase 3: Settings Migration (Pending)

Convert ACF Options Pages to WordPress Settings API:

1. **General Settings** (`general-settings/settings.php`)
   - Debug mode toggle
   - Admin override settings
   - Font Awesome integration

2. **Custom Posts & Taxonomies** (`general-settings/custom-posts.php`)
   - Custom post type creator
   - Taxonomy management
   - Dynamic field population

3. **Template Overrider** (`general-settings/template-overider.php`)
   - User-specific template swapping
   - Template management

4. **Script/Style Injection** (`general-settings/inject-scripts-styles.php`)
   - Custom CSS/JS injection
   - Per-page/post script management

### Phase 4: Pattern Updates (Pending)

Update block patterns in `patterns/` directory:
- Replace `acf/banner` with `braftonium/banner`
- Update block data structure from ACF format to native attributes
- Maintain pattern functionality

### Phase 5: Data Migration (Pending)

Create migration utilities:
- WP-CLI command for bulk content migration
- Admin interface for progressive migration
- Content backup before migration
- Rollback capability

## Backward Compatibility

### How It Works

1. **Dual Block Names:**
   - Legacy: `acf/banner`
   - Native: `braftonium/banner`

2. **Data Reading Priority:**
   ```php
   // In render templates
   $value = braftonium_get_block_value($block, 'attributeName', 'acf_field_name', $default);
   ```
   - First checks native block attributes
   - Falls back to ACF get_field()
   - Returns default if neither exists

3. **Gradual Migration:**
   - Users can re-save blocks to convert to native format
   - No forced migration required
   - Both formats work simultaneously

### Helper Functions

Located in `blocks/includes/backward-compat.php`:

- `braftonium_get_block_value()` - Get value with ACF fallback
- `braftonium_get_block_image()` - Get image data (ACF array or attachment ID)
- `braftonium_is_legacy_acf_block()` - Check block format
- `braftonium_get_block_classes()` - Extract block CSS classes
- `braftonium_get_block_styles()` - Extract inline styles
- `braftonium_migrate_post_blocks()` - Migrate post content (pending)

## Build Process

### Development

```bash
cd blocks
npm install
npm start  # Watch mode with hot reload
```

### Production

```bash
cd blocks
npm run build  # Minified production build
```

### Sass Compilation

```bash
cd blocks
npm run sass-watch  # Watch and compile SCSS
npm run sass-compile-prd  # Production minified CSS
```

## Block Structure

### Native Block File Structure

```
blocks/
└── banner/
    ├── block.json              # Block metadata & attributes
    ├── banner.acf.php          # Legacy ACF registration (kept for compatibility)
    ├── banner-fields.json      # Legacy ACF fields (kept for compatibility)
    ├── banner.html.php         # Legacy template (kept for compatibility)
    ├── banner.render.php       # New server-side render template
    ├── banner.scss             # Frontend styles
    ├── src/
    │   ├── index.js           # Block registration
    │   ├── edit.js            # Edit component (React)
    │   └── editor.scss        # Editor-specific styles
    └── build/
        └── index.js           # Compiled JS (generated)
```

## Creating New Native Blocks

### 1. Create block.json

```json
{
  "apiVersion": 3,
  "name": "braftonium/block-name",
  "title": "Block Title",
  "category": "braftonium",
  "attributes": { ... },
  "supports": { ... }
}
```

### 2. Create src/edit.js

```javascript
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit({ attributes, setAttributes }) {
  const blockProps = useBlockProps();
  return <div {...blockProps}>Block editor UI</div>;
}
```

### 3. Create src/index.js

```javascript
import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from '../block.json';

registerBlockType(metadata.name, {
  ...metadata,
  edit: Edit,
  save: () => null  // Server-side rendering
});
```

### 4. Create block-name.render.php

```php
<?php
$value = braftonium_get_block_value($block_data['block'], 'attributeName', 'acf_fallback_name');
?>
<div class="your-block-class">
  <?php echo esc_html($value); ?>
  <?php echo $block_data['content']; // InnerBlocks ?>
</div>
```

### 5. Build

```bash
npm run build
```

## Testing

### Test Legacy ACF Blocks
1. Create new post
2. Add legacy `acf/banner` block
3. Configure with ACF fields
4. Verify rendering

### Test Native Blocks
1. Create new post
2. Add new `braftonium/banner` block
3. Configure with native controls
4. Verify rendering

### Test Backward Compatibility
1. Use post with legacy ACF blocks
2. Verify blocks still render correctly
3. Edit and re-save block
4. Optionally migrate to native format

## ACF Dependencies

- Plugin still requires ACF Pro for:
  - Legacy block support
  - Options pages (until Phase 3 complete)
  - Custom field groups (until Phase 3 complete)

- After full migration:
  - ACF can be made optional
  - Legacy blocks kept for backward compatibility
  - New sites won't require ACF

## Benefits of Native Blocks

1. **Performance**: No ACF overhead in block editor
2. **Standards**: Uses WordPress core APIs
3. **Flexibility**: Full control over UI/UX
4. **Future-proof**: Aligned with WordPress direction
5. **Reduced Dependencies**: Less reliance on third-party plugins

## Troubleshooting

### Build Errors

```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

### Block Not Appearing

1. Check `block.json` is valid JSON
2. Verify `build/index.js` exists
3. Check browser console for errors
4. Clear WordPress block cache

### ACF Fallback Not Working

1. Verify ACF Pro is active
2. Check field names match in `braftonium_get_block_value()`
3. Review `backward-compat.php` helper functions

## Next Steps

1. ✅ Complete banner block conversion
2. ⏳ Complete npm install and test banner block build
3. 📋 Convert remaining simple blocks
4. 📋 Convert container blocks with InnerBlocks
5. 📋 Convert complex blocks (ContentList, GoogleMap)
6. 📋 Migrate settings pages to WordPress Settings API
7. 📋 Update patterns
8. 📋 Create migration utilities
9. 📋 Update main plugin file
10. 📋 Full testing and documentation

## Contributors

- Jonathan Kowensky
- Deryk King
- James Allan
- Fritz Bester

---

Last Updated: May 5, 2026
