<?php

class ClientAdminTest extends WP_UnitTestCase {

	function test_column_data() {
		$data = [
			'first' => 'First',
			'second' => 'Second',
			'author' => 'Author',
			'comments' => 'Comments',
		];

		// replace this with some actual testing code
		$this->assertArrayHasKey('first', $data);
		$client_admin = new Client_Admin();

		// this method is should add 'location', 'phone', and 'website'
		// and remove the 'author', and 'comments' fields
		$data = $client_admin->columns_data( $data );

		$this->assertArrayHasKey('location', $data);
		$this->assertArrayNotHasKey('author', $data);
	}

	public function test_get_field_info() {

		$client_admin = new Client_Admin();

		$this->assertEquals( '_client_location', $client_admin->get_field( 'location' ) );
		$this->assertEquals( '_client_website', $client_admin->get_field( 'website' ) );
		$this->assertEquals( '_client_phone', $client_admin->get_field( 'phone' ) );
		$this->assertEquals( '_client_sha1', $client_admin->get_field( 'sha' ) );
		$this->assertEquals( '_client_contact_person', $client_admin->get_field( 'contact_name' ) );
		$this->assertEquals( '_client_contact_email', $client_admin->get_field( 'contact_email' ) );
		$this->assertFalse( $client_admin->get_field( 'does_not_exist' ));
	}

	public function test_delete_meta() {
		$data = array( 'phone' => 1, 'sha' => 2 );
		$post_id = 1;

		$client_admin = new Client_Admin();
		$updated = $client_admin->update_meta( $post_id, $data );
		$this->assertEquals( $updated, 2 );

		$deleted = $client_admin->delete_meta( $post_id, $data );
		$this->assertEquals( $deleted, 2 );
	}

}

