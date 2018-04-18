<?php

// test functions in wp-includes/user.php
/**
 * @group user
 */
class TestWPUser extends WP_UnitTestCase {

	protected $_deprecated_errors = array();

	function setUp() {
		parent::setUp();
		$this->_deprecated_errors = array();
	}

	public function deprecated_handler( $function, $message, $version ) {
		$this->_deprecated_errors[] = array(
			'function' => $function,
			'message'  => $message,
			'version'  => $version
		);
	}

	function test_get_users_of_blog() {
		// add one of each user role
		$user_role = array();
		foreach ( array('administrator', 'editor', 'author', 'contributor', 'subscriber' ) as $role ) {
			$id = $this->factory->user->create( array( 'role' => $role ) );
			$user_role[ $id ] = $role;
		}

		$user_list = get_users_of_blog();

		// find the role of each user as returned by get_users_of_blog
		$found = array();
		foreach ( $user_list as $user ) {
			// only include the users we just created - there might be some others that existed previously
			if ( isset( $user_role[$user->user_id] ) ) {
				$roles = array_keys( unserialize( $user->meta_value ) );
				$found[ $user->user_id ] = $roles[0];
			}
		}

		// make sure every user we created was returned
		$this->assertEquals($user_role, $found);
	}

	// simple get/set tests for user_option functions
	function test_user_option() {
		$key = rand_str();
		$val = rand_str();

		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );

		// get an option that doesn't exist
		$this->assertFalse(get_user_option($key, $user_id));

		// set and get
		update_user_option( $user_id, $key, $val );
		$this->assertEquals( $val, get_user_option($key, $user_id) );

