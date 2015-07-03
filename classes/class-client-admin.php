<?php
/**
* Manage the admin dashboard with respect to the Client CPT (custom post type)
*
* Create a custom user experience for the the Client CPT. This includes add columns
* to the list of Clients
*
*
* @package  Freelance_Manager
* @access   public
*/
class Client_Admin  {

	/**
	 * Constructor
	 *
	 * Create an instance of the class and load add all the things!
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'manage_client_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'overview_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_overview_meta_box' ) );

		add_filter( 'manage_client_posts_columns', array( $this, 'columns_data' ) );
	}

	public function get_field( $field ) {
		switch ( $field ) {
		case 'location':
			return '_client_location';
			break;
		case 'website':
			return '_client_website';
			break;
		case 'phone':
			return '_client_phone';
			break;
		case 'sha':
			return '_client_sha1';
			break;
		case 'contact_name':
			return '_client_contact_person';
			break;
		case 'contact_email':
			return '_client_contact_email';
			break;
		default:
			return false;
			break;
		}

	}

	/**
	 * Display the content of Client custom fields
	 *
	 * Render the content of custom fields for the Client CPT.
	 *
	 * @since 0.1
	 * @uses {wp_get_attachment_image_src}
	 * @uses {get_post_thumbnail_id}
	 *
	 * @param  string $column_name
	 * @param  int $post_id
	 * @return void
	 */
	public function column_content( $column_name, $post_id ) {

		$location_id = $this->get_field( 'location' );
		$website_id  = $this->get_field( 'website' );
		$phone_id    = $this->get_field( 'phone' );

		$single = true;


		if ( 'thumbnail' == $column_name ) {
			$default_thumbnail = 'default.png';
			$default_image = plugin_dir_url( dirname( __FILE__ ) ) . $default_thumbnail;

			$post_thumbnail_id = get_post_thumbnail_id( $post_id );
			if ( $post_thumbnail_id ) {
				$post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );

				echo '<img alt="client logo" width="48" src="' . $post_thumbnail_img[0] . '" />';
			} else {
				echo '<img alt="default image" width="48" src="' . $default_image . '" />';
			}
		} else if ( 'location' == $column_name ) {
			echo $location = get_post_meta( $post_id, $location_id, $single );
		} else if ( 'phone' == $column_name ) {
			echo get_post_meta( $post_id, $phone_id, $single );
		} else if ( 'website' == $column_name ) {
			$url = get_post_meta( $post_id, $website_id, $single );

			echo '<a href="' . $url . '">' . $url . '</a>';
		}

	}


	/**
	 * Modify the columns headers
	 *
	 * Update the column headers with new fields. This also determines the order the fields are rendered.
	 *
	 * @since 0.1
	 *
	 * @param  type $name it does something
	 * @return type it does something
	 */
	public function columns_data( $columns ) {
		$myCustomColumns = array(
			'thumbnail' => __( 'Thumbnail', 'fremgr' ),
			'location' => __( 'Location', 'fremgr' ),
			'phone' => __( 'Phone', 'fremgr' ),
			'website' => __( 'Website', 'fremgr' ),
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
		add_meta_box( 'fremgr-client-overview', $title, array( $this,  $callback), 'client' );

	}

	/**
	 * Render the form of the metabax
	 *
	 *
	 * @since 0.1
	 *
	 * @param  WP_Post $post the post object being saved
	 * @return void
	 */
	public function overview_callback( $post ) {
		$post_id = $post->ID;

		$location_id = $this->get_field( 'location' );
		$website_id  = $this->get_field( 'website' );
		$phone_id    = $this->get_field( 'phone' );
		$sha1_id     = $this->get_field( 'sha' );

		$contact_person_id  = $this->get_field('contact_name');
		$contact_email_id   = $this->get_field('contact_email');


		$single = true;

		$location = get_post_meta( $post_id, $location_id, $single );
		$website  = get_post_meta( $post_id, $website_id, $single );
		$phone    = get_post_meta( $post_id, $phone_id, $single );
		$sha1     = get_post_meta( $post_id, $sha1_id, $single );

		$contact_person = get_post_meta( $post_id, $contact_person_id, $single );
		$contact_email  = get_post_meta( $post_id, $contact_email_id, $single );

		error_log( 'location_id=' . $location_id . ' location=' . $location );

		wp_nonce_field( 'fremgr_client_meta_box',  '_fremgr_client_overview_meta_box_nonce' );

		?>
		<table class="form-table">
		<tr>
		<td>
			<label for="<?php echo $sha1_id; ?>"><?php _e( 'SHA1', 'fremgr' );  ?></label>
		</td>
		<td>
			<strong id="<?php echo $sha1_id;  ?>"><?php
			echo ( $sha1 ) ? $sha1 : '';
			?></strong>
		</td>
		</tr>
		<tr>
		<td>
			<label for="<?php echo $location_id; ?>"><?php _e( 'Location', 'fremgr' );  ?></label>
		</td>
		<td>
			<input type="text" id="<?php echo $location_id;  ?>" name="<?php echo $location_id; ?>"
			value="<?php echo ( $location ) ? $location : ''; ?>">
		</td>
		</tr>

		<tr>
		<td>
			<label for="<?php echo $website_id; ?>"><?php _e( 'Website', 'fremgr' );  ?></label>
		</td>
		<td>
			<input type="url" id="<?php echo $website_id;  ?>" name="<?php echo $website_id; ?>"
			value="<?php echo ( $website ) ? $website : ''; ?>" required >
		</td>
		</tr>

		<tr>
		<td>
			<label for="<?php echo $phone_id; ?>"><?php _e( 'Phone Number', 'fremgr' );  ?></label>
		</td>
		<td>
			<input type="tel" id="<?php echo $phone_id;  ?>" name="<?php echo $phone_id; ?>"
			value="<?php echo ( $phone ) ? $phone : ''; ?>">
		</td>
		</tr>

		<tr>
		<td>
			<label for="<?php echo $contact_person_id; ?>"><?php _e( 'Contact Person', 'fremgr' );  ?></label>
		</td>
		<td>
			<input type="text"
			id="<?php echo $contact_person_id; ?>"
			name="<?php echo $contact_person_id; ?>"
			value="<?php echo ( $contact_person ) ? $contact_person : ''; ?>">
		</td>
		</tr>

		<tr>
		<td>
			<label for="<?php echo $contact_email_id; ?>"><?php
			_e( 'Contact Email', 'fremgr' );
			?></label>
		</td>
		<td>
			<input type="email"
			id="<?php echo $contact_email_id; ?>"
			name="<?php echo $contact_email_id; ?>"
			value="<?php echo ( $contact_email ) ? $contact_email : ''; ?>">
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
		$post = get_post($post_id);
		$data = array();
		$data['location'] = '';
		$data['website'] = '';
		$data['phone'] = '';
		$data['person'] = '';
		$data['email'] = '';
		$data['sha1'] = '';

		/* OK, its safe for us to save the data now. */

		if ( isset( $_POST['_client_location'] ) ){
			$data['location'] = sanitize_text_field( $_POST['_client_location'] );
		}
		if ( isset( $_POST['_client_website']) ){
			$data['website']  = sanitize_text_field( $_POST['_client_website'] );
		}
		if ( isset($_POST['_client_phone']) ) {
			$data['phone']    = sanitize_text_field( $_POST['_client_phone'] );
		}

		if ( isset($_POST['_client_contact_person']) ) {
			$data['person'] = sanitize_text_field( $_POST['_client_contact_person'] );
		}

		if ( isset($_POST['_client_contact_email']) ) {
			$data['email'] = sanitize_text_field( $_POST['_client_contact_email'] );
		}

		if ( $post_id && $post->post_title &&
			$data['website'] && $data['person'] && $data['email'] ) {

			$data['sha1'] = $this->create_sha(
				$post_id,
				$post->post_title,
				$data['website'],
				$data['person'],
				$data['email']
			);
		}

		$this->update_meta( $post_id, $data );
	}

	/**
	 * Create a SHA1 string to uniquely identify the client
	 *
	 * Describe your function The sha1 isn't controlled by the user. It's generated for them.
	 * These fields are use to prevent guessability and ensure uniqueness across clients
	 *
	 * @param int $post_id
	 * @param string $post_title
	 * @param string $website
	 * @param string $person
	 * @param string $email
	 * @return string
	 */
	public function create_sha( $post_id, $post_title, $website, $person, $email ){
		$value = $post_id . $post_title . $website . $person . $email;
		return sha1( $value );
	}

	/**
	 * Update the post meta with the $data array
	 *
	 * Take a pre-sanitized array of meta values and update their post meta values
	 *
	 * @param int $post_id ID of the post
	 * @param array $data
	 * @return void
	 */
	public function update_meta( $post_id, $data ) {

		update_post_meta( $post_id, '_client_location', $data['location'] );
		update_post_meta( $post_id, '_client_website', $data['website'] );
		update_post_meta( $post_id, '_client_phone', $data['phone'] );
		update_post_meta( $post_id, '_client_contact_person', $data['person'] );
		update_post_meta( $post_id, '_client_contact_email', $data['email'] );
		update_post_meta( $post_id, '_client_sha1', $data['sha1'] );

	}


	/**
	 * Remove the post meta with the $data array
	 *
	 * Take a pre-sanitized array of meta values and update their post meta values
	 *
	 * @param int $post_id ID of the post
	 * @param array $data
	 * @return void
	 */
	public function delete_meta( $post_id ) {

		delete_post_meta( $post_id, '_client_location' );
		delete_post_meta( $post_id, '_client_website' );
		delete_post_meta( $post_id, '_client_phone' );
		delete_post_meta( $post_id, '_client_contact_person' );
		delete_post_meta( $post_id, '_client_contact_email' );
		delete_post_meta( $post_id, '_client_sha1' );

	}
}


