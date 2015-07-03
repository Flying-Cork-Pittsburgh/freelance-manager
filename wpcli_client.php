<?php


/**
 * Manage Client information
 */
class Client_Command extends WP_CLI_Command {

    /**
     * @var string $object_type WordPress' expected name for the object.
     */
	protected $obj_type = 'client';


    /**
     * @var string $obj_fields WordPress' expected name for the object.
     */
	protected $obj_fields = 'ID,post_title,post_name,post_status,post_date,post_modified';

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

		if ( isset( $assoc_args['location'] ) ){
			$data['location'] = sanitize_text_field( $assoc_args['location'] );
		}
		if ( isset( $assoc_args['website']) ){
			$data['website']  = sanitize_text_field( $assoc_args['website'] );
		}
		if ( isset($assoc_args['phone']) ) {
			$data['phone']    = sanitize_text_field( $assoc_args['phone'] );
		}

		if ( isset($assoc_args['contact_name']) ) {
			$data['contact_name'] = sanitize_text_field( $assoc_args['contact_name'] );
		}

		if ( isset($assoc_args['contact_email']) ) {
			$data['contact_email'] = sanitize_text_field( $assoc_args['contact_email'] );
		}

		if ( $post_id && $name &&
			$data['website'] && $data['contact_name'] && $data['contact_email'] ) {

			$data['sha1'] = $client_admin->create_sha(
				$post_id,
				$name,
				$data['website'],
				$data['contact_name'],
				$data['contact_email']
			);
		}

		$client_admin->update_meta( $post_id, $data );

        WP_CLI::success( "Added $name!" );
    }

	/**
     * list all clients.
     *
     * ## OPTIONS
     *
     *
     * ## EXAMPLES
     *
     *     wp client list [--fields=<fields>]
     *
     * @synopsis [--fields=<fields>]
	 *
	 * @subcommand list
     */
    function listall( $args, $assoc_args = array() ) {
        $formatter = $this->get_formatter( $assoc_args );

		error_log( print_r( $formatter, true ) );

        $defaults = array(
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'post_type'      => 'client',
        );
        $query_args = array_merge( $defaults, $assoc_args );


		$query = new WP_Query( $query_args );
		$formatter->display_items( $query->posts );

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
     *     wp client delete 5
     *
     * @synopsis <id>
     */
    function delete( $args, $assoc_args ) {
        list( $post_id ) = $args;

		$post = wp_delete_post( $post_id, true );
		if ( $post === false ) {
			WP_CLI::error( "Could not delete post" );
			return;
		} else {
			$client_admin = new Client_Admin();
			$client_admin->delete_meta( $post_id );

			WP_CLI::success( "Deleted post with ID $post_id and it's meta!" );
		}
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


    /**
     * Get Formatter object based on supplied parameters.
     *
     * @param array $assoc_args Parameters passed to command. Determines formatting.
     * @return \WP_CLI\Formatter
     */
    protected function get_formatter( &$assoc_args ) {

        if ( ! empty( $assoc_args['fields'] ) ) {
            if ( is_string( $assoc_args['fields'] ) ) {
                $fields = explode( ',', $assoc_args['fields'] );
            } else {
                $fields = $assoc_args['fields'];
            }
        } else {
            $fields = $this->obj_fields;
        }
        return new \WP_CLI\Formatter( $assoc_args, $fields, $this->obj_type );
    }

}

WP_CLI::add_command( 'client', 'Client_Command' );

