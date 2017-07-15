jQuery( document ).ready( function() {


	jQuery( 'body' ).on( 'click', '.chatpress_button_input', function() {

	var index = jQuery( this ).data( 'index' );

	var button = jQuery( this );

	var chat_div = jQuery( '.chatpress_text_input' );

	var message = jQuery( chat_div ).val();

	var data = {
    	index: index,
    	message: message,
    	index: index
	};

	jQuery( chat_div ).val('');

	jQuery.ajax({
	type: 'POST',   // Adding Post method
	url: cp_script.ajaxurl, // Including ajax file
	data: {
   	"action": "chatpress_post_message",
   	"data"   : data
	},
	success: function(){ // Show returned data using the function.
		// alert( data.message );
	}
	});


	} );

} );