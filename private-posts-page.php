<?php
/**
 * @package PrivatePostsPage
 */
/*
Plugin Name: Private Posts Page
Plugin URI: http://plugins.svn.wordpress.org/private-posts-page
Description: Private Posts Page will force your designated posts page (blog) to behave like any normal page that is set to private visibility. That is, for non-logged in users the page will return a 404 error and the posts page title will be removed from the menu. Additionally, all posts will set as private as well. The feed will similarly be disabled and search will not return private pages in results.
Version: 1.1.0
Author: Doug Sparling
Author URI: http://www.dougsparling.org
License: GPLv2 or later
*/

/*
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

if ( !class_exists( "PrivatePostsPage" ) ) {
	class PrivatePostsPage {
		public function __construct() {
			add_action( 'template_redirect', array( $this, 'make_posts_page_private' ) , 1000 );
			add_filter( 'get_pages', array( $this, 'remove_posts_page_from_menu' ), 1000 );
			add_filter( 'wp_page_menu_args', array( $this, 'remove_home_menu_item' ), 1000 );
			//add_filter( 'widget_posts_args', array( $this, 'remove_posts_from_recent_posts_widget' ), 1000 );
		}
	
		// If user not logged, return 404 for posts page and feeds
		public function make_posts_page_private() {
			if ( !is_user_logged_in() ) {
				// Posts page/feed - return 404
				if ( is_home() || is_feed() ) {
					require TEMPLATEPATH . '/404.php';
					exit;
				}
				// Any type of post - return 404
				if ( is_single() || is_archive() || is_category() || is_tag() || is_date() || is_author() || is_search() ) {
					require TEMPLATEPATH . '/404.php';
					exit;
				}
			}
		}
	
		// If user not logged in, remove posts page from menu
		// This only pertains to a post page other than the front page
		public function remove_posts_page_from_menu( $pages ) {
			if ( !is_user_logged_in() ) {
				$postspage_id = get_option( 'page_for_posts' );
				for ( $i = 0; $i < count( $pages ); $i++ ) {
					if ( $pages[$i]->ID == $postspage_id ) {
						unset( $pages[$i] );
						break;
					}
				}
			}
			return $pages;
		}
	
		// Remove the home menu item if user not logged in
		// and user is on the home page
		public function remove_home_menu_item( $args ) {
			if ( is_user_logged_in() )
				return $args;
	
			$postspage_id = get_option( 'page_for_posts' );
			if ( !$postspage_id ) {
				$args['show_home'] = false;
			} else {
				$args['show_home'] = true;
			}
			return $args;
		}
	
		//public function remove_posts_from_recent_posts_widget( $args ) {
		//	$recent_posts = wp_get_recent_posts();
		//	$recent_posts_ids_arr = array();
		//	foreach( $recent_posts as $recent ){
		//		$recent_posts_ids_arr[] = $recent['ID'];
		//	}
		//	$args['post__not_in'] = $recent_posts_ids_arr;
		//	return $args;
		//}
	}
}

if( class_exists( "PrivatePostsPage" ) ) {
	$privatePostsPage = new PrivatePostsPage();
}

?>
