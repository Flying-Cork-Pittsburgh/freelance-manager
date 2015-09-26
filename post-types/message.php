<?php

function fremgr_message_init() {
	register_post_type( 'message', array(
		'labels'            => array(
			'name'                => __( 'Messages', 'fremgr' ),
			'singular_name'       => __( 'Message', 'fremgr' ),
			'all_items'           => __( 'Messages', 'fremgr' ),
			'new_item'            => __( 'New Message', 'fremgr' ),
			'add_new'             => __( 'Add New', 'fremgr' ),
			'add_new_item'        => __( 'Add New Message', 'fremgr' ),
			'edit_item'           => __( 'Edit Message', 'fremgr' ),
			'view_item'           => __( 'View Message', 'fremgr' ),
			'search_items'        => __( 'Search Messages', 'fremgr' ),
			'not_found'           => __( 'No Messages found', 'fremgr' ),
			'not_found_in_trash'  => __( 'No Messages found in trash', 'fremgr' ),
			'parent_item_colon'   => __( 'Parent Message', 'fremgr' ),
			'menu_name'           => __( 'Messages', 'fremgr' ),
		),
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title', 'editor' ),
		'has_archive'       => true,
		'rewrite'           => true,
		'query_var'         => true,
	) );

}
add_action( 'init', 'fremgr_message_init' );

function fremgr_message_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['message'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Message updated. <a target="_blank" href="%s">View Message</a>', 'fremgr'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'fremgr'),
		3 => __('Custom field deleted.', 'fremgr'),
		4 => __('Message updated.', 'fremgr'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Message restored to revision from %s', 'fremgr'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Message published. <a href="%s">View Message</a>', 'fremgr'), esc_url( $permalink ) ),
		7 => __('Message saved.', 'fremgr'),
		8 => sprintf( __('Message submitted. <a target="_blank" href="%s">Preview Message</a>', 'fremgr'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Message scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Message</a>', 'fremgr'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Message draft updated. <a target="_blank" href="%s">Preview Message</a>', 'fremgr'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'fremgr_message_updated_messages' );
