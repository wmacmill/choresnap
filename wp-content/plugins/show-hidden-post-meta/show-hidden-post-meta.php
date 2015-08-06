<?php
/*
Plugin Name: Show Hidden Post Meta
Plugin URI: http://wordpress.org/plugins/show-hidden-post-meta/
Description: Makes hidden custom fields (post meta) visible on the post edit screen
Author: Philip Newcomer
Version: 1.0
Author URI: http://philipnewcomer.net
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright (C) 2014  Philip Newcomer

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Filters is_protected_meta to always show hidden custom fields
 */
add_filter( 'is_protected_meta', '__return_false', 999 );
