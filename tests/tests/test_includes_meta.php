<?php

/**
 * @group meta
 */
class WPTestIncludesMeta extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
		$this->author = new WP_User( $this->factory->user->create( array( 'role' => 'author' ) ) );
		$this->meta_id = add_metadata( 'user', $this->author->ID, 'meta_key', 'meta_value' );
		$this->delete_meta_id = add_metadata( 'user', $this->author->ID, 'delete_meta_key', 'delete_meta_value' );
	}

	function _meta_sanitize_cb ( $meta_value, $meta_key, $meta_type ) {
		return 'sanitized';
	}

	function test_sanitize_meta() {
		$meta = sanitize_meta( 'some_meta', 'unsanitized', 'post' );
		$this->assertEquals( 'unsanitized', $meta );

		register_meta( 'post', 'some_meta', array( &$this, '_meta_sanitize_cb' ) );
		$meta = sanitize_meta( 'some_meta', 'unsanitized', 'post' );
		$this->assertEquals( 'sanitized', $meta );
	}

	function test_delete_metadata_by_mid() {
		// Let's try and delete a non-existing ID, non existing meta
		$this->assertFalse( delete_metadata_by_mid( 'user', 0 ) );
		$this->assertFalse( delete_metadata_by_mid( 'non_existing_meta', $this->delete_meta_id ) );

		// Now let's delete the real meta data
		$this->assertTrue( delete_metadata_by_mid( 'user', $this->delete_meta_id ) );

		// And make sure it's been deleted
		$this->assertFalse( get_metadata_by_mid( 'user', $this->delete_meta_id ) );

		// Make sure the caches are cleared
		$this->assertFalse( (bool) get_user_meta( $this->author->ID, 'delete_meta_key' ) );
	}

	function test_update_metadata_by_mid() {
		// Setup
		$meta = get_metadata_by_mid( 'user', $this->meta_id );

		// Update the meta value
		$this->assertTrue( update_metadata_by_mid( 'user', $this->meta_id, 'meta_new_value' ) );
		$meta = get_metadata_by_mid( 'user', $this->meta_id );
		$this->assertEquals( 'meta_new_value', $meta->meta_value );

		// Update the meta value
		$this->assertTrue( update_metadata_by_mid( 'user', $this->meta_id, 'meta_new_value', 'meta_new_key' ) );
		$meta = get_metadata_by_mid( 'user', $this->meta_id );
		$this->assertEquals( 'meta_new_key', $meta->meta_key );

		// Update the key and value
		$this->assertTrue( update_metadata_by_mid( 'user', $this->meta_id, 'meta_value', 'meta_key' ) );
		$meta = get_metadata_by_mid( 'user', $this->meta_id );
		$this->assertEquals( 'meta_key', $meta->meta_key );
		$this->assertEquals( 'meta_value', $meta->meta_value );

		// Update the value that has to be serialized
		$this->assertTrue( update_metadata_by_mid( 'user', $this->meta_id, array( 'first', 'second' ) ) );
		$meta = get_metadata_by_mid( 'user', $this->meta_id );
		$this->assertEquals( array( 'first', 'second' ), $meta->meta_value );

		// Let's try some invalid meta data
		$this->assertFalse( update_metadata_by_mid( 'user', 0, 'meta_value' ) );
		$this->assertFalse( update_metadata_by_mid( 'user', $this->meta_id, 'meta_value', array('invalid', 'key' ) ) );

		// Let's see if caches get cleared after updates.
		$meta = get_metadata_by_mid( 'user', $this->meta_id );
		$first = get_user_meta( $meta->user_id, $meta->meta_key );
		$this->assertTrue( update_metadata_by_mid( 'user', $this->meta_id, 'other_meta_value' ) );
		$second = get_user_meta( $meta->user_id, $meta->meta_key );
		$this->assertFalse( $first === $second );
	}

	function test_metadata_exists() {
		$this->assertFalse( metadata_exists( 'user',  $this->author->ID, 'foobarbaz' ) );
		$this->assertTrue( metadata_exists( 'user',  $this->author->ID, 'meta_key' ) );
		$this->assertFalse( metadata_exists( 'user',  1234567890, 'foobarbaz' ) );
		$this->assertFalse( metadata_exists( 'user',  1234567890, 'meta_key' ) );
	}

	function test_metadata_slashes() {
		$key = rand_str();
		$value = 'Test\\singleslash';
		$expected = 'Testsingleslash';
		$value2 = 'Test\\\\doubleslash';
		$expected2 = 'Test\\doubleslash';
		$this->assertFalse( metadata_exists( 'user', $this->author->ID, $key ) );
		$this->assertFalse( delete_metadata( 'user', $this->author->ID, $key ) );
		$this->assertSame( '', get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertInternalType( 'int', add_metadata( 'user', $this->author->ID, $key, $value ) );
		$this->assertEquals( $expected, get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertTrue( delete_metadata( 'user', $this->author->ID, $key ) );
		$this->assertSame( '', get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertInternalType( 'int', update_metadata( 'user', $this->author->ID, $key, $value ) );
		$this->assertEquals( $expected, get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertTrue( update_metadata( 'user', $this->author->ID, $key, 'blah' ) );
		$this->assertEquals( 'blah', get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertTrue( delete_metadata( 'user', $this->author->ID, $key ) );
		$this->assertSame( '', get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertFalse( metadata_exists( 'user', $this->author->ID, $key ) );

		// Test overslashing
		$this->assertInternalType( 'int', add_metadata( 'user', $this->author->ID, $key, $value2 ) );
		$this->assertEquals( $expected2, get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertTrue( delete_metadata( 'user', $this->author->ID, $key ) );
		$this->assertSame( '', get_metadata( 'user', $this->author->ID, $key, true ) );
		$this->assertInternalType( 'int', update_metadata( 'user', $this->author->ID, $key, $value2 ) );
		$this->assertEquals( $expected2, get_metadata( 'user', $this->author->ID, $key, true ) );
	}
}
