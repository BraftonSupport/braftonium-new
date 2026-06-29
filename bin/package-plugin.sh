#!/usr/bin/env bash
#
# package-plugin.sh — produce an installable WordPress plugin zip.
#
# 1. Stamps the calculated version and the "Braftonium" plugin name into the
#    main plugin file's WordPress header doc block.
# 2. Assembles the plugin into dist/braftonium/ (a single top-level folder, as
#    WordPress requires for "Upload Plugin" installs).
# 3. Zips it to braftonium.zip at the repo root.
#
# Run this AFTER the SCSS + gutenberg-addon builds, so the compiled CSS/JS
# (which are gitignored and only exist post-build) are included in the package.
#
# Usage: bin/package-plugin.sh <version>
set -euo pipefail

version="${1:?Usage: package-plugin.sh <version>}"
plugin_file="brafton-new-braftonium.php"
slug="braftonium"

# --- Stamp the WordPress plugin header --------------------------------------
# WordPress reads "Plugin Name:" and "Version:" from the main file's doc block.
# Match the docblock lines (" * Plugin Name:" / " * Version:") regardless of
# the existing value or surrounding whitespace.
sed -i -E "s|^([[:space:]]*\*[[:space:]]*Plugin Name:).*|\1 Braftonium|" "${plugin_file}"
sed -i -E "s|^([[:space:]]*\*[[:space:]]*Version:).*|\1 ${version}|" "${plugin_file}"

echo "Stamped ${plugin_file}: Plugin Name -> Braftonium, Version -> ${version}"

# --- Assemble the package ---------------------------------------------------
rm -rf dist
mkdir -p "dist/${slug}"
rsync -a \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='node_modules' \
  --exclude='dist' \
  --exclude='*.map' \
  --exclude='maps' \
  --exclude='bin' \
  ./ "dist/${slug}/"

# --- Zip --------------------------------------------------------------------
( cd dist && zip -qr "../${slug}.zip" "${slug}" )
echo "Created ${slug}.zip"