		// change and get again
		$val2 = rand_str();
		update_user_option( $user_id, $key, $val2 );
		$this->assertEquals( $val2, get_user_option($key, $user_id) );

	}

	// simple tests for usermeta functions
	function test_usermeta() {

		$key = rand_str();
		$val = rand_str();

		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );

		// get a meta key that doesn't exist
		$this->assertEquals( '', get_usermeta($user_id, $key) );

		// set and get
		update_usermeta( $user_id, $key, $val );
		$this->assertEquals( $val, get_usermeta($user_id, $key) );

		// change and get again
		$val2 = rand_str();
		update_usermeta( $user_id, $key, $val2 );
		$this->assertEquals( $val2, get_usermeta($user_id, $key) );

		// delete and get
		delete_usermeta( $user_id, $key );
		$this->assertEquals( '', get_usermeta($user_id, $key) );

		// delete by key AND value
		update_usermeta( $user_id, $key, $val );
		// incorrect key: key still exists
		delete_usermeta( $user_id, $key, rand_str() );
		$this->assertEquals( $val, get_usermeta($user_id, $key) );
		// correct key: deleted
		delete_usermeta( $user_id, $key, $val );
		$this->assertEquals( '', get_usermeta($user_id, $key) );

	}

	// test usermeta functions in array mode
	function test_usermeta_array() {
		// some values to set
		$vals = array(
			rand_str() => 'val-'.rand_str(),
			rand_str() => 'val-'.rand_str(),
			rand_str() => 'val-'.rand_str(),
		);

		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );

		// there is already some stuff in the array
		$this->assertTrue(is_array(get_usermeta($user_id)));

		foreach ($vals as $k=>$v)
			update_usermeta( $user_id, $k, $v );

		// get the complete usermeta array
		$out = get_usermeta($user_id);

		// for reasons unclear, the resulting array is indexed numerically; meta keys are not included anywhere.
		// so we'll just check to make sure our values are included somewhere.
		foreach ($vals as $v)
			$this->assertTrue(in_array($v, $out));

		// delete one key and check again
		$key_to_delete = array_pop(array_keys($vals));
		delete_usermeta($user_id, $key_to_delete);
		$out = get_usermeta($user_id);
		// make sure that key is excluded from the results
		foreach ($vals as $k=>$v) {
			if ($k == $key_to_delete)
				$this->assertFalse(in_array($v, $out));
			else
				$this->assertTrue(in_array($v, $out));
		}
	}

	// Test property magic functions for property get/set/isset.
	function test_user_properties() {
		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$user = new WP_User( $user_id );

		foreach ( $user->data as $key => $data ) {
			$this->assertEquals( $data, $user->$key );
		}

		$this->assertTrue( isset( $user->$key ) );
		$this->assertFalse( isset( $user->fooooooooo ) );

		$user->$key = 'foo';
		$this->assertEquals( 'foo', $user->$key );
		$this->assertEquals( 'foo', $user->data->$key );  // This will fail with WP < 3.3

		foreach ( (array) $user as $key => $value ) {
			$this->assertEquals( $value, $user->$key );
		}
	}
	
	/**
	 * Test the magic __unset method
	 *
	 * @ticket 20043
	 */
	public function test_user_unset() {
		// New user
		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$user = new WP_User( $user_id );

		// Test custom fields
		$user->customField = 123;
		$this->assertEquals( $user->customField, 123 );
		unset( $user->customField );
		$this->assertFalse( isset( $user->customField ) );

		// Test 'id' (lowercase)
		add_action( 'deprecated_argument_run', array( $this, 'deprecated_handler' ), 10, 3 );
		unset( $user->id );
		$this->assertCount( 1, $this->_deprecated_errors );
		$this->assertEquals( 'WP_User->id', $this->_deprecated_errors[0]['function'] );
		$this->assertEquals( '2.1', $this->_deprecated_errors[0]['version'] );
		remove_action( 'deprecated_argument_run', array( $this, 'deprecated_handler' ), 10, 3);

		// Test 'ID'
		$this->assertNotEmpty( $user->ID );
		unset( $user->ID );
		$this->assertEmpty( $user->ID );		
	}

	// Test meta property magic functions for property get/set/isset.
	function test_user_meta_properties() {
		global $wpdb;

		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$user = new WP_User( $user_id );

		update_user_option( $user_id, 'foo', 'foo', true );

		$this->assertTrue( isset( $user->foo ) );

		$this->assertEquals( 'foo', $user->foo );
	}

	function test_id_property_back_compat() {
		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$user = new WP_User( $user_id );

		$this->assertTrue( isset( $user->id ) );
		$this->assertEquals( $user->ID, $user->id );
		$user->id = 1234;
		$this->assertEquals( $user->ID, $user->id );
	}

	/**
	 * ticket 19265
	 */
	function test_user_level_property_back_compat() {
		$roles = array(
			'administrator' => 10,
			'editor' => 7,
			'author' => 2,
			'contributor' => 1,
			'subscriber' => 0,
		);

		foreach ( $roles as $role => $level ) {
			$user_id = $this->factory->user->create( array( 'role' => $role ) );
			$user = new WP_User( $user_id );

			$this->assertTrue( isset( $user->user_level ) );
			$this->assertEquals( $level, $user->user_level );
		}
	}

	function test_construction() {
		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );

		$user = new WP_User( $user_id );
		$this->assertInstanceOf( 'WP_User', $user );
		$this->assertEquals( $user_id, $user->ID );

		$user2 = new WP_User( 0,  $user->user_login );
		$this->assertInstanceOf( 'WP_User', $user2 );
		$this->assertEquals( $user_id, $user2->ID );
		$this->assertEquals( $user->user_login, $user2->user_login );

		$user3 = new WP_User();
		$this->assertInstanceOf( 'WP_User', $user3 );
		$this->assertEquals( 0, $user3->ID );
		$this->assertFalse( isset( $user3->user_login ) );

		$user3->init( $user->data );
		$this->assertEquals( $user_id, $user3->ID );

		$user4 = new WP_User( $user->user_login );
		$this->assertInstanceOf( 'WP_User', $user4 );
		$this->assertEquals( $user_id, $user4->ID );
		$this->assertEquals( $user->user_login, $user4->user_login );

		$user5 = new WP_User( null, $user->user_login );
		$this->assertInstanceOf( 'WP_User', $user5 );
		$this->assertEquals( $user_id, $user5->ID );
		$this->assertEquals( $user->user_login, $user5->user_login );
	}

	function test_get() {
		$user_id = $this->factory->user->create( array(
			'role' => 'author',
			'user_login' => 'test_wp_user_get',
			'user_pass' => 'password',
			'user_email' => 'test@test.com',
		) );

		$user = new WP_User( $user_id );
		$this->assertEquals( 'test_wp_user_get', $user->get( 'user_login' ) );
		$this->assertEquals( 'test@test.com', $user->get( 'user_email' ) );
		$this->assertEquals( 0, $user->get( 'use_ssl' ) );
		$this->assertEquals( '', $user->get( 'field_that_does_not_exist' ) );

		update_user_meta( $user_id, 'dashed-key', 'abcdefg' );
		$this->assertEquals( 'abcdefg', $user->get( 'dashed-key' ) );
	}

	function test_has_prop() {
		$user_id = $this->factory->user->create( array(
			'role' => 'author',
			'user_login' => 'test_wp_user_has_prop',
			'user_pass' => 'password',
			'user_email' => 'test2@test.com',
		) );

		$user = new WP_User( $user_id );
		$this->assertTrue( $user->has_prop( 'user_email') );
		$this->assertTrue( $user->has_prop( 'use_ssl' ) );
		$this->assertFalse( $user->has_prop( 'field_that_does_not_exist' ) );

		update_user_meta( $user_id, 'dashed-key', 'abcdefg' );
		$this->assertTrue( $user->has_prop( 'dashed-key' ) );
	}

	function test_update_user() {
		$user_id = $this->factory->user->create( array(
			'role' => 'author',
			'user_login' => 'test_wp_update_user',
			'user_pass' => 'password',
			'user_email' => 'test3@test.com',
		) );
		$user = new WP_User( $user_id );

		update_user_meta( $user_id, 'description', 'about me' );
		$this->assertEquals( 'about me', $user->get( 'description' ) );

		$user_data = array( 'ID' => $user_id, 'display_name' => 'test user' );
		wp_update_user( $user_data );

		$user = new WP_User( $user_id );
		$this->assertEquals( 'test user', $user->get( 'display_name' ) );

		// Make sure there is no collateral damage to fields not in $user_data
		$this->assertEquals( 'about me', $user->get( 'description' ) );

		// Test update of fields in _get_additional_user_keys()
		$user_data = array( 'ID' => $user_id, 'use_ssl' => 1, 'show_admin_bar_front' => 1,
						   'rich_editing' => 1, 'first_name' => 'first', 'last_name' => 'last',
						   'nickname' => 'nick', 'comment_shortcuts' => 1, 'admin_color' => 'classic',
						   'description' => 'describe', 'aim' => 'aim', 'yim' => 'yim', 'jabber' => 'jabber' );
		wp_update_user( $user_data );

		$user = new WP_User( $user_id );
		foreach ( $user_data as $key => $value )
			$this->assertEquals( $value, $user->get( $key ), $key );
	}

	/**
	 * Test that usermeta cache is cleared after user deletion.
	 *
	 * @ticket 19500
	 */
	function test_get_blogs_of_user() {
		// Logged out users don't have blogs.
		$this->assertEquals( array(), get_blogs_of_user( 0 ) );

		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$blogs = get_blogs_of_user( $user_id );
		$this->assertEquals( array( 1 ), array_keys( $blogs ) );

		// Non-existent users don't have blogs.
		if ( is_multisite() )
			wpmu_delete_user( $user_id );
		else
			wp_delete_user( $user_id );
		$this->assertEquals( array(), get_blogs_of_user( $user_id ) );
	}

	/**
	 * Test that usermeta cache is cleared after user deletion.
	 *
	 * @ticket 19500
	 */
	function test_is_user_member_of_blog() {
		$old_current = get_current_user_id();

		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );

		$this->assertTrue( is_user_member_of_blog() );
		$this->assertTrue( is_user_member_of_blog( 0, 0 ) );
		$this->assertTrue( is_user_member_of_blog( 0, get_current_blog_id() ) );
		$this->assertTrue( is_user_member_of_blog( $user_id ) );
		$this->assertTrue( is_user_member_of_blog( $user_id, get_current_blog_id() ) );

		// Will only remove the user from the current site in multisite; this is desired
		// and will achieve the desired effect with is_user_member_of_blog().
		wp_delete_user( $user_id );

		$this->assertFalse( is_user_member_of_blog( $user_id ) );
		$this->assertFalse( is_user_member_of_blog( $user_id, get_current_blog_id() ) );

		wp_set_current_user( $old_current );
	}

	/**
	 * ticket 19595
	 */
	function test_global_userdata() {
		global $userdata, $wpdb;

		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );

		$this->assertNotEmpty( $userdata );
		$this->assertInstanceOf( 'WP_User', $userdata );
		$this->assertEquals( $userdata->ID, $user_id );
		$prefix = $wpdb->get_blog_prefix();
		$cap_key = $prefix . 'capabilities';
		$this->assertTrue( isset( $userdata->$cap_key ) );
	}

	/**
	 * ticket 19769
	 */
	function test_global_userdata_is_null_when_logged_out() {
		global $userdata;
		wp_set_current_user(0);
		$this->assertNull( $userdata );
	}

	function test_exists() {
		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$user = new WP_User( $user_id );

		$this->assertTrue( $user->exists() );

		$user = new WP_User( 123456789 );

		$this->assertFalse( $user->exists() );

		$user = new WP_User( 0 );

		$this->assertFalse( $user->exists() );
	}

	function test_global_authordata() {
		global $authordata, $id;

		$old_post_id = $id;

		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$user = new WP_User( $user_id );

		$post = array(
			'post_author' => $user_id,
			'post_status' => 'publish',
			'post_content' => rand_str(),
			'post_title' => rand_str(),
			'post_type' => 'post'
		);

		// insert a post and make sure the ID is ok
		$post_id = wp_insert_post( $post );
		$this->assertTrue( is_numeric( $post_id ) );

		setup_postdata( get_post( $post_id ) );

		$this->assertNotEmpty( $authordata );
		$this->assertInstanceOf( 'WP_User', $authordata );
		$this->assertEquals( $authordata->ID, $user_id );

		setup_postdata( get_post( $old_post_id ) );
	}

	function test_delete_user() {
		$user_id = $this->factory->user->create( array( 'role' => 'author' ) );
		$user = new WP_User( $user_id );

		$post = array(
			'post_author' => $user_id,
			'post_status' => 'publish',
			'post_content' => rand_str(),
			'post_title' => rand_str(),
			'post_type' => 'post',
		);

		// insert a post and make sure the ID is ok
		$post_id = wp_insert_post($post);
		$this->assertTrue(is_numeric($post_id));
		$this->assertTrue($post_id > 0);

		$post = get_post( $post_id );
		$this->assertEquals( $post_id, $post->ID );

		$post = array(
			'post_author' => $user_id,
			'post_status' => 'publish',
			'post_content' => rand_str(),
			'post_title' => rand_str(),
			'post_type' => 'nav_menu_item',
		);

		// insert a post and make sure the ID is ok
		$nav_id = wp_insert_post($post);
		$this->assertTrue(is_numeric($nav_id));
		$this->assertTrue($nav_id > 0);

		$post = get_post( $nav_id );
		$this->assertEquals( $nav_id, $post->ID );

		wp_delete_user( $user_id );
		$user = new WP_User( $user_id );
		if ( is_multisite() )
			$this->assertTrue( $user->exists() );
		else
			$this->assertFalse( $user->exists() );

		$this->assertNotNull( get_post( $post_id ) );
		$this->assertEquals( 'trash', get_post( $post_id )->post_status );
		// nav_menu_item is delete_with_user = false so the nav post should remain published.
		$this->assertNotNull( get_post( $nav_id ) );
		$this->assertEquals( 'publish', get_post( $nav_id )->post_status );
		wp_delete_post( $nav_id, true );
		$this->assertNull( get_post( $nav_id ) );
		wp_delete_post( $post_id, true );
		$this->assertNull( get_post( $post_id ) );
	}
}
