<?php


/**
 * Manage Client information
 */
class Client_Command extends WP_CLI_Command {

    /**
     * Add a Client.
     *
     * ## OPTIONS
     *
     * <name>
     * :   The name of the Client
     *
     * <slug>
     * :   The unique value that can used in an URL.
     *
     * ## EXAMPLES
	 *
     * wp client add Company
     * wp client add 'Company Name' [company-name]
     *
     * @synopsis <name> [<slug>] [--location=<location>] [--website=<url>] [--phone=<tel>] [--contact_name=<fullname>] [--contact_email=<email>]
     */
    function add( $args, $assoc_args = array() ) {
		$data = array();
		$data['location'] = '';
		$data['website'] = '';
		$data['phone'] = '';
		$data['person'] = '';
		$data['email'] = '';
		$data['sha1'] = '';

		$name = $args[0];
		$slug = ( isset( $args[1] ) ) ? sanitize_title( $args[1] ) : sanitize_title( $name );

		$post = array();
		$post['post_title'] = $name;
		$post['post_name'] = $slug;
		$post['post_type'] = 'client';
		$post['post_author'] = '1';

		$post_id = wp_insert_post( $post, true );
		if( is_wp_error( $post_id ) ) {
			error_log( print_r( $post_id->get_error_message(), true ) );
			return;
		}

		$client_admin = new Client_Admin();

		error_log( 'assoc_args=' . print_r( $assoc_args, true ) );

		if ( isset( $assoc_args['location'] ) ){
			$data['location'] = sanitize_text_field( $assoc_args['location'] );
		}
		if ( isset( $assoc_args['url']) ){
			$data['website']  = sanitize_text_field( $assoc_args['url'] );
		}
		if ( isset($assoc_args['tel']) ) {
			$data['phone']    = sanitize_text_field( $assoc_args['tel'] );
		}

		if ( isset($assoc_args['fullname']) ) {
			$data['person'] = sanitize_text_field( $assoc_args['fullname'] );
		}

		if ( isset($assoc_args['email']) ) {
			$data['email'] = sanitize_text_field( $assoc_args['email'] );
		}

		if ( $post_id && $name &&
			$data['website'] && $data['person'] && $data['email'] ) {

			$data['sha1'] = $this->create_sha(
				$post_id,
				$name,
				$data['website'],
				$data['person'],
				$data['email']
			);
		}

		$client_admin->update_meta( $post_id, $data );

        // Print a success message
        WP_CLI::success( "todo: Added $name!" );
    }

	/**
     * Delete a client.
     *
     * ## OPTIONS
     *
     * <name>
     * : The client you want to remove.
     *
     * ## EXAMPLES
     *
     *     wp client delete Newman
     *
     * @synopsis <name>
     */
    function delete( $args, $assoc_args ) {
        list( $name ) = $args;

        // Print a success message
        WP_CLI::success( "todo: Deleted $name!" );
    }

    /**
     * Update a client.
     *
     * ## OPTIONS
     *
     * <name>
     * : The client you want to update.
     *
     * ## EXAMPLES
     *
     *     wp client update Newman
     *
     * @synopsis <name>
     */
    function update( $args, $assoc_args ) {
        list( $name ) = $args;

        // Print a success message
        WP_CLI::success( "todo: Updated $name!" );
    }

    /**
     * Get a client.
     *
     * ## OPTIONS
     *
     * <name>
     * : The client you want to retrieve.
     *
     * ## EXAMPLES
     *
     *     wp client get Newman
     *
     * @synopsis <name>
     */
    function get( $args, $assoc_args ) {
        list( $name ) = $args;

        // Print a success message
        WP_CLI::success( "todo: Retrieved $name!" );
    }


}

WP_CLI::add_command( 'client', 'Client_Command' );

