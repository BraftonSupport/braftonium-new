<?php
/**
 * COPY ME — pattern file template.
 *
 * 1. Copy this file to patterns/<your-slug>.php (the "_" prefix means THIS file
 *    is skipped by the loader — your copy must NOT start with "_").
 * 2. Fill in the headers below (Title + Slug are required).
 * 3. Replace the markup under the closing "?>" with your block markup.
 *
 * The loader (general-settings/block-patterns.php) auto-registers it on `init` —
 * no register_block_pattern() call needed. Unknown Categories are auto-created.
 *
 * ── Recognised headers (mirror WordPress core theme-pattern headers) ──────────
 * Title:          Human-readable name (required).
 * Slug:           Unique id, e.g. braftonium/your-slug (required).
 * Categories:     Comma list. Base ones: braftonium, braftonium-sections,
 *                 braftonium-containers, braftonium-components.
 * Keywords:       Comma list of inserter search terms.
 * Viewport Width: Inserter preview width in px, e.g. 1200.
 * Description:    One-line summary.
 * Inserter:       "no" to hide from the inserter (default yes).
 * Block Types:    Comma list, e.g. core/post-content (optional).
 * Post Types:     Comma list (optional).
 *
 * ── AI / REUSE FRONTAGE (optional, ignored by WordPress) ──────────────────────
 * @intent       What this layout is for, in one line.
 * @layout       The structural shape (columns, grid, band…).
 * @composition  The block tree.
 * @classes      Custom classes used (keep them generic, not content-specific).
 * @slots        What an author edits/replaces.
 * @reusewhen    When to reach for this pattern.
 * @avoidwhen    When NOT to use it.
 * @responsive   How it behaves on mobile.
 * ───────────────────────────────────────────────────────────────────────────
 *
 * Example skeleton (delete and replace):
 *
 * Title:          Example Pattern
 * Slug:           braftonium/example
 * Categories:     braftonium
 * Description:    A short description.
 */
?>
<!-- wp:paragraph -->
<p>Replace this with your block markup.</p>
<!-- /wp:paragraph -->
