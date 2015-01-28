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


}


