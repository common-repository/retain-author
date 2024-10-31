<?php
/*
Plugin Name: Retain Author
Plugin URI: http://wordpress.org/extend/plugins/retain-author/
Description: Retain the author for the next and previous post links within a category
Version: 1.0
Author: David Powell
Author URI: http://www.gobiglabs.com
License: GPL2
*/
?>
<?php
/*  Copyright 2012 David Powell  (email : dcp3450@gmail.com)

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
?>
<?php
add_action( 'admin_menu', 'my_plugin_menu' );

function my_plugin_menu() {
	add_options_page( 'Retain Author', 'Retain Author', 'manage_categories', 'retainAuthorCategory', 'my_plugin_options' );
}

add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init(){
register_setting( 'plugin_options', 'categoriesToRetain' );
add_settings_section('plugin_main', 'Categories to retain under author', 'plugin_section_text', 'plugin');
add_settings_field('plugin_text_string', 'Categories (select multiple with CTRL)', 'plugin_setting_string', 'plugin', 'plugin_main');
}

function plugin_section_text() {
echo '<p>Select the categories that should also retain the author</p>';
}

function plugin_setting_string() {
	$options = get_option('plugin_options');
	$categoriesAffected = get_option('categoriesToRetain');
?>
	<table class="form-table">
	<tr valign="top">
	<td>
		<select name="categoriesToRetain[]" multiple>
		<?php 
			$categories = get_categories( array('orderby' => 'name','order' => 'ASC' ) ); 
			foreach($categories as $category){
				if(in_array($category->cat_ID,$categoriesAffected))
					echo '<option value="'.$category->cat_ID.'" selected="selected">'.$category->name.'</option>';
				else
					echo '<option value="'.$category->cat_ID.'">'.$category->name.'</option>';
			}
		?>
		</select>
	</td>
	</tr>
	</table>
<?php
}

function my_plugin_options() {
?>
	<div class="wrap">
	<h2>Retain Author</h2>
	<form method="post" action="options.php">
	<?php settings_fields('plugin_options'); ?>
	<?php do_settings_sections('plugin'); ?>
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
<?php 
	} 
	add_filter( 'get_next_post_where', 'retain_author_where', 10, 3 );
	add_filter( 'get_previous_post_where', 'retain_author_where', 10, 3 );
	function retain_author_where( $where, $in_same_cat, $excluded_categories ) {
		global $post;
		$categoriesAffected = get_option('categoriesToRetain');
		$currentCategory = get_the_category();
		$catID = $currentCategory[0]->cat_ID;
		if(in_array($catID,$categoriesAffected))
			return $where .= " AND p.post_author='" . $post->post_author . "'";
	}
?>