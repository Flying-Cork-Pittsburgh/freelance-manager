<?php
/**
* Manage the admin dashboard with respect to the Message CPT (custom post type)
*
* Create a custom user experience for the the Message CPT. This includes add columns
* to the list of Clients
*
*
* @package  Freelance_Manager
* @access   public
*/
class Message_Admin  {

	private $status_default = 'not_sent';
	private $client_default = 'Client not specified';

	// Field IDs
	private $client_id   = '_client_id';
	private $subject     = '_message_subject';
	private $status      = '_message_status';
	private $actions     = '_message_actions';
	private $clients     = [];
	private $statuses    = [];

	/**
	 * Constructor
	 *
	 * Create an instance of the class and load add all the things!
	 *
	 * @since 0.1
	 *
	 */
	public function __construct() {
		$this->statuses['not_sent'] = __( 'Not Sent' );
		$this->statuses['sent'] = __( 'Sent' );

	}

	/**
	 * Load Actions and Filters
	 *
	 * @uses add_action
	 * @uses add_filter
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'overview_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_overview_meta_box' ) );
		add_action( 'manage_message_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'wp_ajax_message_send', array( $this, 'message_send' ) );
		add_action( 'wp_ajax_nopriv_status_update', array( $this, 'status_update' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ajax_js' ) );

		add_filter( 'manage_message_posts_columns', array( $this, 'columns_headings' ) );
	}

	/**
	* Enqueues the administrative ajax file
	*
	* @param String $one a necessary parameter
	* @param String optional $two an optional value
	* @return void
	*/
	public function ajax_js( $hook ) {
		wp_enqueue_script( 'fremgr-ajax', plugin_dir_url( __DIR__ ) . 'js/ajax.js' );
	}

	/**
	 * Add an 'Overview' meta box to the post
	 *
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	 public function overview_meta_box() {

		$title = __('Overview');
		$callback = 'overview_callback';
		add_meta_box( 'fremgr-message-overview', $title, array( $this,  $callback), 'message' );

	}

	/**
	 * Retreieve a list of Client and save them to this object
	 *
	 * @uses WP_Query
	 *
	 * @param String optional $two an optional value
	 * @return void
	 */
	public function get_clients() {

		if ( ! $this->clients ) {
			$clientQuery = new WP_Query('post_type=client');
			if ( $clientQuery->have_posts() ) {
				while ( $clientQuery->have_posts() ) {
					$clientQuery->the_post();
					$this->clients[ $clientQuery->post->ID ] = $clientQuery->post;
				}
			}
		}

		return $this->clients;
	}

	/**
	 * Render the form of the metabax
	 *
	 *
	 * @since -1.1
	 *
	 * @param  WP_Post $post the post object being saved
	 * @return void
	 */
	public function overview_callback( $post ) {
		$post_id = $post->ID;

		$clients = $this->get_clients();
		$default = ['ID' => 0, 'post_title' => '--default--' ];
		array_unshift($clients, (object) $default);
		$single = true;

		$client_id = get_post_meta( $post_id, $this->client_id, $single );
		$subject = get_post_meta( $post_id, $this->subject, $single );
		$status = get_post_meta( $post_id, $this->status, $single );
		$status = $this->get_status( $status );
		$sha1 = sha1( 'Hello World' );

		wp_nonce_field( 'fremgr_message_meta_box',  '_fremgr_message_overview_meta_box_nonce' );
		?>
		<table class="form-table">
		<tr>
		<td>
			<label for="<?php echo $this->client_id; ?>"><?php _e( 'Client Name', 'fremgr' );  ?></label>
		</td>
		<td>
			<select id="<?php echo $this->client_id;  ?>"  name="<?php echo $this->client_id;  ?>"><?php
			foreach( $clients AS $client ){
				echo '<option value="' . $client->ID . '" '
				. selected( $client_id, $client->ID, false) . '>'
				. $client->post_title . '</option>';
			}
			?></select>
		</td>
		</tr>
		<tr>
		<td>
			<label for="<?php echo $this->subject; ?>"><?php _e( 'Subject', 'fremgr' );  ?></label>
		</td>
		<td>
			<input type="text" id="<?php echo $this->subject;  ?>" name="<?php echo $this->subject; ?>"
			value="<?php echo ( $subject ) ? $subject : ''; ?>">
		</td>
		</tr>

		<tr>
		<td>
			<label for="<?php echo $this->status; ?>"><?php _e( 'Status', 'fremgr' );  ?></label>
		</td>
		<td><?php echo $status; ?>
		</td>
		</tr>

		</table>
		 <?php
	}


	/**
	 * Generate a SHA-1 and save the updated custom fields for the metabox
	 *
	 * @since 0.1
	 *
	 * @param  int $post_id the ID of the Post being saved
	 * @return void
	 */
	public function save_overview_meta_box( $post_id ) {
		error_log( 'Saving MESSAGE=' . print_r( $_POST, true ) );
		$post = get_post($post_id);
		$client_id = '';
		$subject = '';

		if ( isset( $_POST[ $this->client_id ] ) ){
			$client_id = intval( $_POST[ $this->client_id ] );
			update_post_meta( $post_id, $this->client_id, $client_id );
		}

		if ( isset( $_POST[ $this->subject] ) ){
			$subject  = sanitize_text_field( $_POST[ $this->subject ] );
			update_post_meta( $post_id, $this->subject, $subject );
		}

		if ( isset( $_POST[ $this->status] ) ){
			$status  = sanitize_text_field( $_POST[ $this->status ] );
			update_post_meta( $post_id, $this->status, $status );
		}

	}


