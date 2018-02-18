<?php
class parser extends ChatPress {

	public function __construct() {}

	/**
	 * Parse the input string with quotes and greentext etc.
	 *
	 * @param string $input - string to parse.
	 *
	 * @since 0.1
	 */
	public function cp_parse( $input ) {

		$strlen = strlen( $input ) + 1;

		$output = '';

		// 	> FOR GREENTEXT
		//
		// 	*TEXT* FOR BOLD
		//
		// 	_TEXT_ FOR UNDERLINE
		//
		// 	/TEXT/ FOR ITALIC


		$x = 1;

		for ( $i = 0; $i <= $strlen; $i++ ) {

			$char = substr( $input, $i, 1 );

			$preceeding_char = substr( $input, $i - 1, 1 );


			// 	>> FOR MESSAGE QUOTE
			if ( '>' === $char ) {


				// if ( '>' === $preceeding_char ) {
				//
				// 	$output = substr( $output, 0, $i - 1 );
				//
				// 	$link_text_id = substr( $input, $i + 1, 21 );
				//
				// 	$output .= '>> <a data-message_id="' . $link_text_id . '" class="cp_quoted_comment_link" href="#">' . $link_text_id . '</a>';
				//
				// 	$output .= '<div data-message_id="' . $link_text_id . '" class="cp_quoted_comment_div" style="background: white; border: solid black 1px; width: 100%; min-height: 50px; padding: 10px; display: none;">';
				//
				// 	$message_query = new WP_Query( [
				// 		'post_type'      => 'chatpress_message',
				// 		'posts_per_page' => -1,
				// 	] );
				//
				// 	if ( $message_query->have_posts() ) {
				//
				// 		while ( $message_query->have_posts() ) {
				//
				// 			$message_query->the_post();
				//
				// 			if ( get_post_meta( get_the_ID(), 'message_number', true ) === $link_text_id ) {
				//
				// 				$output .= get_the_content();
				//
				// 			}
				// 		}
				//
				// 		wp_reset_postdata();
				//
				// 	} else {
				//
				// 		$content .= 'none';
				//
				// 	}
				//
				// 	$output .= '</div>';
				//
				// 	$i = $i + 25;
				//
				// } else {
				//
				// 		$output .= $char;
				//
				// }
				$output .= '!';
			} else {

				$output .= $char;

			}
		}

		return $output;

	}

}

$parser = new parser();
