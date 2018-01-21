jQuery( document ).ready( function() {

	jQuery('.cp_quoted_comment_div').hide();

	jQuery( 'body' ).on( 'click', '.chatpress_button_input', function() {

		var index         = jQuery( this ).data( 'index' ),
 		    button        = jQuery( this ),
				message_input = jQuery('.chatpress_text_input'),
				message       = message_input.val(); // parse this input

		var data = {
	    	index: index,
	    	message: message,
		};

		message_input.val('');


		jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: cp_script.ajaxurl, // Including ajax file
			data: {
		   	"action": "chatpress_post_message",
		   	"data"   : data
			},
			success: function( response ) { // Show returned data using the function.
				// alert( data.message );

				var channel = jQuery( '.chatpress_channel_message_container' );

				// call refresh method

			}

			});


	});

	jQuery( 'body' ).on( 'click', '.chatpress_button_refresh', function() {

		//e.preventDefault();

		var button  = jQuery( this ),
				index = jQuery( this ).data( 'index' );

			//alert( 'refreshed' );

			jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: cp_script.ajaxurl, // Including ajax file
			data: {
		   	"action": "chatpress_refresh_message",
		   	"data"   : data
			},
			success: function( response ) { // Show returned data using the function.
				alert( 'refreshed' );

				// var channel = jQuery( '.chatpress_channel_message_container' );

				// channel.html( response.data.query_results )

			}

			});


	});


	jQuery( 'body' ).on( 'click', '.message_delete_link', function( e ) {
		e.preventDefault();

		var message_container = jQuery( this ).parent().parent();

		var id = jQuery( this ).data( 'index' );

		var data = {
			index:id,
		};

		message_container.remove();

			jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: cp_script.ajaxurl, // Including ajax file
			data: {
				"action": "chatpress_delete_message",
				"data"   : data,
			},
			success: function( response ) { // Show returned data using the function.

				//alert( response.data.query_results );

			}

			});


	});


jQuery( 'body' ).on( 'click', '.message_number_link', function( e ) {

	e.preventDefault();

	var message_number = jQuery( this ).data( 'message_number' ),
			index          = jQuery( this ).data( 'index' ),
			content        = tinymce.editors['editor_' + index].getContent();

	tinymce.editors['editor_' + index].setContent( content + '{{' + message_number + '' );

});

jQuery( '.chatpress_channel_message_container_title' ).hover( function() {

	jQuery('.chatpress_title_hover_div').show();

});

jQuery( '.chatpress_channel_message_container_title' ).mouseout( function() {

	jQuery('.chatpress_title_hover_div').hide();

});

jQuery( 'body' ).on( 'click', '.cp_quoted_comment_link', function( e ) {

	var message_number = jQuery( this ).data( 'message_id' );

	e.preventDefault();

		//var grandparent = this.parent();

	var div_string = '.cp_quoted_comment_div[data-message_id="' + message_number + '"]';

	var the_div = jQuery( this ).parent().parent().find( div_string );


	if ( jQuery( the_div ).css('display') === 'none') {

		 jQuery( the_div ).show();

	 } else {

		 jQuery( the_div ).hide();

	 }

});


});
