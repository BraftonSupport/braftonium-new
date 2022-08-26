# Brafonium Plugin

This is a plugin we have wanted to make for a while, to make our(and clients') lives easier and we thought we had to share it with everyone! (If you love ACF PRO that is). This plugin will only work if you have ACF PRO Plugin.

## General User Options

1. Blocks - We have created blocks which will work with Gutenberg, just like any other block. They will all be in the category braftonium.
2. Widget Areas - You can easily create multiple widgets with optional settings like (class, id, description). They will appear in the general Wordpress Widgets page.
3. Custom Posts & Taxonomies - Create re-usable taxonomies and multiple custom posts without any coding.
4. Debug - Debug mode will only turn on debug for administrators, so the public doesn't see funny stuff.
5. Inject custom CSS/CSS stylesheet/JS/JS file with async/defer into the header or footer.
6. Change the Wordpress Administrator without requiring an email confirmation.

## Developer Options

1. Blocks - You can override our blocks templates in your child theme (See Readme in /blocks)
2. Custom Posts & Taxonomies - Create re-usable taxonomies and multiple custom posts without any coding. You will need to create your own templates, as usual.
3. Debug - Debug mode will only turn on debug for administrators, so the public doesn't see funny stuff.
4. Inject custom CSS/CSS stylesheet/JS/JS file with async/defer into the header or footer for specific pages/posts or on the entire site.
5. Swop a template for a specific audience so you can work on a template while the public see the old template, helping you avoid live problems. This is like a mini staging area.
6. Change the Wordpress Administrator without requiring an email confirmation.

## Brafonium Blocks

### Overview

It can be a tedious task creating blocks.. You need to rewrite/remember ALOT of code. We have developed a system which speeds things up(ALOT).. and requires minimal code, while keeping the dev in full control. We have included a SCSS compiler and given you the ability to override templates in your child theme. We also enabled JSON field import so you can just export it using ACF in Wordpress itself.

### Example

There is an example block in folder /example. Use this, and it's comments to guide you. This guide will be based on the New Block.

### Creating a block

1. Create a new folder in /blocks/new-block
2. Copy the example.acf.php file to new-block and rename it new-block.html.php & delete example features you won't be using
3. Edit All block fields like: title, description, assets, render_callback (must match braftonium_blocks_new_block_template)
4. Rename braftonium_blocks_example_template function to braftonium_blocks_new_block_template (and update as callback in)
5. Create all your asset files/libraries & enqueue them.
6. Create new-block.html.php file to output the block.
7. On Wordpress backend in ACF, create your field group, click tools and export it to a file with the name new-block-fields.json

### Create & Export Fields

1. Go to the Wordpress admin panel -> Custom Fields -> Add New
2. Create your field group(make sure to set the Location to your new block) and click publish
3. Click Tools (next to field groups on the top of the page)
4. Select your new field group and click Export File
5. Copy the new JSON file to your new folder and rename it fields.json

### SASS

We have added a sass compiler to our blocks. You can work with SCSS and your CSS file will be created/updated. CSS will NOT be commited to Github, but will be compiled using a Github Workflow (.github/workflows/compile-scss.yml). 

Follow the steps below to use SCSS on your local machine!

1. Create new-block.scss (sass will compile any file name) file in the block folder (remember to enqueue new-block.css)
2. Open your terminal to the folder: /wp-content/plugins/braftonium-new/blocks
3. Run "install npm" (First time)
4. Run "npm sass-watch" (Everytime)
* You do not need to create the CSS file first, the compiler will do it

### Override Template

You/someone else may want to override the default template (/wp-content/plugins/braftonium-new/blocks/new-block.html.php). If the template new-block.html.php is found in /themes/current-theme/braftonium/blocks/ it will be used instead of the default template.

## Custom Posts & Taxonomies

No dev work should be needed here. 

There is a repeater which let's you create multiple post types and add multiple taxonomies. You will have to create your own templates. (https://wphierarchy.com/)

## Add Scripts or Styles

This will give you a few different ways to inject/enqueue JS, Scripts, CSS or Stylesheets. Add global rules for all pages or you can add rules for specific posts/pages. Options include:
1. Location: Header/Footer
2. Method Files: CSS stylesheet/JS Async/JS Defer
3. Method Inline: Type actual JS/CSS
4. Give each rule a unique id
3. Disable: Disable any rule, without having to delete it.
4. Content: This will either be your JS/CSS/URL

## Debug

You can enable/disable the debug mode. Debug can only be enabled for the administrators, making sure the public don't see weird stuff.

## Widget Areas

No dev work should be needed here. All you need to do is go into the Braftonium settings and enter your widget name and it will appear in the general Wordpress Widgets page. Optional settings include:
1. Name (required)
2. Class
3. ID
4. Description

## Template Swopper

Often we need to work on a template which is live OR create a duplicate page and template so we do affect the live site. The solution to this is to be able to create a new template which will swop with the intended(general/public) template. You can make the template swop by selecting the following options:
1. Target Template - This can be a template file name, a post/page name or a full url.
2. Audience - Who should it swop for? Choose between everyone, just you or a few other users.
3. New Template - This can be anywhere in /wp-content. We recommend you use a child theme or custom plugin.

A swopped template will have the class "dev-template" added to the page. (This will help with custom styling while you work).

### Steps:
1. Input template name (eg. page.php - .php is not needed but won't break it) or full url (eg. https://www.yoursite.com/contact-us)
2. Relative path to new template which will be added onto /wp-content. You can pick a template in /themes or /plugins
3. Set audience - This can either be set to all or a single username or multiple usernames (no spaces eg. user_1,user_2)

You can disable a swop without deleting the rule.

## Admin Email

Change the admin email address without needing an email confirmation. Saving confirmation and email not sending issues.

## Useful Functions

Some functions which we either need often or would just help minimize code:
1. consoleJS - Output to Inspector console
2. readingTime - Optional values: choose between min/minute and append text
3. includeForAdmin - Only include a php file if the user is admin, avoid public errors

## More Coming soon!