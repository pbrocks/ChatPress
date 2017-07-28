<?php
/**
 *
 * Plugin Name: ChatPress
 * Plugin URI:  https://wordpress.org/plugins/rename-wp-login/
 * Description: This plugin creates a chatboard to embed on pages that keeps the identity of each poster anonymous.
 * Version:     1.0.0
 * Author:      Ben Rothman
 * Author URI:  http://www.BenRothman.org
 * Text Domain: chatpress
 * License:     GPL-2.0+
 */
class ChatPress {

	/**
	 * ChatPress class constructor
	 *
	 * @since 0.1
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'chatpress_channel_function' ], 0 );

		add_action( 'init', [ $this, 'chatpress_message_function' ], 0 );

		add_shortcode( 'chatpress_channel' , [ $this, 'cp_shortcode_function' ] );

		if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {

			require_once dirname( __FILE__ ) . '/cmb2/init.php';

		} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {

			require_once dirname( __FILE__ ) . '/CMB2/init.php';

		}

		add_action( 'cmb2_admin_init', [ $this, 'cp_register_chatpress_channel_metabox' ] );

		add_action( 'cmb2_admin_init', [ $this, 'cp_register_chatpress_message_metabox' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'cp_enqueue_styles' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'cp_enqueue_scripts' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'cp_enqueue_admin_scripts' ] );

		add_action( 'wp_ajax_chatpress_post_message', [ $this, 'chatpress_post_message' ] );

		add_action( 'wp_ajax_chatpress_refresh_message', [ $this, 'chatpress_refresh_message' ] );

		add_action( 'wp_ajax_chatpress_delete_message', [ $this, 'chatpress_delete_message' ] );

		add_action( 'post_submitbox_misc_actions', [ $this, 'cp_add_shortcode_generator_button' ] );

	}

	/**
	 * Register ChatPress Channel CPT
	 *
	 * @since 0.1
	 */
	public function chatpress_channel_function() {

		$labels = [
			'name'                  => _x( 'ChatPress', 'Post Type General Name', 'chatpress' ),
			'singular_name'         => _x( 'ChatPress', 'Post Type Singular Name', 'chatpress' ),
			'menu_name'             => __( 'ChatPress', 'chatpress' ),
			'name_admin_bar'        => __( 'ChatPress', 'chatpress' ),
			'archives'              => __( 'ChatPress Archives', 'chatpress' ),
			'attributes'            => __( 'ChatPress Attributes', 'chatpress' ),
			'parent_item_colon'     => __( 'Parent Channel:', 'chatpress' ),
			'all_items'             => __( 'All Channels', 'chatpress' ),
			'add_new_item'          => __( 'Add New ChatPress', 'chatpress' ),
			'add_new'               => __( 'Add New', 'chatpress' ),
			'new_item'              => __( 'New Item', 'chatpress' ),
			'edit_item'             => __( 'Edit Item', 'chatpress' ),
			'update_item'           => __( 'Update Item', 'chatpress' ),
			'view_item'             => __( 'View Item', 'chatpress' ),
			'view_items'            => __( 'View Items', 'chatpress' ),
			'search_items'          => __( 'Search Item', 'chatpress' ),
			'not_found'             => __( 'Not found', 'chatpress' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'chatpress' ),
			'featured_image'        => __( 'Featured Image', 'chatpress' ),
			'set_featured_image'    => __( 'Set featured image', 'chatpress' ),
			'remove_featured_image' => __( 'Remove featured image', 'chatpress' ),
			'use_featured_image'    => __( 'Use as featured image', 'chatpress' ),
			'insert_into_item'      => __( 'Insert into item', 'chatpress' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'chatpress' ),
			'items_list'            => __( 'Items list', 'chatpress' ),
			'items_list_navigation' => __( 'Items list navigation', 'chatpress' ),
			'filter_items_list'     => __( 'Filter items list', 'chatpress' ),
		];
		$args = [
			'label'                 => __( 'ChatPress', 'chatpress' ),
			'description'           => __( 'Post Type Description', 'chatpress' ),
			'labels'                => $labels,
			'supports'              => [],
			'taxonomies'            => [ 'category', 'post_tag' ],
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'menu_icon'           => 'dashicons-media-document',
		];
		register_post_type( 'chatpress_channel', $args );

	}

