<?php
/**
 * Plugin Name: Editorial Quota
 * Plugin URI: http://theflow95.github.io/editorial-quota
 * Description: Wordpress Plugin to Manage Posts' Redaction
 * Version: 1.0
 * Author: TheFlow_
 * Author URI: http://flow.olympe.in
 * License: GPL2
 */

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

Class EditorialQuota
{
	public function __construct()
	{
		add_action('admin_init', array($this, 'register_settings'));
		add_action('admin_menu', array($this, 'add_admin_menu'));

		// Default role value
		if ( get_option( 'eq_role' ) == false ) {
			update_option( 'eq_role', array('author') );
		}
		// Default quota value
		if ( get_option( 'eq_quota' ) == false ) {
			update_option( 'eq_quota', '10' );
		}
	}
	
	// Custom Wordpress wp_dropdown_roles() function
	private function wp_dropdown_multiple_roles( $selected = false ) {
		$p = '';
		$r = '';
	 
		$editable_roles = array_reverse( get_editable_roles() );
	 
		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role($details['name'] );
			if ( is_array($selected) AND in_array($role,$selected) ) // preselect specified role
				$p .= "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
			else
				$r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
		}
		echo $p . $r;
	}
	
	// Custom Wordpress count_user_posts() function
	private function count_user_posts_by_month()
	{
		global $wpdb;
		
		$userid = wp_get_current_user()->ID;
	
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = $userid AND post_type = 'post' AND (post_status = 'publish' OR post_status = 'private' OR post_status = 'future') AND MONTH(post_date) = MONTH(NOW())" );
		return apply_filters( 'get_usernumposts', $count, $userid );
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
		echo '<select name="eq_role[]" multiple>';
		$this->wp_dropdown_multiple_roles(get_option('eq_role'));
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
		?>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="<?php echo plugins_url( 'js/jquery.knob.js' , __FILE__ ); ?>"></script>
		<div class="wrap">
		<h2><?php echo get_admin_page_title(); ?></h2>
		<p>Welcome to the homepage of Editorial Quota.</p>
		<?php
		// Pour afficher l'erreur
		$match = 0;
		foreach (get_option( 'eq_role' ) as $role) {
			if(current_user_can($role)) {
				$match = 1;
				?>
				<p>Goal completion:</p>
				<input type="text" value="<?php echo $this->count_user_posts_by_month()*get_option( 'eq_quota' ); ?>" class="knob" data-thickness=".2" data-skin="tron" data-readOnly=true style="box-shadow:none">
				<script>
				$(function() {
					$(".knob").knob();
				});
				</script>
				<p>Posts remaining to reach the goal:</p>
				<input type="text" value="<?php $remain = get_option( 'eq_quota' )-$this->count_user_posts_by_month(); if ($remain < 0) { echo '0'; } else { echo $remain; } ?>" class="knob2" data-thickness=".2" data-skin="tron" data-readOnly=true data-max="<?php echo get_option( 'eq_quota' ); ?>" style="box-shadow:none">
				<script>
				$(function() {
					$(".knob2").knob();
				});
				</script>
				<?php
			}
		}
		
		if(!$match) {
			?>
			<p>You don't have any goal to reach.</p>
			<?php
		}
		?>
		</div>
		<?php
	}
}

new EditorialQuota();