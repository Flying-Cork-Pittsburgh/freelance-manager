<?php


/**
 * Manage Client information
 */
class Client_Command extends WP_CLI_Command {

    /**
     * Add a client.
     *
     * ## OPTIONS
     *
     * <name>
     * : The client you want to create.
     *
     * ## EXAMPLES
     *
     *     wp client add Newman
     *
     * @synopsis <name>
     */
    function add( $args, $assoc_args ) {
        list( $name ) = $args;

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

