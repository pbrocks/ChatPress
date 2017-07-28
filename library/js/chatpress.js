jQuery( document ).ready( function() {

	jQuery( 'body' ).on( 'click', '.chatpress_button_input', function() {

		var index = jQuery( this ).data( 'index' );

		var button = jQuery( this );

		var message_input = tinymce.editors['editor_' + index].getContent();

		var author_input = jQuery( '.chatpress_author_input' );

		var style_input = jQuery( '.chatpress_style_input' );

		var message = message_input; // parse this input

		var author = jQuery( author_input ).val();

		var style = jQuery( style_input ).val();

		var data = {
	    	index: index,
	    	author: author,
	    	message: message,
	    	style: style,
		};

		tinymce.editors['editor_' + index].setContent('');

		// jQuery( message_input ).val('');

		jQuery( author_input ).val('');

		jQuery( style_input ).val('');

			jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: cp_script.ajaxurl, // Including ajax file
			data: {
		   	"action": "chatpress_post_message",
		   	"data"   : data
			},
			success: function( response ) { // Show returned data using the function.
				// alert( data.message );

				var channel    = jQuery( '.chatpress_channel_message_container' ),
				    this_index = jQuery( channel ).data( 'index' );

				alert( 'message posted' );

				// jQuery('.chatpress_button_refresh').trigger('click');

				channel.prepend( response.data.message );

			}

			});


	});

	jQuery( 'body' ).on( 'click', '.chatpress_button_refresh', function( e ) {

		e.preventDefault();

		var index = jQuery( this ).data( 'index' );

		var button = jQuery( this );

		var message_input = jQuery( '.chatpress_content_input' );

		var author_input = jQuery( '.chatpress_author_input' );

		var style_input = jQuery( '.chatpress_style_input' );

		var message = jQuery( message_input ).val();

		var author = jQuery( author_input ).val();

		var style = jQuery( style_input ).val();

		var data = {
	    	index: index,
	    	author: author,
	    	message: message,
	    	style: style,
		};


			jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: cp_script.ajaxurl, // Including ajax file
			data: {
		   	"action": "chatpress_refresh_message",
		   	"data"   : data
			},
			success: function( response ) { // Show returned data using the function.
				// alert( data.message );

				var channel    = jQuery( '.chatpress_channel_message_container' ),
				    this_index = jQuery( channel ).data( 'index' );

				jQuery( '.chatpress_message_div' ).remove();

				alert( 'messages refreshed' );

				channel.html( response.data.query_results );

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

	var message_number = jQuery( this ).data( 'message_number' );

	var index = jQuery( this ).data( 'index' );

	var content = tinymce.editors['editor_' + index].getContent();

	tinymce.editors['editor_' + index].setContent( content + '>> ' + message_number );

});

});
