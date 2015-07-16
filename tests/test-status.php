<?php

class StatusTest extends WP_UnitTestCase {

	function test_is_valid() {
		$status = new Status();

		$this->assertTrue( $status->is_valid( Status::NOT_SENT ) );
		$this->assertTrue( $status->is_valid( Status::SENT ) );
		$this->assertTrue( $status->is_valid( Status::NOT_READ ) );
		$this->assertTrue( $status->is_valid( Status::READ ) );
		$this->assertFalse( $status->is_valid( 'unknown' ) );
		$this->assertFalse( $status->is_valid( 'draft' ) );
		$this->assertFalse( $status->is_valid( 'unread' ) );
	}

}

