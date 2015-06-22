<?php

class MessageAdminTest extends WP_UnitTestCase {

	public function __construct() {

	}

	function test_column_data() {
		$data = [
			'author' => 'Author',
			'comments' => 'Comments',
		];

		// replace this with some actual testing code
		$this->assertArrayHasKey('author', $data);
		$message_admin = new Message_Admin();

		// this method is should add 'subject', and 'client'
		// and remove the 'author', and 'comments' fields
		$data = $message_admin->columns_headings( $data );

		$this->assertArrayHasKey('subject', $data);
		$this->assertArrayNotHasKey('author', $data);
	}

}

