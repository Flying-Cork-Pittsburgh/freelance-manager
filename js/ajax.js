

jQuery( document ).ready(function($) {
	console.log( "js/ajax.js - ready!" );

	$('button.message_send').on('click', function( event ){
		event.preventDefault();
		var postData = {};
		postData.id = $(this).attr('data-id');
		postData.action = $(this).attr('data-action');
		postData.website = $(this).attr('data-website');

		console.log('send a message to: ' + ajaxurl  );
		console.dir(postData);

		$.ajax({
			method: "POST",
			url: ajaxurl,
			data: postData

		}).done(function( msg ) {
			console.log( "message_send - Success: ");
			console.dir( msg );
			alert( "message_send - Success: ");

		}).fail(function( jqXHR, textStatus ) {
			console.log( "message_send - Failed: ");
			console.dir( textStatus );
			alert( "message_send - Failed: ");
		});;
	})
});


