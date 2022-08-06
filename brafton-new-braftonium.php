<?php
/**
 * Plugin Name: New Brafton Plugin
 * Description: Custom Plugin for blocks and helper functions
 * Version: 1.0
 * Author: Brafton
 * Developers: Deryk King, James Allan, Jonathan Kowensky, Fritz Bester
 */

 //Register all acf blocks
include ("blocks/register-blocks.php");

//Include General Settings
include ("general-settings/settings.php");

//Include Template Swopper
include ("general-settings/template-overider.php");

//Include Custom Post Types
include ("general-settings/custom-posts.php");