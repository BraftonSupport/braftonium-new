# Brafonium Blocks

## Overview

It can be a tedious task creating blocks.. You need to rewrite/remember ALOT of code. We have developed a system which speeds things up(ALOT).. and requires minimal code, while keeping the dev in full control. We have included a SCSS compiler and given you the ability to override templates in your child theme. We also enabled JSON field import so you can just export it using ACF in Wordpress itself.

## Example

There is an example block in folder /example. Use this, and it's comments to guide you. This guide will be based on the New Block.

## Creating a block

1. Create a new folder in /blocks/new-block
2. Copy the example.acf.php file to new-block and rename it new-block.html.php & delete example features you won't be using
3. Edit All block fields like: title, description, assets, render_callback (must match braftonium_blocks_new_block_template)
4. Rename braftonium_blocks_example_template function to braftonium_blocks_new_block_template (and update as callback in)
5. Create all your asset files/libraries & enqueue them.
6. Create new-block.html.php file to output the block.
7. On Wordpress backend in ACF, create your field group, click tools and export it to a file with the name new-block-fields.json

## Create & Export Fields

1. Go to the Wordpress admin panel -> Custom Fields -> Add New
2. Create your field group(make sure to set the Location to your new block) and click publish
3. Click Tools (next to field groups on the top of the page)
4. Select your new field group and click Export File
5. Copy the new JSON file to your new folder and rename it fields.json

## SASS

We have added a sass compiler to our blocks. You can work with SCSS and your CSS file will be created/updated. CSS will NOT be commited to Github, but will be compiled using a Github Workflow (.github/workflows/compile-scss.yml). 

Follow the steps below to use SCSS on your local machine!

1. Create new-block.scss (sass will compile any file name) file in the block folder (remember to enqueue new-block.css)
2. Open your terminal to the folder: /wp-content/plugins/braftonium-new/blocks
3. Run "install npm" (First time)
4. Run "npm sass-watch" (Everytime)
* You do not need to create the CSS file first, the compiler will do it

## Override Template

You/someone else may want to override the default template (/wp-content/plugins/braftonium-new/blocks/new-block.html.php). If the template new-block.html.php is found in /themes/current-theme/braftonium/blocks/ it will be used instead of the default template.