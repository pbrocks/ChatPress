<?php
/*
 * Plugin Name: ChatPress
 * Plugin URI:  https://wordpress.org/plugins/rename-wp-login/
 * Description: Creates an chatroom to embed on on the site.
 * Version:     1.0.0
 * Author:      Ben Rothman
 * Author URI:  http://www.BenRothman.org
 * Text Domain: chatpress
 * License:     GPL-2.0+
 */

class ChatPress {

	public static $options;

	/**
	* ChatPress class constructor
	*
	* @since 0.1
	*/
	public function __construct() {

		self::$options = get_option( 'cp_options', [
			'cp_delete_messages_after'       => 'weekly',
			'cp_prevent_email_cron_creation' => 0,
			'rick'                           => true,
			'morty'                          => false,
		] );

		update_option( 'cp_options', self::$options );

		add_action( 'init', [ $this, 'chatpress_channel_function' ], 0 );

		add_action( 'init', [ $this, 'chatpress_message_function' ], 0 );

		add_shortcode( 'chatpress_channel', [ $this, 'cp_shortcode_function' ] );

		$this->init();

	}

	/**
	* Runs additional actions to be performed at startup.
	*
	* @since 0.1
	*/
	public function init() {

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

		add_action( 'wp_loaded', [ $this, 'page_loaded' ] );

		add_filter( 'cron_schedules', [ $this, 'custom_cron_schedules' ] );

		add_action( 'init', [ $this, 'cp_create_crontask' ] );

		add_action( 'admin_init', array( $this, 'wpm_settings_init' ) );

	}

	/**
	* [wpm_settings_init A method to create the settings used by the plugin.]
	*/
	public function wpm_settings_init() {
			register_setting(
				'cp_options',
				'cp_options',
				[ $this, 'wpm_sanitize' ]
			);

			register_setting(
				'cp_prevent_email_cron_creation',
				'cp_prevent_email_cron_creation',
				[ $this, 'wpm_sanitize' ]
			);

	}

	/**
	*  Make the crontask to erase old messages
	*
	* @since 0.1
	*/
	public function cp_create_crontask() {

		self::$options['cp_delete_messages_after'] = 'daily';

		update_option( 'cp_options', self::$options );

		$cp_how_often = self::$options['cp_delete_messages_after'];

		if ( 0 === self::$options['cp_prevent_email_cron_creation'] ) {

			wp_schedule_event( time(), 'weekly', 'cp_delete_old_messages' );

			self::$options['cp_prevent_email_cron_creation'] = 1;

			update_option( 'cp_options', self::$options );
		}

	}

	public function wpm_sanitize( $input ) {

		$valid = [];

		$valid['wpm_show_monitor'] = (bool) empty( $input['cp_prevent_email_cron_creation'] ) ? 0 : 1;

		return $valid;

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
		$args   = [
			'label'               => __( 'ChatPress', 'chatpress' ),
			'description'         => __( 'Post Type Description', 'chatpress' ),
			'labels'              => $labels,
			'supports'            => [],
			'taxonomies'          => [ 'category', 'post_tag' ],
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-media-document',
		];
		register_post_type( 'chatpress_channel', $args );

	}

	/**
	*  Add Generate Shortcode button to ChatPress Channel post-type.
	*
	* @since 0.1
	*/
	public function cp_add_shortcode_generator_button() {

		global $post_type;

		if ( 'chatpress_channel' === $post_type ) {

				$html = '<div id="major-publishing-actions" style="overflow:hidden">';

					$html .= '<div id="publishing-action">';

						$html .= '<input type="button" accesskey="p" data-index="' . get_the_ID() . '" tabindex="5" value="Generate Shortcode" class="button-primary chatpress_shortcode_button" id="custom" name="publish">';

					$html .= '</div>';

				$html .= '</div>';

				echo $html;
		}

	}

