<?php

/*
Plugin Name: Flyn-Syntax
Plugin URI: http://www.flynsarmy.com
Description: Syntax highlighting using <a href="http://qbnz.com/highlighter/">GeSHi</a> supporting a wide range of popular languages.
Version: 2.0
Author: Flyn San
Author URI: http://www.flynsarmy.com
Original Author: Steven A. Zahm, Ryan McGeary
License: GPL2

Copyright 2014  Flyn San  (email : flynsarmy@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
Look at these:    http://wordpress.org/extend/plugins/wp-synhighlight/
                http://wordpress.org/extend/plugins/wp-codebox/
*/

require_once __DIR__ . "/vendor/autoload.php";

/**
 * Start the plugin.
 */
add_action(
    'plugins_loaded',
    function () {
        $flynSyntax = new FlynSyntax\FlynSyntax();
        $flynSyntax->initFilters();
    }
);
