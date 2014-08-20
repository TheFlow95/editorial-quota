<?php
/*  Copyright 2014  TheFlow_  (email : theflow@outlook.com)

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
/**
 * Plugin Name: Editorial Quota
 * Plugin URI: http://theflow95.github.io/editorial-quota
 * Description: Wordpress Plugin to Manage Articles
 * Version: 1.0
 * Author: TheFlow_
 * Author URI: http://flow.olympe.in
 * License: GPL2
 */
Class EditorialQuota
{
	public function __construct()
	{
		add_action('admin_menu', array($this, 'add_admin_menu'));
	}

	/*public static function install()
	{
		global $wpdb;

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}editorial_quota (id INT PRIMARY KEY, quota INT NOT NULL);");
	}*/

	public function add_admin_menu()
	{
		add_options_page( 'Réglages de Editorial Quota', 'Editorial Quota', 'manage_options', 'editorial-quota', array($this, 'menu_page'));
	}

	public function menu_html()
	{
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
	}
}