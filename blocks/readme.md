# Brafonium Blocks

## Overview

It can be a tedious task creating blocks.. You need to rewrite/remember ALOT of code. We have developed a system which speeds things up(ALOT).. and requires minimal code, while keeping the dev in full control. We have included a SCSS compiler and given you the ability to override templates in your child theme. We also enabled JSON field import so you can just export it using ACF in Wordpress itself.

## Creating a block

== Initial Setup ==
Create a new folder in /blocks/new-block

== Initialize Block ==
1. Create a new folder in /blocks/new-block
2. Copy the example.acf.php file to new-block and rename it new-block.html.php
3. Delete example features you won't be using
4. Edit All block fields like: title, description, assets & render_callback

== Assets ==
1. Add all required assets/libraries like JS, Fonts, CSS, JSON, SVG, Images etc.
2. Maintain naming convention for assets key to the block Ie. Main styling/JS files should also have the name eg. new-block.js

== Styling ==
Create your styling using SCSS and run sass compiler and maintain naming convention eg. new-block.scss

== SCSS Compiler ==
1. Open blocks folder in CLI Terminal
2. First Time Use Only: Run 'npm install'
3. Every Time: Run 'npm sass-watch'
You have run 'npm install' for the sass compiler to work. You MUST run 'npm sass-watch' every time you work on styling, and the css file of the same name will be generated. You can work with SCSS and your CSS file will be created/updated. CSS will NOT be commited to Github, but will be compiled using a Github Workflow (.github/workflows/compile-scss.yml). 

== Callback Template ==
The 'render_callback' function in blocks.php will automatically add your template and allow you to override the template in your child theme. You template file MUST match the folder name eg. new-block.html.php for 'render_callback' to work.
1. Copy example.html.php to block folder new-block and rename it to match the folder name eg. new-block.html.php
2. Remove any features you don't need and make this template your own.

== Fields ==
1. Create all fields using Advanced Custom Fields in Wordpress Admin backend.
2. Click 'Tools' -> select the field Group for this new block -> click 'Export File'.
3. Copy JSON file to block folder -> rename file to match block folder name and add '-fields' eg. new-block-fields.json

## Overriding Template/Fields

== Override Block Template ==
1. Create the folder '/braftonium/' in your child theme then create the folder '/blocks/'.
2. Create a new template with the correct naming structure so it can target which template to override.
To override example.html.php template, create a file with same name so the file path is '/child-theme/braftonium/blocks/example.html.php'

== Override Block Fields ==
1. Create the folder '/braftonium/' in your child theme then create the folder '/blocks/'.
2. Create a new JSON file using all steps for creating a fields file, ensure name of the JSON file matches the block to override. 
3. To override example-fields.json, the file path is '/child-theme/braftonium/blocks/example-fields.json'

# Credits

** blocks.php ** 
Jonathan Kowensky (Developer, Architecture), Deryk King (Reviewer, Architecture)

** Example Block **
Jonathan Kowensky (Developer, Architecture), Deryk King (Reviewer, Architecture)

** Custom Row Block **
Deryk King (Developer, Architecture), Jonathan Kowensky (Reviewer)

** Custom List, Content List, Slider Blocks **
Fritz Bester (Developer), Deryk King (Architecture, Reviewer), Jonathan Kowensky (Reviewer, Architecture)

** Google Maps, CTA Blocks **
James Allen (Developer), Deryk King (Architecture, Reviewer), Jonathan Kowensky (Reviewer, Architecture)