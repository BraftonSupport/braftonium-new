# Brafonium Blocks

## Overview

It can be a tedious task creating blocks.. You need to rewrite ALOT of code. We have developed a system which speeds things up(ALOT).. and requires minimal code. We have included a SCSS compiler, given you the ability to override templates in your child theme and JSON fields (no database work required to setup fields/groups).

## Example

There is an example block in folder /example. Use this, and it's comments to guide you. This guide will be based on the Example Block.

## Creating a block

1. Create a new folder in /blocks/example
2. Add acf.php, copy the example.acf.php file and make your changes
3. Add html.php (this will be where your block outputs)
4. Create your field group using ACF on the backend and export it to a file with the name fields.json
5. Add JS/CSS files to the folder and enqueue them (example.acf.php)

## Create & Export Fields

1. Go to the Wordpress admin panel -> Custom Fields -> Add New
2. Create your field group(make sure to set the Location to your new block) and click publish
3. Click Tools (next to field groups on the top of the page)
4. Select your new field group and click Export File
5. Copy the new JSON file to your new folder and rename it fields.json

## SASS

We have added a sass compiler to our blocks. You can work with SCSS and your CSS file will be created/updated. CSS will NOT be commited to Github, but will be compiled using a Github Workflow (.github/workflows/compile-scss.yml).

Follow the steps below to use SCSS on your local machine!

1. Create example.scss(can have anyname) file in the block folder (remember to enqueue example.css in acf.php)
2. Open your terminal to the /blocks root folder: /wp-content/plugins/braftonium-new/blocks
3. Run "install npm" (First time)
4. Run "npm sass-watch" (Everytime)

## Override Template

You may want to override the default template (blocks/example/html.php) in your child theme. If you have example.php in /themes/current-theme/templates/blocks/example.php it will be used instead of the default temple (blocks/example/html.php).