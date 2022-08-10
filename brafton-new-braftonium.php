<?php
/**
 * Plugin Name: New Brafton Plugin
 * Description: Custom Plugin for blocks and helper functions
 * Version: 1.0
 * Developers: Jonathan Kowensky, Deryk King, James Allan, Fritz Bester
 * Website: https://www.brafton.com
 * Requires: ACF Pro(https://www.advancedcustomfields.com/) & NPM if using Sass(optional)
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

 //Register all acf blocks
include ("blocks/register-blocks.php");

//Include General Settings - (General, Custom Posts, Template Swopper, Style/Script Injection)
include ("general-settings/settings.php");

//Include useful functions
include("general-settings/useful-functions.php");