	/**
	 * Add CMB2 fields to ChatPress Channel post-type.
	 *
	 * @since 0.1
	 */
	public function cp_register_chatpress_channel_metabox() {
		$prefix = 'chatpress_channel_';

		$cmb_demo = new_cmb2_box( [
			'id'           => $prefix . 'metabox',
			'title'        => esc_html__( 'ChatPress', 'cmb2' ),
			'object_types' => [ 'chatpress_channel' ], // Post type.
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
			'name'    => esc_html__( 'Background Color', 'cmb2' ),
			'desc'    => '',
			'id'      => $prefix . 'color',
			'type'    => 'colorpicker',
			'default' => '#ffffff',
		) );

		$cmb_demo->add_field( array(
			'name' => esc_html__( 'Image', 'cmb2' ),
			'desc' => esc_html__( 'Upload an image or enter a URL.', 'cmb2' ),
			'id'   => $prefix . 'image',
			'type' => 'file',
		) );

	}

	/**
	 * Shortcode callback function for when users use the channel shortcode.
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
				'p'         => $atts['id'],
			] );

			if ( $channel_query->have_posts() ) {

				while ( $channel_query->have_posts() ) {

					$channel_query->the_post();

					$channel_id = get_the_ID();

					$channel_styles = '';

					if ( $this->starts_with( $atts['size'], '100%' ) ) {

						$channel_styles = 'width: 100%;';

					} elseif ( '50% of Container' === $atts['size'] ) {

						$channel_styles = 'width: 50%; height: 600px;';

					} else {

						$channel_styles = 'width: 300px; height: 600px;';
					}

					if ( 'true' === $atts['stick_to_bottom'] ) {

						$channel_styles .= ' position: fixed; bottom: 0%;';

					}

						$background = 'background: ' . get_post_meta( $atts['id'], 'chatpress_channel_color', true ) . ';';

					if ( '' !== get_post_meta( $atts['id'], 'chatpress_channel_image', true ) ) {

								$background = 'background: url(' . get_post_meta( $atts['id'], 'chatpress_channel_image', true ) . ');';

					}
				?>

					<div class="chatpress_channel_wrapper" style="<?php echo esc_html( $background ) . ' ' . esc_html( $channel_styles ); ?>" data-index="<?php echo esc_html( $channel_id ); ?>">

						<div style="float: right;">
								<a href="#" class="chatpress_button_refresh" style="float: right;" data-index="<?php echo esc_html( $channel_id ); ?>"> <i class="fa fa-refresh" aria-hidden="true"></i> Refresh</a>
						</div>

						<p class="chatpress_channel_message_container_title"> <?php echo get_the_title(); ?> </p>

						<div class="chatpress_title_hover_div">
							<p> Moderator: <?php echo esc_html( get_post_meta( get_the_ID(), 'chatpress_channel_moderator', true ) ); ?><br />
									Topic: <?php echo esc_html( get_post_meta( get_the_ID(), 'chatpress_channel_topic', true ) ); ?>
							</p>
						</div>

						<p class="chatpress_channel_message_container_description"> <?php echo get_the_content(); ?> </p>

								<div class="chatpress_channel_message_container" data-index="<?php echo esc_html( $channel_id ); ?>">
							<?php
								wp_reset_postdata();

								echo $this->cp_populate( $atts['id'] );

								?>

								</div>

							<div class="chatpress_channel_input_container" style="float: left;">

									<input type="text" class="chatpress_text_input chatpress_content_input" placeholder="Message" style="width: 50%; float: left;" data-index="<?php echo esc_html( $channel_id ); ?>"></input>

									<input type="button" class="chatpress_button_input" value="Send" style="float: left;" data-index="<?php echo esc_html( $channel_id ); ?>"></input>

							</div>

							</div>

					</div>

			<?php

				} // End while().
			}// End if().
		} else {

			echo 'private channel';

		} // End if().

		return ' ';

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
		$args   = [
			'label'               => __( 'ChatPress', 'chatpress' ),
			'description'         => __( 'Post Type Description', 'chatpress' ),
			'labels'              => $labels,
			'supports'            => [],
			'taxonomies'          => [ 'category', 'post_tag' ],
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-media-document',
		];
		register_post_type( 'chatpress_message', $args );

	}

	/**
	 * Add CMB2 fields to ChatPress Message post-type.
	 *
	 * @since 0.1
	 */
	public function cp_register_chatpress_message_metabox() {
		$prefix = 'chatpress_message_';

		$cmb_message = new_cmb2_box( [
			'id'           => $prefix . 'metabox',
			'title'        => esc_html__( 'ChatPress Info', 'cmb2' ),
			'object_types' => [ 'chatpress_message' ], // Post type.
		] );

	}

