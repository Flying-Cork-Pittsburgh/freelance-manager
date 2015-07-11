
function update_status_in_dash( data ){
	jQuery('#post-' + data.id + '').find('.message_status').text( data.status );
}

function update_status_via_ajax( data ) {

	console.log( "data=" );
	data.action =  'status_update';
	data.status =  'sent';
	console.log( data );
	jQuery.ajax({
		method: "POST",
		url: ajaxurl,
		action: 'status_update',
		data: data

	}).done(function( msg ) {
		console.log("Booyah!");
		console.log( msg );
	});

}

jQuery( document ).ready(function($) {
	console.log( "js/ajax.js - ready!" );

	$('button.message_send').on('click', function( event ){
		event.preventDefault();
		var postData = {};
		postData.id = $(this).attr('data-id');
		postData.action = $(this).attr('data-action');
		postData.website = $(this).attr('data-website');
		postData.client_id = $(this).attr('data-client-id');

		console.log('send a message to: ' + ajaxurl  );
		console.dir(postData);

		$.ajax({
			method: "POST",
			url: ajaxurl,
			data: postData

		}).done(function( msg ) {
			var temp = JSON.parse( msg.data );
			temp.data.status = 'Sent';
			update_status_in_dash( temp.data );
			update_status_via_ajax( temp.data );


		}).fail(function( jqXHR, textStatus ) {
			console.log( "message_send - Failed: ");
			console.dir( textStatus );
			alert( "message_send - Failed: ");
		});;
	})
});


