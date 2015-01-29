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
		
		add_filter( 'manage_posts_columns', array( $this, 'columns_data' ) );
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
			echo 'Seattle, WA';
		} else if ( 'phone' == $column_name ) {
			echo '206-555-1212';
		} else if ( 'website' == $column_name ) {
			$url = 'http://example.org';

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

		$location_id = '_client_location';
		$website_id  = '_client_website';
		$phone_id    = '_client_phone';

		$single = true;

		$location = get_post_meta( $post_id, $location_id, $single );
		$website  = get_post_meta( $post_id, $website_id, $single );
		$phone    = get_post_meta( $post_id, $phone_id, $single );

		error_log( 'location_id=' . $location_id . ' location=' . $location );

		wp_nonce_field( 'fremgr_client_meta_box',  '_fremgr_client_overview_meta_box_nonce' );

		?>
		<div class="widefat">
			<label for="<?php echo $location_id; ?>"><?php _e( 'Location', 'fremgr' );  ?></label>
			<input type="text" id="<?php echo $location_id;  ?>" name="<?php echo $location_id; ?>"
			value="<?php echo ( $location ) ? $location : ''; ?>">
		</div>
		<div class="widefat">
			<label for="<?php echo $website_id; ?>"><?php _e( 'Website', 'fremgr' );  ?></label>
			<input type="text" id="<?php echo $website_id;  ?>" name="<?php echo $website_id; ?>"
			value="<?php echo ( $website ) ? $website : ''; ?>">
		</div>
		<div class="widefat">
			<label for="<?php echo $phone_id; ?>"><?php _e( 'Phone Number', 'fremgr' );  ?></label>
			<input type="text" id="<?php echo $phone_id;  ?>" name="<?php echo $phone_id; ?>"
			value="<?php echo ( $phone ) ? $phone : ''; ?>">
		</div>
		 <?php
	}

	/**
	 * Save the updated custom fields for the metabox
	 *
	 *
	 * @since 0.1
	 *
	 * @param  int $post_id the ID of the Post being saved
	 * @return void
	 */
	public function save_overview_meta_box( $post_id ) {

		/* OK, its safe for us to save the data now. */

		$location = sanitize_text_field( $_POST['_client_location'] );
		$website  = sanitize_text_field( $_POST['_client_website'] );
		$phone    = sanitize_text_field( $_POST['_client_phone'] );

		// Update the meta fields.
		update_post_meta( $post_id, '_client_location', $location );
		update_post_meta( $post_id, '_client_website', $website );
		update_post_meta( $post_id, '_client_phone', $phone );
	}

}