	/**
	 *  Populate the channel content container with posts that belong in that channel.
	 *
	 * @param int $channel_number - number of the current channel.
	 *
	 * @since 0.1
	 */
	public function cp_populate( $channel_number ) {

		$messages = '';

		$message_id = '';

		// Create the query + query the database
		$message_query = new WP_Query( [
			'post_type'      => 'chatpress_message',
			'title'          => $channel_number,
			'posts_per_page' => -1,
			'order_by'       => 'ASC',
		] );

		if ( $message_query->have_posts() ) {

			while ( $message_query->have_posts() ) {

					$message_query->the_post();

					$messages .= '<div class="chatpress_message_div" data-index="' . get_the_title() . '" data-id="' . get_post_meta( get_the_ID(), 'message_id', true ) . '">';

				if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {

						$messages .= '<div class="chatpress_message_admin_panel">';

						$messages .= '<a class="message_delete_link" href="#" data-index="' . get_the_ID() . '" data-message_id="' . get_post_meta( get_the_ID(), 'message_id', true ) . '">Delete</a>';

						$messages .= '</div>';

				}

					$messages .= '<p class="chatpress_message_datetime" style="float: left;">&nbsp;' . get_post_time( 'm/d/y h:m:s' ) . '&nbsp;&nbsp;</p>';

					$messages .= '<a href="#" class="message_id_link" data-index="' . $channel_number . '" style="float: left; color: green; font-size: 10px;" data-message_id="' . get_post_meta( get_the_ID(), 'message_id', true ) . '">' . get_post_meta( get_the_ID(), 'message_id', true ) . '</a>' . '<br /> ';

					$messages .= '<p>' . get_the_content() . '</p>';

					$messages .= '</div>';
			}
		}

			wp_reset_postdata();

			return $messages;

	}

	/**
	 * Create a 'ChatPress_Message' post + refresh posts in channel
	 *
	 * @since 0.1
	 */
	public function chatpress_post_message() {

		$index = wp_unslash( $_POST['data']['index'] );

		$message = wp_unslash( $_POST['data']['message'] );

		//$message = $this->cp_parse( $message );

		$message_id = $this->random_20_chars();

		// Create post object.
		$my_post = [
			'post_title'   => $index,
			'post_type'    => 'chatpress_message',
			'post_content' => $message,
			'post_status'  => 'publish',
			'post_author'  => 1,
		];

		// Insert the post into the database.
		$my_new_post = wp_insert_post( $my_post );
		//wp_insert_post( $my_post );

		add_post_meta( $my_new_post, 'message_id', $message_id );

		wp_send_json_success( [
			'message' => 'posted new message',
		] );

	}

	/**
	 *  PHP function called by AJAX hook to refresh a channel
	 *
	 * @since 0.1
	 */
	public function chatpress_refresh_message() {

		$index = wp_unslash( $_POST['data'] );

		$new_html = $this->cp_populate( $index );

		wp_send_json_success( $new_html );
	}

