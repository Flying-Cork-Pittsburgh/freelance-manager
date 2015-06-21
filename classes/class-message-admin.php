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

	// Field IDs
	private $client_id   = '_client_id';
	private $subject     = '_message_subject';
	private $clients     = [];

	/**
	 * Constructor
	 *
	 * Create an instance of the class and load add all the things!
	 *
	 * @since 0.1
	 *
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'overview_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_overview_meta_box' ) );
		add_action( 'manage_message_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_filter( 'manage_message_posts_columns', array( $this, 'columns_headings' ) );
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

		$single = true;

		$client_id = get_post_meta( $post_id, $this->client_id, $single );
		$subject = get_post_meta( $post_id, $this->subject, $single );
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
				echo "Client not specified";
			}

		} else if ( 'subject' == $column_name ) {
			echo get_post_meta( $post_id, $this->subject, $single );

		}

	}
}
