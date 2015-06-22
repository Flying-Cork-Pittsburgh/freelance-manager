<?php

class ClientAdminTest extends WP_UnitTestCase {

	public function __construct() {

	}

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

}