	/**
	 * Register ChatPress Message CPT
	 *
	 * @since 0.1
	 */
	public function chatpress_message_function() {

		$labels = [
			'name'                  => _x( 'Message', 'Post Type General Name', 'chatpress' ),
			'singular_name'         => _x( 'Message', 'Post Type Singular Name', 'chatpress' ),
			'menu_name'             => __( 'Message', 'chatpress' ),
			'name_admin_bar'        => __( 'Message', 'chatpress' ),
			'archives'              => __( 'Message Archives', 'chatpress' ),
			'attributes'            => __( 'Message Attributes', 'chatpress' ),
			'parent_item_colon'     => __( 'Parent Message:', 'chatpress' ),
			'all_items'             => __( 'All Messages', 'chatpress' ),
			'add_new_item'          => __( 'Add New Message', 'chatpress' ),
			'add_new'               => __( 'Add New', 'chatpress' ),
			'new_item'              => __( 'New Message', 'chatpress' ),
			'edit_item'             => __( 'Edit Messagge', 'chatpress' ),
			'update_item'           => __( 'Update Message', 'chatpress' ),
			'view_item'             => __( 'View Item', 'chatpress' ),
			'view_items'            => __( 'View Items', 'chatpress' ),
			'search_items'          => __( 'Search Item', 'chatpress' ),
			'not_found'             => __( 'Not found', 'chatpress' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'chatpress' ),
			'featured_image'        => __( 'Featured Image', 'chatpress' ),
			'set_featured_image'    => __( 'Set featured image', 'chatpress' ),
			'remove_featured_image' => __( 'Remove featured image', 'chatpress' ),
			'use_featured_image'    => __( 'Use as featured image', 'chatpress' ),
			'insert_into_item'      => __( 'Insert into item', 'chatpress' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'chatpress' ),
			'items_list'            => __( 'Items list', 'chatpress' ),
			'items_list_navigation' => __( 'Items list navigation', 'chatpress' ),
			'filter_items_list'     => __( 'Filter items list', 'chatpress' ),
		];
		$args = [
			'label'                 => __( 'ChatPress', 'chatpress' ),
			'description'           => __( 'Post Type Description', 'chatpress' ),
			'labels'                => $labels,
			'supports'              => [],
			'taxonomies'            => [ 'category', 'post_tag' ],
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'menu_icon'           => 'dashicons-media-document',
		];
		register_post_type( 'chatpress_message', $args );

	}

	/**
	 * Define 'starts_with' function to be used in code.
	 *
	 * @param string $haystack - string to search through.
	 *
	 * @param string $needle - string to search for.
	 *
	 * @since 0.1
	 */
	public function starts_with( $haystack, $needle ) {

		$length = strlen( $needle );

			 return ( substr( $haystack, 0, $length ) === $needle );

	}

