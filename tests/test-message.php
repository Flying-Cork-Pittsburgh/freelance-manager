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

		$message->set_status( $message::READ );
		$this->assertEquals( 'read' , $message->get_status() );

		$message->set_status( $message::NOT_READ );
		$this->assertEquals( 'not_read' , $message->get_status() );

		$message->set_status( $message::SENT );
		$this->assertEquals( 'sent' , $message->get_status() );

		$message->set_status( $message::NOT_SENT );
		$this->assertEquals( 'not_sent' , $message->get_status() );


		$message_with_status = new Message( $subject, $content, 'sent' );
		$this->assertEquals( $message_with_status::SENT , $message_with_status->get_status() );

		$this->assertNotEquals( $message_with_status::NOT_SENT , $message_with_status->get_status() );
		$this->assertNotEquals( $message_with_status::READ , $message_with_status->get_status() );
		$this->assertNotEquals( $message_with_status::NOT_READ , $message_with_status->get_status() );
	}

}

