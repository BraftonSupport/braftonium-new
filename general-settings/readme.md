# General Settings

This is were everything happens which is not connected to patterns or blocks. This is a tool box of things we find useful which help us speed up efficiency and simplify the developer and client's lives.

# Custom Posts & Taxonomies

** What are Custom Post Types? **
A page and a post is a post type. Sometimes you need more post types eg. eBooks, Case Studies, Cars, Locations, Employees. 

** What are Taxonomies? **
A categry or tag are taxonomies which come standard. They are ways to group together similar or different post types. Eg. Genre/Author for custom post type Book.

You can create global taxonomies which can be used on any custom post type you create or you can create a taxonomy which is unique to a specific post type. 

## Creating Custom Post Type

1. In Wordpress backend, go to the 'Braftonium' Menu option & select 'Custom Posts & Taxonomies'
2. Under the 'Custom Post Types' heading click 'Add Row'
3. Enter your post type title & select/create any taxonomies you want it to have.
4. Click 'Update' to save and you should see your new Custom Post Type on the main menu bar with 'Posts'

## Creating Shared Taxonomies

1. In Wordpress backend, go to the 'Braftonium' Menu option & select 'Custom Posts & Taxonomies'
2. Under the 'Create Taxonomies' heading click 'Add new choice'
3. Add choices and click 'Update'

# Inject JS Scripts/Stylesheets/Fonts/JS Code Block/Inline CSS

This is a tool which helps you inject stylesheets or JS scripts, while giving you an option to pop some CSS or Javascript code right in. This is a great tool to add tracking codes, quick js/css tweaks, add fonts or libraries. You can enable and disable rules, so you do not need to keep deleting and recreating rules. Injection rules on the settings page are global (will be used on every post/page). Injection rules done on a specific post/page will be local to that page only.

## Creating a rule

### Location
Choose wether your want the rule to connect to the Header/Footer.

### Methods
** Inline CSS **            CSS rule/s
** Stylesheet **            Actual .css stylesheet url
** JS (Script Block)**      JS code
** JS (Script)**            JS script url
** JS (Script Async)**      JS script url, using async
** JS (Script Defer)**      JS script url, using defer

### ID
Which ever method you are using, you will need to create a unique ID for the rule

### Inject Content
Depending on the method you selected, you will either input a url, Javascript / CSS

# Widget Areas

Widget areas are small areas where you can add blocks to. Creating blocks in these areas is the same as if it were on a page/post. Generally widgets are used for narrower content like sidebars or segments of a footer (left footer, center footer, right footer).

## Creating a widget

1. In Wordpress backend, go to the 'Braftonium' Menu option & select 'Widget Areas'
2. Click add row, only the widget title is required
3. You can add id, class and description if you decide you want them to have values.
4. Click 'Update'
5. In Wordpress backend, go to the 'Appearance' Menu option & select 'Widgets' (This is where you edit and design your Widgets)

# Settings

There are only General Settings which are Admin Email Override & Turn On Debug Mode for Admins only.

## Admin Email Override

If you change the admin email in this section you will not need to wait for a confirmation email to be sent and accepted, the admin email will be changed immediately without an email being sent.

## Debug Mode

This turns on debug mode, without access to wp-admin or php. Debug mode will only display errors/warnings if the user is logged in and is a site admin.

# Template Swopper

This is a tool which is meant to create an environment where the developer can work on a template without the general public seeing it. If you need to work on a live template this will enable you to create a new template file to work on and redirect to, while the general public will still see the old/existing template.

## Using Template Swopper

For this example we will discuss working on the single-page.php template on https://www.website.com/about-us.
1. Copy single-page.php or Create a new template - Give template file a unique name (single-page-new.php).
2. Copy your template to any location inside /wp-content/. It can be in a plugin/child theme.
3. In Wordpress backend, go to the 'Braftonium' Menu option & select 'Template swopper'

### Target to override
This can either be the original template filename or a specific url. So either "single-page" or "https://www.website.com/about-us".

### New Template
The file path is locked to /wp-content/. You can then choose your file location anywhere in wp-content. eg. Input '/themes/child-theme/single-page-new.php'.

### Audience
This allows you to decide who will see the new template. It will be prepopulated with your username but you can add other user's using csv without spaces eg user1,user_2,dev or you can enter 'all' for it to work for all users logged in or not.

Now you can work on a template, only your team will see until you are ready to replace the original template. 

You can disable rules when ever you want.

# Useful Functions

There are 3 simple functions, for now, which we find useful.

** includeForAdmin($filename) **
This will only include a php file if you are admin. This helps for working on functions which you don't wan't to crash the live site. The file will only be included on the front-end. This helps you access the backend and fix a problem which may have forced you to use cpanel/ftp.

** readingTime($postId, $makeMinutes = false, $appendTxt='') ** 
This will calculate the required reading time of a post and will either be appended with mins/minutes with the option to add another word/string. eg. 2 min read.

** consoleJS($txt) **
Just a quick easy way to output a string to the console.

## Credits

** Inject Scripts & Styles, General Settings **
Jonathan Kowensky (Developer, Architecture), Deryk King (Reviewer, Architecture)

** Custom Posts & Taxonomy, Manage Widgets **
Deryk King (Reviewer, Architecture), Jonathan Kowensky (Developer)

** Template Swopper, Useful Functions **
Jonathan Kowensky (Developer, Architecture), Deryk King (Reviewer)