	/**
	 * Shortcode callback function that fires when users put the channel shortcode on a page.
	 *
	 * @param Array $atts - array of each of the parameters and their value specified in the shortcode.
	 *
	 * @since 0.1
	 */
	public function cp_shortcode_function( $atts ) {

		$atts = shortcode_atts( [
			'id'              => false,
			'size'            => false,
			'stick_to_bottom' => false,
			'private'         => false,
			'allowimages'     => false,
		], $atts );

		if ( 'false' === $atts['private'] ) {

				$channel_query = new WP_Query( [
					'post_type' => 'chatpress_channel',
					'p' => $atts['id'],
				] );

			if ( $channel_query->have_posts() ) {

				while ( $channel_query->have_posts() ) {

					$channel_query->the_post();

					$channel_id = get_the_ID();

					$channel_styles = '';

					if ( $this->starts_with( $atts['size'],'100%' ) ) {

						$channel_styles = 'width: 100%;';

					} elseif ( '50% of Container' === $atts['size'] ) {

						$channel_styles = 'width: 50%; height: 600px;';

					} else {

						$channel_styles = 'width: 300px; height: 600px;';
					}

					if ( 'true' === $atts['stick_to_bottom'] ) {

						$channel_styles .= ' position: fixed; bottom: 0%;';

					}

					?>

					 	<div class="chatpress_channel_wrapper" style="<?php echo $channel_styles; ?>" data-index="<?php echo esc_html( $channel_id ); ?>">


							<div style="float: right;">

									<!-- <p style="font-size: 10px; float: left;">autorefresh?&nbsp;</p> -->

									<!-- <div style="font-size: 10px; float: left; color: red;" class="chatpress_timer_div">10&nbsp;</div> -->

									<!-- <input style="font-size: 10px; float: left;" type="checkbox" class="chatpress_autorefresh_link" /><br /> -->

					 				<a href="#" class="chatpress_button_refresh" style="float: right;" data-index="<?php echo esc_html( $channel_id ); ?>"> <i class="fa fa-refresh" aria-hidden="true"></i> Refresh</a>
							</div>

							<p class="chatpress_channel_message_container_title"> <?php echo get_the_title(); ?> </p>

					 		<p class="chatpress_channel_message_container_description"> <?php echo get_the_content(); ?> </p>

								<div class="chatpress_channel_content_container">

									<div class="chatpress_channel_message_container" data-index="<?php echo esc_html( $channel_id ); ?>">
								<?php
									wp_reset_postdata();

									echo $this->cp_populate( $atts['id'] ); ?>

									</div>

									<div class="chatpress_channel_input_container">

										<?php if ( '100% of container' === $atts['size'] ) { ?>

											<input type="text" class="chatpress_text_input chatpress_content_input" placeholder="Message" style="width: 50%; float: left;" data-index="<?php echo esc_html( $channel_id ); ?>"></input>

											<input type="text" class="chatpress_text_input chatpress_style_input" placeholder="Style" style="width: 49%;  margin-right: 1%; float: left;" data-index="<?php echo esc_html( $channel_id ); ?>"></input>

											<input type="button" class="chatpress_button_input" value="Send" style="width: 20%; float: left;" data-index="<?php echo esc_html( $channel_id ); ?>"></input>

										<?php } else {

													if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {

																wp_editor( '', 'editor_' . esc_html( $channel_id ) );?>

																<input type="button" class="chatpress_button_input" value="Send" style="width: 100%; float: left; margin-top: 1%; padding-top: 20px; padding-bottom: 20px !important;" data-index="<?php echo esc_html( $channel_id ); ?>"></input>


													<?php }

										}

										if ( !current_user_can( 'editor' ) &&  !current_user_can( 'administrator' ) ) {?>

											<div style="width: 100%; background: black; color: white; margin-top: 2%; padding: 3% 0px 3% 0px; text-align: center;">Please <a style="color: white;" href="/login">Login</a> to Comment</div>

										<?php } ?>

									</div>

									</div>

								</div>

						</div>

				<?php } // End while().
				}// End if().
		} else {

			echo 'private channel';

		} // End if().

	 	return ' ';

	}


	/**
	 * Add CMB2 fields to ChatPress Channel CPT
	 *
	 * @since 0.1
	 */
	public function cp_register_chatpress_channel_metabox() {
		$prefix = 'chatpress_channel_';

		$cmb_demo = new_cmb2_box( [
			'id'            => $prefix . 'metabox',
			'title'         => esc_html__( 'ChatPress Info', 'cmb2' ),
			'object_types'  => [ 'chatpress_channel' ], // Post type.
		] );

			$cmb_demo->add_field( [
				'name'       => esc_html__( 'Moderator', 'cmb2' ),
				'desc'       => esc_html__( ' ', 'cmb2' ),
				'id'         => $prefix . 'moderator',
				'type'       => 'text',
				'show_on_cb' => 'yourprefix_hide_if_no_cats',
			] );

		$cmb_demo->add_field( [
			'name' => esc_html__( 'Topic', 'cmb2' ),
			'desc' => esc_html__( ' ', 'cmb2' ),
			'id'   => $prefix . 'topic',
			'type' => 'text_small',
		] );

		$cmb_demo->add_field( [
			'name' => esc_html__( 'Rules', 'cmb2' ),
			'desc' => esc_html__( ' ', 'cmb2' ),
			'id'   => $prefix . 'rules',
			'type' => 'textarea',
		] );

		$cmb_demo->add_field( array(
			'name' => esc_html__( 'Image', 'cmb2' ),
			'desc' => esc_html__( 'Upload an image or enter a URL.', 'cmb2' ),
			'id'   => $prefix . 'image',
			'type' => 'file',
		) );

	}