	/**
	 * Modify the columns headers
	 *
	 * Update the column headers with new fields. This also determines the order the fields are rendered.
	 *
	 * @since 0.1
	 *
	 * @param  array $columns Associative array of column headings
	 * @return array
	 */
	public function columns_headings( $columns ) {
		$myCustomColumns = array(
			'subject' => __( 'Subject', 'fremgr' ),
			'client' => __( 'Client', 'fremgr' ),
			'message_status' => __( 'Status', 'fremgr' ),
			'message_actions' => __( 'Actions', 'fremgr' ),
		);
		$columns = array_merge( $columns, $myCustomColumns );

		/** Remove a Author, Comments Columns **/
		if ( isset( $columns['author'] ) ) {
			unset( $columns['author'] );
		}

		if ( isset( $columns['comments'] ) ) {
			unset( $columns['comments'] );
		}

		return $columns;
	}

	/**
	 * Display the content of Message custom fields
	 *
	 * Render the content of custom fields for the Message CPT.
	 *
	 * @since 0.1
	 *
	 * @param  string $column_name
	 * @param  int $post_id
	 * @return void
	 */
	public function column_content( $column_name, $post_id ) {
		$single = true;

		if ( 'client' == $column_name ) {
			$client_id = get_post_meta( $post_id, $this->client_id, $single );
			if ( $client_id ) {
				$clients = $this->get_clients();

				if ( isset($clients[ $client_id ] ) ) {
					echo $clients[ $client_id ]->post_title;
				}
			} else {
				echo $this->client_default;
			}

		} else if ( 'subject' == $column_name ) {
			echo get_post_meta( $post_id, $this->subject, $single );

		} else if ( 'message_status' == $column_name ) {
			$code = get_post_meta( $post_id, $this->status, $single );
			echo $this->get_status( $code );

		} else if ( 'message_actions' == $column_name ) {

			$client_id = get_post_meta( $post_id, $this->client_id, $single );
			if ( $client_id ) {
				$clients = $this->get_clients();

				$client_admin = new Client_Admin();
				$url_field = $client_admin->get_field('website');

				$website = get_post_meta( $client_id, $url_field, $single );
				$action = 'message_send';

				$data_attrs = '';
				$data_attrs .= ' data-id="' . $post_id . '" ';
				$data_attrs .= ' data-action="' . $action . '" ';
				$data_attrs .= ' data-website="' . $website . '" ';

				echo '<button ' . $data_attrs . ' class="message_send">Send</button>';
			}
		}
	}

	/**
	* Look up the content for the current request and send it to the client dashboard
	*
	* @param int $_POST['id']  the Post ID for the message.
	* @param String $_POST['website'] The client website - must be a wordpress
	* site using the Freelance Site
	* @return void
	*/
	public function message_send() {
		$website = strip_tags( $_POST['website'] );
		$id      = intval( $_POST['id'] );

		$url = $website . '/wp-admin/admin-ajax.php';

		wp_reset_postdata();
		//
		// Look up the message content for this $id
		//
		$query_args = 'p=' . $id. '&post_type=message';
		$query2 = new WP_Query( $query_args );
		$content = '';

		if ( $query2->have_posts() ) {
			while ( $query2->have_posts() ) {
				$query2->the_post();
				$content = get_the_content();
			}
		} else {
			$data = array('message' => 'There are no posts for this ID');

			wp_send_json_error( $data );
		}

		// Restore original Post Data
		wp_reset_postdata();

		$data = array(
			'action' => 'new_message',
			'id' => $id,
			'message' => $content,
			'client_sha' => 'd2a04d71301a8915217dd5faf81d12cffd6cd958',
			'manager_sha' => 'f2e048910a8c617d70ccac9d60cca84c77a960c09'
		);

		$args = array();
		$args['method'] = 'POST';
		$args['user-agent'] = 'Freelance-Manager/' . FREMGR_VERSION;
		$args['body'] = $data;

		$response = wp_remote_post( $url, $args );

		$data = wp_remote_retrieve_body( $response );
		if ( 200 == $response['response']['code'] ) {
			wp_send_json_success( $data );
		} else {
			error_log( 'data=' . print_r( $data, true ) );
			wp_send_json_error( $data );
		}

	}

	/**
	 * Retrieve the Status text for a given code.
	 *
	 * @param string $code a status code
	 * @return string
	 */
	public function get_status( $code ) {
		if ( empty( $code ) ) {
			$code = $this->status_default;
		}

		if ( isset( $this->statuses[ $code ] ) ) {
			$status = $this->statuses[ $code ];
		} else {
			$status = $code;
		}

		return $status;
	}


	/**
	 *  Handle the AJAX request to update status
	 *
	 * @param String $one a necessary parameter
	 * @param String optional $two an optional value
	 * @return void
	 */
	public function status_update() {


		if ( isset( $_POST["action"] ) ) {
			$data = array();
			$data['action']        = wp_kses_data( $_POST['action'] );
			$data['id']            = intval( $_POST['id'] );
			$data['message']       = wp_kses_data( $_POST['message'] );
			$data['status']        = wp_kses_data( $_POST['status'] );
			$data['client_sha']    = wp_kses_data( $_POST['client_sha'] );
			$data['manager_sha']   = wp_kses_data( $_POST['manager_sha'] );

			update_post_meta( $data['id'], $this->status, $this->statuses['sent'], $this->statuses['not_sent'] );

			status_header( 200 );
			wp_send_json_success( $data );
			wp_die();
		} else {
			status_header( 412 );
			wp_send_json_error( "action parameter not specified" );
			wp_die();

		}
	}

}
