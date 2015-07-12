<?php

/**
 * Represents a single messagemessage
 *
 * @package Freelance_Manager
 */
class Message
{

	protected $subject = '';
	protected $content = '';
	protected $date_created = '';
	protected $date_updated = '';
	protected $statuses = '';
	protected $status = '';
	protected $id = '';

	protected $default_status = '';


	public function __construct( $subject, $content, $status = '' ) {

		$this->statuses = new Status();
		$this->default_status = Status::NOT_READ;

		$this->subject = $subject;
		$this->content = $content;

		$this->status = ( $this->statuses->is_valid( $status )  ) ? $status : $this->default_status;
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
		if ( $this->statuses->is_valid( $status )  ) {
			$this->status = $status;
		} else {
			$this->status = $this->default_status;
		}
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
			'time' => date('U'),
		);
	}

}