	/**
	 * Add CMB2 fields to ChatPress Message CPT
	 *
	 * @since 0.1
	 */
	public function cp_register_chatpress_message_metabox() {
		$prefix = 'chatpress_message_';

		$cmb_message = new_cmb2_box( [
			'id'            => $prefix . 'metabox',
			'title'         => esc_html__( 'ChatPress Info', 'cmb2' ),
			'object_types'  => [ 'chatpress_message' ], // Post type.
		] );

		// $cmb_message->add_field( array(
		// 	'name' => esc_html__( 'Image', 'cmb2' ),
		// 	'desc' => esc_html__( 'Upload an image or enter a URL.', 'cmb2' ),
		// 	'id'   => $prefix . 'image',
		// 	'type' => 'file',
		// ) );

	}

	/**
	 * Create a 'ChatPress_Message' post
	 *
	 * @since 0.1
	 */
	public function chatpress_post_message() {

		$message = wp_unslash( $_POST['data']['message'] );

		$index = wp_unslash( $_POST['data']['index'] );

		$author = wp_unslash( $_POST['data']['author'] );

		$style = wp_unslash( $_POST['data']['style'] );

		$message_number = $this->random_20_chars();

		// Create post object.
		$my_post = [
		  'post_title'    => $index,
		  'post_type'     => 'chatpress_message',
		  'post_content'  => $message,
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		];

		$message_number = $this->random_20_chars();

		$current_user = wp_get_current_user();

		if ( is_user_logged_in() ) {

			$image = get_avatar( $current_user->user_email, 25 );

		}

		// Insert the post into the database.
		$my_new_post = wp_insert_post( $my_post );

		add_post_meta( $my_new_post, 'author', $author );

		add_post_meta( $my_new_post, 'style', $style );

		add_post_meta( $my_new_post, 'icon', $image );

		add_post_meta( $my_new_post, 'message_number', $message_number );

		$post_container = '<div style="width: 100%; border: solid black 1px; padding-left: 10px;" class="chatpress_message_div" data-index="' . $index . '">';

		if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {

			$post_container .= '<div class="chatpress_message_admin_panel">';

			$post_container .= '<a class="message_delete_link" href="#" data-index="' . get_the_ID() . '">Delete</a>';

			$post_container .= '</div>';

		}

		$post_container .= '<div style="float: left;">' . $image . '</div>';

		$post_container .= '<p style="float: left;">' . '&nbsp;' . date_i18n( 'm/d/y h:m:s', strtotime( 'now' ) ) . '&nbsp;&nbsp;</p><a href="#" class="message_number_link" data-index="' . $index . '" style="font-size: 10px; color: green;" data-message_number="' . $message_number . '">' . $message_number . '</a><br />';

		$post_container .= '<p>' . $message . '</p>';

		$post_container .= '</div>';

		wp_send_json_success( [
			'message' => $post_container,
		] );

	}

	/**
	 *  function to generate and return a random 20 character string of digits
	 *
	 * @since 0.1
	 */
	public function random_20_chars() {

		$final = '';

		for ( $i = 1; $i < 20; $i++ ) {
			$digit = rand( 1, 10 );
			$final .= $digit;
		}


		return $final;
	}

	/**
	 *  PHP function called by AJAX hook to  refresh/repopulate a channel
	 *
	 * @since 0.1
	 */
	public function chatpress_refresh_message() {

		$index = wp_unslash( $_POST['data']['index'] );

		$new_query = $this->cp_populate( $index );

		wp_send_json_success( [

			'query_results' => $new_query,

		] );

	}

	/**
	 *  PHP function called by AJAX hook to  delete a message
	 *
	 * @since 0.1
	 */
	public function chatpress_delete_message() {

		$index = wp_unslash( $_POST['data']['index'] );

		wp_delete_post( $index, false );

		wp_send_json_success( [

			'query_results' => $index,

		] );

	}

