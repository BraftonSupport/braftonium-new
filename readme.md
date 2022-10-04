# Brafonium Plugin

This is a plugin we have wanted to make for a while, to make our(and clients') lives easier and we thought we had to share it with everyone! (If you love ACF PRO that is). This plugin will only work if you have ACF PRO Plugin.

# General User Options

1. Blocks - We have created blocks which will work with Gutenberg, just like any other block. They will all be in the category braftonium.
2. Widget Areas - You can easily create multiple widgets with optional settings like (class, id, description). They will appear in the general Wordpress Widgets page.
3. Custom Posts & Taxonomies - Create re-usable taxonomies and multiple custom posts without any coding.
4. Debug - Debug mode will only turn on debug for administrators, so the public doesn't see funny stuff.
5. Inject custom CSS/CSS stylesheet/JS/JS file with async/defer into the header or footer.
6. Change the Wordpress Administrator without requiring an email confirmation.

# Developer Options

1. Blocks - You can override our blocks templates and fields in your child theme (See Readme in /blocks)
2. Custom Posts & Taxonomies - Create re-usable taxonomies and multiple custom posts without any coding. You will need to create your own templates, as usual.
3. Debug - Debug mode will only turn on debug for administrators, so the public doesn't see funny stuff.
4. Inject custom CSS/CSS stylesheet/JS/JS file with async/defer into the header or footer for specific pages/posts or on the entire site.
5. Swop a template for a specific audience so you can work on a template while the public see the old template, helping you avoid live problems. This is like a mini staging area.
6. Change the Wordpress Administrator without requiring an email confirmation.
7. Patterns - You can create and save patterns in your child theme.

# Brafonium Blocks

It can be a tedious task creating blocks.. You need to rewrite/remember ALOT of code. We have developed a system which speeds things up(ALOT).. and requires minimal code, while keeping the dev in full control. We have included a SCSS compiler and given you the ability to override templates in your child theme. We also enabled JSON field import so you can just export it using ACF in Wordpress itself.

# Template Swopper

You/someone else may want to override the default template (/wp-content/plugins/braftonium-new/blocks/new-block.html.php). If the template new-block.html.php is found in /themes/current-theme/braftonium/blocks/ it will be used instead of the default template.

# Custom Posts & Taxonomies

** What are Custom Post Types? **
A page and a post is a post type. Sometimes you need more post types eg. eBooks, Case Studies, Cars, Locations, Employees. 

** What are Taxonomies? **
A categry or tag are taxonomies which come standard. They are ways to group together similar or different post types. Eg. Genre/Author for custom post type Book.

You can create global taxonomies which can be used on any custom post type you create or you can create a taxonomy which is unique to a specific post type. 

See Readme file in /general-settings/

# Inject JS Scripts/Stylesheets/Fonts/JS Code Block/Inline CSS

This is a tool which helps you inject stylesheets or JS scripts, while giving you an option to pop some CSS or Javascript code right in. This is a great tool to add tracking codes, quick js/css tweaks, add fonts or libraries. You can enable and disable rules, so you do not need to keep deleting and recreating rules. Injection rules on the settings page are global (will be used on every post/page). Injection rules done on a specific post/page will be local to that page only.

# Template Swopper

This is a tool which is meant to create an environment where the developer can work on a template without the general public seeing it. If you need to work on a live template this will enable you to create a new template file to work on and redirect to, while the general public will still see the old/existing template.

# Patterns

A pattern is a block or many blocks which are reusable throughout the website. All patterns used for any blocks will be in this folder and must end with '-pattern.php'. 

# Widget Areas

Widget areas are small areas where you can add blocks to. Creating blocks in these areas is the same as if it were on a page/post. Generally widgets are used for narrower content like sidebars or segments of a footer (left footer, center footer, right footer).

# Settings

There are only General Settings which are Admin Email Override & Turn On Debug Mode for Admins only.

## Admin Email Override

If you change the admin email in this section you will not need to wait for a confirmation email to be sent and accepted, the admin email will be changed immediately without an email being sent.

## Debug Mode

This turns on debug mode, without access to wp-admin or php. Debug mode will only display errors/warnings if the user is logged in and is a site admin.

# Useful Functions

There are 3 simple functions, for now, which we find useful.

** includeForAdmin($filename) **
This will only include a php file if you are admin. This helps for working on functions which you don't wan't to crash the live site. The file will only be included on the front-end. This helps you access the backend and fix a problem which may have forced you to use cpanel/ftp.

** readingTime($postId, $makeMinutes = false, $appendTxt='') ** 
This will calculate the required reading time of a post and will either be appended with mins/minutes with the option to add another word/string. eg. 2 min read.

** consoleJS($txt) **
Just a quick easy way to output a string to the console.

## More Coming soon!

## Credits

** General Plugin ** Jonathan Kowensky (Developer, Architecture), Deryk King (Reviewer, Architecture)
** Blocks **         Deryk King, Fritz Bester, James Allen, Jonathan Kowensky