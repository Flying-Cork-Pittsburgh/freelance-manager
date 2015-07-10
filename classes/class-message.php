<?php

/**
 * Represents a single messagemessage
 *
 * @package Freelance_Manager
 */
class Message
{
	const SENT     = 'sent';
	const NOT_SENT = 'not_sent';
	const READ     = 'read';
	const NOT_READ = 'not_read';

	protected $subject = '';
	protected $content = '';
	protected $date_created = '';
	protected $date_updated = '';
	protected $status = '';
	protected $statuses = [];
	protected $id = '';

	protected $default_status = self::NOT_READ;


	public function __construct( $subject, $content, $status = '' ) {

		$this->statuses[ self::SENT ] = __( 'Sent', 'fremgr');
		$this->statuses[ self::NOT_SENT ] = __( 'Not Sent', 'fremgr');
		$this->statuses[ self::READ ] = __( 'Read', 'fremgr');
		$this->statuses[ self::NOT_READ ] = __( 'Not Read', 'fremgr');

		$this->subject = $subject;
		$this->content = $content;

		$this->status = ( $status && isset( $this->statuses[ $status ]) ) ? $status : $this->default_status;
	}

	// Content Field
	public function get_content(){
		return $this->content;
	}


	// Subject Field
	public function get_subject(){
		return $this->subject;
	}

	// Date Created Field
	public function get_date_created(){
		return $this->date_created;
	}

	public function set_date_created( $date ){
		$this->date_created = $date;
	}


	// Date Updated Field
	public function get_date_updated(){
		return $this->date_updated;
	}

	public function set_date_updated( $date ){
		$this->date_updated = $date;
	}


	// Status Field
	public function get_status(){
		return $this->status;
	}

	public function set_status( $status ){
		$this->status = $status;
	}

	// ID Field
	public function get_id(){
		return $this->id;
	}

	public function set_id( $id ){
		$this->id = $id;
	}

	public function get() {
		return array(
			'id' => $this->get_id(),
			'subject' => $this->get_subject(),
			'content' => $this->get_content(),
			'date' => date('c'),
		);
	}
}

