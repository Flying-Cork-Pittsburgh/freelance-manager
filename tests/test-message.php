<?php

class MessageTest extends WP_UnitTestCase {

	function test_subject() {
		$subject = 'The Subject';
		$content = 'lorem ipsum dolor content';

		$message = new Message( $subject, $content );

		$this->assertEquals( $subject , $message->get_subject() );
	}

	function test_content() {
		$subject = 'The Subject';
		$content = 'lorem ipsum dolor content';

		$message = new Message( $subject, $content );

		$this->assertEquals( $content , $message->get_content() );
	}

	function test_status() {

		$subject = 'The Subject';
		$content = 'lorem ipsum dolor content';

		$message = new Message( $subject, $content );

		$this->assertEquals( 'not_read' , $message->get_status() );

		$message->set_status( Status::READ );
		$this->assertEquals( 'read' , $message->get_status() );

		$message->set_status( Status::NOT_READ );
		$this->assertEquals( 'not_read' , $message->get_status() );

		$message->set_status( Status::SENT );
		$this->assertEquals( 'sent' , $message->get_status() );

		$message->set_status( Status::NOT_SENT );
		$this->assertEquals( 'not_sent' , $message->get_status() );


		$message_with_status = new Message( $subject, $content, 'sent' );
		$this->assertEquals( Status::SENT , $message_with_status->get_status() );

		$this->assertNotEquals( Status::NOT_SENT , $message_with_status->get_status() );
		$this->assertNotEquals( Status::READ , $message_with_status->get_status() );
		$this->assertNotEquals( Status::NOT_READ , $message_with_status->get_status() );
	}

}