	/**
	 *  Add shortcode generator button to ChatPress Channel posts
	 *
	 * @param string $channel_number - channel's index number used to populate the channel with all messages that have the
	 * index as their title (messages have the index of a channel as their title if they belong in that channel).
	 *
	 * @since 0.1
	 */
	public function cp_populate( $channel_number ) {

		$messages = '';

		$message_query = new WP_Query( [
			'post_type'      => 'chatpress_message',
			'title'          => $channel_number,
			'posts_per_page' => -1,
			'order_by'       => 'ASC',
		] );

		if ( $message_query->have_posts() ) {

			while ( $message_query->have_posts() ) {

					$message_query->the_post();

					$messages .= '<div class="chatpress_message_div" data-index="' . get_the_title() . '">';

					if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {

						$messages .= '<div class="chatpress_message_admin_panel">';

						$messages .= '<a class="message_delete_link" href="#" data-index="' . get_the_ID() . '">Delete</a>';

						$messages .= '</div>';

					}

					$messages .= '<div style="float: left;">' . get_post_meta( get_the_ID(), 'icon', true ) . '</div>';

					$messages .= '<p style="float: left;">' . '&nbsp;' . get_post_time( 'm/d/y h:m:s' ) . '&nbsp;&nbsp;</p><a href="#" class="message_number_link" data-index="' . $channel_number . '" style="float: left; color: green; font-size: 10px;" data-message_number="' . get_post_meta( get_the_ID(), 'message_number', true ) . '">' . get_post_meta( get_the_ID(), 'message_number', true ) . '</a>' . '<br /> ';

					$messages .= '<p>' . get_the_content() . '</p>';

					$messages .= '</div>';
			}
		}

			wp_reset_postdata();

			return $messages;

	}

	/**
	 *  Enqueue Stylsheet
	 *
	 * @since 0.1
	 */
	public function cp_enqueue_styles() {

		wp_enqueue_style( 'cp_stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/style.css', [], false, 'all' );

		wp_enqueue_style( 'cp_fontawesome', plugin_dir_url( __FILE__ ) . '/library/fonts/font-awesome-4.7.0/css/font-awesome.min.css', [], false, 'all' );

	}

		/**
		 *  Add shortcode generator button to ChatPress Channel posts
		 *
		 * @since 0.1
		 */
	public function cp_add_shortcode_generator_button() {

			global $post_type;

		if ( 'chatpress_channel' === $post_type ) {

		        $html  = '<div id="major-publishing-actions" style="overflow:hidden">';

		        	$html .= '<div id="publishing-action">';

		        		$html .= '<input type="button" accesskey="p" data-index="' . get_the_ID() . '" tabindex="5" value="Generate Shortcode" class="button-primary chatpress_shortcode_button" id="custom" name="publish">';

		        	$html .= '</div>';

		        $html .= '</div>';

						echo $html ;
		}

	}

	/**
	 * Admin scripts enqueued
	 *
	 * @since 0.1
	 */
	public function cp_enqueue_admin_scripts() {

		wp_register_script( 'cp_backend', plugin_dir_url( __FILE__ ) . '/library/js/chatpress_backend.js', [ 'jquery' ], 'all', true );

		wp_enqueue_script( 'cp_backend' );

		wp_enqueue_style( 'cp_admin_stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/admin_style.css', [], false, 'all' );

	}

	/**
	 * Scripts enqueued
	 *
	 * @since 0.1
	 */
	public function cp_enqueue_scripts() {

		wp_register_script( 'cp_script', plugin_dir_url( __FILE__ ) . '/library/js/chatpress.js', [ 'jquery' ], 'all', true );

		wp_localize_script( 'cp_script', 'cp_script', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		] );

		wp_enqueue_script( 'cp_script' );

		wp_register_script( 'cp_frontend', plugin_dir_url( __FILE__ ) . '/library/js/chatpress_frontend.js', [ 'jquery' ], 'all', true );

		wp_enqueue_script( 'cp_frontend' );

	}

}

$instance = new ChatPress();
