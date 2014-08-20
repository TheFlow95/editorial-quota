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
		add_action('admin_init', array($this, 'register_settings'));
		add_action('admin_menu', array($this, 'add_admin_menu'));

		if ( get_option( 'eq_role' ) == false ) {
			update_option( 'eq_role', 'author' );
		}
		if ( get_option( 'eq_quota' ) == false ) {
			update_option( 'eq_quota', '10' );
		}
	}

	public function register_settings()
	{
	    register_setting('eq_settings', 'eq_role');
		register_setting('eq_settings', 'eq_quota');
		add_settings_section('eq_section', '', array($this, 'section_form'), 'eq_settings');
		add_settings_field('eq_role', 'Authors\' role', array($this, 'role_form'), 'eq_settings', 'eq_section');
		add_settings_field('eq_quota', 'Authors\' quota', array($this, 'quota_form'), 'eq_settings', 'eq_section');
	}

	public function section_form()
	{
		echo '<p>Select the role to which to apply the quota and the posts\' quota to perform by month</p>';
	}
	
	public function role_form()
	{
	    echo '<select>';
	    wp_dropdown_roles(get_option('eq_role'));
	    echo '</select>';
	}

	public function quota_form()
	{
		echo '<input id="eq_quota" type="number" min="0" class="regular-text" name="eq_quota"  value="'.get_option('eq_quota').'">';
	}

	public function add_admin_menu()
	{
		add_options_page( 'Editorial Quota Options', 'Editorial Quota', 'manage_options', 'editorial-quota', array($this, 'menu_html') );
		add_management_page( 'Quota', 'Quota', 'publish_posts', 'quota', array($this, 'quota_html') );
	}

	public function menu_html()
	{
		echo '<div class="wrap">';
		echo '<h2>'.get_admin_page_title().'</h2>';
		echo '<form method="post" action="options.php">';
		settings_fields('eq_settings');
		do_settings_sections('eq_settings');
		submit_button();
		echo '</form>';
		echo '</div>';
	}
	
	public function quota_html()
	{
		echo '<div class="wrap">';
		echo '<h2>'.get_admin_page_title().'</h2>';
		echo '<p>Welcome to the homepage of Editorial Quota</p>';
		echo '</div>';
	}
}

new EditorialQuota();