	/**
	 *  PHP function called by AJAX hook to delete a message
	 *
	 * @since 0.1
	 */
	public function chatpress_delete_message() {

		$message_id = wp_unslash( $_POST['data'] );

		$message_query = new WP_Query( [
			'post_type'      => 'chatpress_message',
			'posts_per_page' => -1,
		] );

		if ( $message_query->have_posts() ) {

			while ( $message_query->have_posts() ) {

				$message_query->the_post();

				if ( get_post_meta( get_the_ID(), 'message_id', true ) === $message_id ) {

					wp_delete_post( get_the_ID(), false );

				}
			}

			wp_reset_postdata();

		} else {

			$content .= 'none';

		}

		wp_send_json_success( 'message id:' . $message_id );

	}

	/**
	 * Parse the input string.
	 *
	 * @param string $input - string to parse.
	 *
	 * @since 0.1
	 */
	public function cp_parse( $input ) {

		$strlen = strlen( $input ) + 1;

		$output = '';

		$x = 1;

		for ( $i = 0; $i <= $strlen; $i++ ) {

			$char = substr( $input, $i, 1 );

			$preceeding_char = substr( $input, $i - 1, 1 );

			if ( '{' === $char ) {

				if ( '{' === $preceeding_char ) {

					$output = substr( $output, 0, $i - 1 );

					$link_text_id = substr( $input, $i + 1, 21 );

					$output .= '>> <a data-message_id="' . $link_text_id . '" class="cp_quoted_comment_link" href="#">' . $link_text_id . '</a>';

					$output .= '<div data-message_id="' . $link_text_id . '" class="cp_quoted_comment_div" style="background: white; border: solid black 1px; width: 100%; min-height: 50px; padding: 10px; display: none;">';

					$message_query = new WP_Query( [
						'post_type'      => 'chatpress_message',
						'posts_per_page' => -1,
					] );

					if ( $message_query->have_posts() ) {

						while ( $message_query->have_posts() ) {

							$message_query->the_post();

							if ( get_post_meta( get_the_ID(), 'message_number', true ) === $link_text_id ) {

								$output .= get_the_content();

							}
						}

						wp_reset_postdata();

					} else {

						$content .= 'none';

					}

					$output .= '</div>';

					$i = $i + 25;

				} else {

						$output .= $char;

				}
			} else {

				$output .= $char;

			}
		}

		return $output;

	}

	/**
		* Create cron schedule for garbage collection
		*
		* @param Object $schedules - string to search through.
		*
		* @since 0.1
		*/
	public function custom_cron_schedules( $schedules ) {

		if ( ! isset( $schedules['weekly'] ) ) {

			$schedules['weekly'] = array(
				'interval' => 604800,
				'display'  => __( 'Once Per Week' ),
			);

		}

		if ( ! isset( $schedules['monthly'] ) ) {

			$schedules['monthly'] = array(
				'interval' => 2628000,
				'display'  => __( 'Once Per Month' ),
			);

		}

		return $schedules;

	}


			/**
			 *  Function to generate and return a random 20 character string of digits
			 *
			 * @since 0.1
			 */
	public function random_20_chars() {

		$final = '';

		for ( $i = 1; $i < 20; $i++ ) {
			$digit  = rand( 1, 10 );
			$final .= $digit;
		}

		// BUG: CHECK IF STRING HAS ALREADY BEEN USED

		return $final;
	}

	/**
	 * Runs functions on page load (front end)
	 *
	 * @since 0.1
	 */
	public function page_loaded() {

	}

	/**
	 * Define 'starts_with' function.
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
	 *  Normal Stylsheets enqueued + FontAwesome enqueued
	 *
	 * @since 0.1
	 */
	public function cp_enqueue_styles() {

		wp_enqueue_style( 'cp_stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/style.css', [], false, 'all' );

		wp_enqueue_style( 'cp_fontawesome', plugin_dir_url( __FILE__ ) . '/library/fonts/font-awesome-4.7.0/css/font-awesome.min.css', [], false, 'all' );

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
	 * Normal scripts enqueued
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
