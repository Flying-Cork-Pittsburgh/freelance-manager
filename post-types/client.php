<?php

function client_init() {
	register_post_type( 'client', array(
		'labels'            => array(
			'name'                => __( 'Clients', 'fremgr' ),
			'singular_name'       => __( 'Client', 'fremgr' ),
			'all_items'           => __( 'Clients', 'fremgr' ),
			'new_item'            => __( 'New Client', 'fremgr' ),
			'add_new'             => __( 'Add New', 'fremgr' ),
			'add_new_item'        => __( 'Add New Client', 'fremgr' ),
			'edit_item'           => __( 'Edit Client', 'fremgr' ),
			'view_item'           => __( 'View Client', 'fremgr' ),
			'search_items'        => __( 'Search Clients', 'fremgr' ),
			'not_found'           => __( 'No Clients found', 'fremgr' ),
			'not_found_in_trash'  => __( 'No Clients found in trash', 'fremgr' ),
			'parent_item_colon'   => __( 'Parent Client', 'fremgr' ),
			'menu_name'           => __( 'Clients', 'fremgr' ),
		),
		'public'            => false,
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
		'has_archive'       => false,
		'rewrite'           => true,
		'query_var'         => true,
	) );

}
add_action( 'init', 'client_init' );

function client_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['client'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Client updated. <a target="_blank" href="%s">View Client</a>', 'fremgr'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'fremgr'),
		3 => __('Custom field deleted.', 'fremgr'),
		4 => __('Client updated.', 'fremgr'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Client restored to revision from %s', 'fremgr'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Client published. <a href="%s">View Client</a>', 'fremgr'), esc_url( $permalink ) ),
		7 => __('Client saved.', 'fremgr'),
		8 => sprintf( __('Client submitted. <a target="_blank" href="%s">Preview Client</a>', 'fremgr'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Client scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Client</a>', 'fremgr'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Client draft updated. <a target="_blank" href="%s">Preview Client</a>', 'fremgr'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'client_updated_messages' );
