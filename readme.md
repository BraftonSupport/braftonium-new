# Brafonium Plugin

This is a plugin we have wanted to make for a while, to make our lives easier and we thought we had to share it with everyone! (If you love ACF PRO that is).
- Create: widgets, custom blocks(with sass), custom post types(unlimited taxonomies)
- Enable debug for administrators only
- Add JS/CSS to all/specific pages with options to defer/async 
- Swop a template for a specific audience so you can work on template B while the public see template A

## ACF BLOCKS

It can be a tedious task creating blocks.. You need to rewrite ALOT of code: enqueue assets, create groups and fields(with unique ids). We have developed a system which speeds things up(ALOT).. We even added a SASS compiler.. All you have to do now is follow a few simple steps:
1. Create a new block(block-name) in the blocks folder
2. Copy the 'block-settings.php' file from /blocks/example.php
3. Set: Block Name, Description & Fields
4. Create your block.php file to output your block (As per ACF guidelines it should match the folder name)
5. Add JS/CSS files(their names don't matter) to the block folder (They will automatically be enqueued thanks to register-blocks.php)

### SASS in blocks

We have added a sass compiler to our blocks. CSS will NOT be commited.

There is is a Github workflow file (/.github/workflows/compile-scss.yml). When you push to Github the SASS/SCSS files will be compiled into CSS files, with the same name as the SCSS file. This workflow will only be done when you push to Github. If you want to compile SCSS files on your local build to preview before you push, you will need to run the commands using npm.

Follow the steps below to use SCSS!

1. Create scss file in the block folder (it can have any name)
2. Open your terminal to the blocks root folder: /wp-content/plugins/braftonium-new/blocks
3. Run "install npm" (First time)
4. Run "npm sass-watch"

You have to run "npm sass-watch" before saving an scss file, then it will either create/update the css file automatically.

## Custom Posts

No dev work should be needed here. There is a repeater which let's you create multiple post types and add multiple taxonomies including: 
Categories, Tags & anything else you want.

## Add Scripts or Styles

This will give you a few different ways to inject/enqueue JS, Scripts, CSS or Stylesheets. You can add global rules for all pages or you can add rules for specific posts/pages.

### You can enqueue(Requires url):
1. Scripts (Normal, Defer, Async)
2. Stylesheets

### And you can inject (Needs to be wrapped in <script> or <style> tags):
1. Header
2. Before content
3. After content
4. Footer

## Debug

You can enable/disable the debug mode. Debug can only be enabled for the administrators, making sure the public don't see weird stuff.

## Manage Widgets

No dev work should be needed here. All you need to do is go into the Braftonium settings and enter your widget name into the repeater.
The text you enter will become sentence case as the widget title and will get a unique class assigned.

All widget classes will start with "braftonium-widget-" and the rest will be your title with all characters being lowercase and spaces/"_" converted to "-". So for "Example 1" the output will be "braftonium-widget-example-1"

## Template Swopper

Often we need to work on a template which is live OR create a duplicate page and template so we do affect the live site. The solution to this is to be able to create a new template which will swop with the intended(general/public) template.

A swopped template will have the class "template-swopped" added to the page. (This will help with custom styling while you work).

### Steps:
1. Input template name (eg. page.php - .php is not needed but won't break it) or full url (eg. https://www.yoursite.com/contact-us)
2. Relative path to new template which will be added onto /wp-content. You can pick a template in /themes or /plugins
3. Set audience - This can either be set to all or a single username or multiple usernames (no spaces eg. user_1,user_2)

You can disable a swop without deleting the rule.

## Fonts Awesome

This is just a general quick thing you can add to save some time.

## Admin Email

Change the admin email address without needing an email confirmation.

## Useful Functions

Some functions which we either need often or would just help minimize code:
1. consoleJS - Output to Inspector console
2. readingTime - Optional values: choose between min/minute and append text
3. includeForAdmin - Only include a php file if the user is admin, avoid public errors
4. templateAssets - Input active theme location for JS/CSS (/library/styles & /library/js are the presets). File must have same name as the template. 

## More Coming soon!