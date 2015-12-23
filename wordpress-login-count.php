<?php

/*
Plugin Name: WordPress User Login Counter
Plugin URI: http://sitepoint.com
Description: Count the number of times users log in to their WordPress account.
Version: 1.0
Author: Agbonghama Collins
Author URI: http://w3guy.com
License: GPL2
*/


namespace Sitepoint\WordPressPlugin;

class Login_Counter {

	public function init() {
		add_action( 'wp_login', array( $this, 'count_user_login' ), 10, 2 );
		add_filter( 'manage_users_columns', array( $this, 'add_stats_columns' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'fill_stats_columns' ), 10, 3 );
	}


	/**
	 * Save user login count to Database.
	 *
	 * @param string $user_login username
	 * @param object $user WP_User object
	 */
	public function count_user_login( $user_login, $user ) {

		$count = get_user_meta( $user->ID, 'sp_login_count', true );
		if ( ! empty( $count ) ) {
			$login_count = get_user_meta( $user->ID, 'sp_login_count', true );
			update_user_meta( $user->ID, 'sp_login_count', ( (int) $login_count + 1 ) );
		}
		else {
			update_user_meta( $user->ID, 'sp_login_count', 1 );
		}
	}


	/**
	 * Add the login stat column to WordPress user listing
	 *
	 * @param string $columns
	 *
	 * @return mixed
	 */
	public function add_stats_columns( $columns ) {
		$columns['login_stat'] = __( 'Login Count' );

		return $columns;
	}


	/**
	 * Fill the stat column with values.
	 *
	 * @param string $empty
	 * @param string $column_name
	 * @param int $user_id
	 *
	 * @return string|void
	 */
	public function fill_stats_columns( $empty, $column_name, $user_id ) {

		if ( 'login_stat' == $column_name ) {
			if ( get_user_meta( $user_id, 'sp_login_count', true ) !== '' ) {
				$login_count = get_user_meta( $user_id, 'sp_login_count', true );

				return "<strong>$login_count</strong>";
			}
			else {
				return __( 'No record found.' );
			}
		}

		return $empty;
	}


	/**
	 * Singleton class instance
	 * @return Login_Counter
	 */
	public static function get_instance() {
		static $instance;
		if ( ! isset( $instance ) ) {
			$instance = new self();
			$instance->init();
		}

		return $instance;
	}
}


Login_Counter::get_instance();