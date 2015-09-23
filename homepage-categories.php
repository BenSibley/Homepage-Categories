<?php
/*
Plugin Name: Homepage Categories
Version: 0.1
Plugin URI: https://www.competethemes.com/homepage-categories-plugin/
Description: Get complete control over what categories are shown on your Blog. Easily exclude categories and display just one, or many.
Author: Compete Themes
Author URI: https://www.competethemes.com
Text Domain: homepage-categories
Domain Path: /languages
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Homepage Categories WordPress Plugin, Copyright 2015 Compete Themes
Homepage Categories is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// prevent direct access
defined( 'ABSPATH' ) OR exit;

// require class file
require_once( plugin_dir_path( __FILE__ ) . 'homepage-categories-class.php' );

// initiate class
Homepage_Categories_Class::get_instance();