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

	public function __construct() {

		add_action( 'init', [ $this, 'ChatPress_Channel_Function' ], 0 );

		add_action( 'init', [ $this, 'ChatPress_Message_Function' ], 0 );

		add_filter( 'manage_chatpress_channel_posts_columns', [ $this, 'set_custom_edit_chatpress_channel_columns' ] );
		add_action( 'manage_chatpress_channel_posts_custom_column' , [ $this, 'custom_chatpress_channel_column' ], 10, 2 );

		add_shortcode( 'chatpress_channel' , [ $this, 'shortcode_function' ] );

		if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {

			require_once dirname( __FILE__ ) . '/cmb2/init.php';

		} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {

			require_once dirname( __FILE__ ) . '/CMB2/init.php';

		}

		add_action( 'cmb2_admin_init', [ $this, 'cp_register_chatpress_metabox' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'cp_enqueue_styles' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'cp_enqueue_scripts' ] );

		add_action( 'wp_ajax_chatpress_post_message', [ $this, 'chatpress_post_message' ] );

	}

	// Register Custom Post Type
	public function ChatPress_Channel_Function() {

		$labels = array(
			'name'                  => _x( 'Channel', 'Post Type General Name', 'chatpress' ),
			'singular_name'         => _x( 'Channel', 'Post Type Singular Name', 'chatpress' ),
			'menu_name'             => __( 'Channel', 'chatpress' ),
			'name_admin_bar'        => __( 'Channel', 'chatpress' ),
			'archives'              => __( 'Channel Archives', 'chatpress' ),
			'attributes'            => __( 'Channel Attributes', 'chatpress' ),
			'parent_item_colon'     => __( 'Parent Channel:', 'chatpress' ),
			'all_items'             => __( 'All Channels', 'chatpress' ),
			'add_new_item'          => __( 'Add New Channel', 'chatpress' ),
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
		);
		$args = array(
			'label'                 => __( 'ChatPress', 'chatpress' ),
			'description'           => __( 'Post Type Description', 'chatpress' ),
			'labels'                => $labels,
			'supports'              => array( ),
			'taxonomies'            => array( 'category', 'post_tag' ),
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
		);
		register_post_type( 'chatpress_channel', $args );

	}

		// Register Custom Post Type
	public function ChatPress_Message_Function() {

		$labels = array(
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
		);
		$args = array(
			'label'                 => __( 'ChatPress', 'chatpress' ),
			'description'           => __( 'Post Type Description', 'chatpress' ),
			'labels'                => $labels,
			'supports'              => array( ),
			'taxonomies'            => array( 'category', 'post_tag' ),
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
		);
		register_post_type( 'chatpress_message', $args );

	}

	public function set_custom_edit_chatpress_channel_columns( $columns ) {

	    $columns['Shortcode'] = __( 'Shortcode', 'chatpress' );

    	return $columns;
	}

	public function custom_chatpress_channel_column( $column, $post_id ) {
	    switch ( $column ) {

	        case 'Shortcode' :

	            echo '[chatpress_channel id="' . $post_id . '"]';

	        break;

	    }

	}



	public function shortcode_function( $atts ) {

	$atts = shortcode_atts( array(

		'id' => false,

	), $atts );

	$the_query = new WP_Query( [ 'post_type' => 'chatpress_channel', 'p' => $atts['id'] ] );

	if ( $the_query->have_posts() ) {



		while ( $the_query->have_posts() ) {

			$the_query->the_post();

			echo '<div class="chatpress_channel_wrapper">';

			echo '<h1>' . get_the_title() . '</h1>';

			echo get_the_content();

				echo '<div class="chatpress_channel_content_container">';

					echo '<div class="chatpress_channel_message_container">';

						// messages go here

					echo '</div>';

						echo '<div class="chatpress_channel_input_container">';

							echo '<input type="text" class="chatpress_text_input" style="width: 80%; float: left;" data-index="' . get_the_ID() . '"></input>';

							echo '<input type="button" class="chatpress_button_input" value="Send" style="width: 20%; float: left;" data-index="' . get_the_ID() . '"></input>';

					echo '</div>';

				echo '</div>';

			echo '</div>';

		}



		wp_reset_postdata();

	}

	return get_the_title();

}




/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function cp_register_chatpress_metabox() {
	$prefix = 'chatpress_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'ChatPress Info', 'cmb2' ),
		'object_types'  => array( 'chatpress_channel' ), // Post type
		// 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
		// 'context'    => 'normal',
		// 'priority'   => 'high',
		// 'show_names' => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
		// 'classes'    => 'extra-class', // Extra cmb2-wrap classes
		// 'classes_cb' => 'yourprefix_add_some_classes', // Add classes through a callback.
	) );

	$cmb_demo->add_field( array(
		'name'       => esc_html__( 'Test Text', 'cmb2' ),
		'desc'       => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'         => $prefix . 'text',
		'type'       => 'text',
		'show_on_cb' => 'yourprefix_hide_if_no_cats', // function should return a bool value
		// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
		// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
		// 'on_front'        => false, // Optionally designate a field to wp-admin only
		// 'repeatable'      => true,
		// 'column'          => true, // Display field value in the admin post-listing columns
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Text Small', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textsmall',
		'type' => 'text_small',
		// 'repeatable' => true,
		// 'column' => array(
		// 	'name'     => esc_html__( 'Column Title', 'cmb2' ), // Set the admin column title
		// 	'position' => 2, // Set as the second column.
		// );
		// 'display_cb' => 'yourprefix_display_text_small_column', // Output the display of the column values through a callback.
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Text Medium', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textmedium',
		'type' => 'text_medium',
	) );

	$cmb_demo->add_field( array(
		'name'       => esc_html__( 'Read-only Disabled Field', 'cmb2' ),
		'desc'       => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'         => $prefix . 'readonly',
		'type'       => 'text_medium',
		'default'    => esc_attr__( 'Hey there, I\'m a read-only field', 'cmb2' ),
		'save_field' => false, // Disables the saving of this field.
		'attributes' => array(
			'disabled' => 'disabled',
			'readonly' => 'readonly',
		),
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Custom Rendered Field', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'render_row_cb',
		'type' => 'text',
		'render_row_cb' => 'yourprefix_render_row_cb',
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Website URL', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'url',
		'type' => 'text_url',
		// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Text Email', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'email',
		'type' => 'text_email',
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Time', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'time',
		'type' => 'text_time',
		// 'time_format' => 'H:i', // Set to 24hr format
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Time zone', 'cmb2' ),
		'desc' => esc_html__( 'Time zone', 'cmb2' ),
		'id'   => $prefix . 'timezone',
		'type' => 'select_timezone',
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Date Picker', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textdate',
		'type' => 'text_date',
		// 'date_format' => 'Y-m-d',
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Date Picker (UNIX timestamp)', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textdate_timestamp',
		'type' => 'text_date_timestamp',
		// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Date/Time Picker Combo (UNIX timestamp)', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'datetime_timestamp',
		'type' => 'text_datetime_timestamp',
	) );

	// This text_datetime_timestamp_timezone field type
	// is only compatible with PHP versions 5.3 or above.
	// Feel free to uncomment and use if your server meets the requirement
	// $cmb_demo->add_field( array(
	// 	'name' => esc_html__( 'Test Date/Time Picker/Time zone Combo (serialized DateTime object)', 'cmb2' ),
	// 	'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
	// 	'id'   => $prefix . 'datetime_timestamp_timezone',
	// 	'type' => 'text_datetime_timestamp_timezone',
	// ) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Money', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textmoney',
		'type' => 'text_money',
		// 'before_field' => '£', // override '$' symbol if needed
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name'    => esc_html__( 'Test Color Picker', 'cmb2' ),
		'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => $prefix . 'colorpicker',
		'type'    => 'colorpicker',
		'default' => '#ffffff',
		// 'attributes' => array(
		// 	'data-colorpicker' => json_encode( array(
		// 		'palettes' => array( '#3dd0cc', '#ff834c', '#4fa2c0', '#0bc991', ),
		// 	) ),
		// ),
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Text Area', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textarea',
		'type' => 'textarea',
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Text Area Small', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textareasmall',
		'type' => 'textarea_small',
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Text Area for Code', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'textarea_code',
		'type' => 'textarea_code',
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Title Weeeee', 'cmb2' ),
		'desc' => esc_html__( 'This is a title description', 'cmb2' ),
		'id'   => $prefix . 'title',
		'type' => 'title',
	) );

	$cmb_demo->add_field( array(
		'name'             => esc_html__( 'Test Select', 'cmb2' ),
		'desc'             => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'               => $prefix . 'select',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'standard' => esc_html__( 'Option One', 'cmb2' ),
			'custom'   => esc_html__( 'Option Two', 'cmb2' ),
			'none'     => esc_html__( 'Option Three', 'cmb2' ),
		),
	) );

	$cmb_demo->add_field( array(
		'name'             => esc_html__( 'Test Radio inline', 'cmb2' ),
		'desc'             => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'               => $prefix . 'radio_inline',
		'type'             => 'radio_inline',
		'show_option_none' => 'No Selection',
		'options'          => array(
			'standard' => esc_html__( 'Option One', 'cmb2' ),
			'custom'   => esc_html__( 'Option Two', 'cmb2' ),
			'none'     => esc_html__( 'Option Three', 'cmb2' ),
		),
	) );

	$cmb_demo->add_field( array(
		'name'    => esc_html__( 'Test Radio', 'cmb2' ),
		'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => $prefix . 'radio',
		'type'    => 'radio',
		'options' => array(
			'option1' => esc_html__( 'Option One', 'cmb2' ),
			'option2' => esc_html__( 'Option Two', 'cmb2' ),
			'option3' => esc_html__( 'Option Three', 'cmb2' ),
		),
	) );

	$cmb_demo->add_field( array(
		'name'     => esc_html__( 'Test Taxonomy Radio', 'cmb2' ),
		'desc'     => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'       => $prefix . 'text_taxonomy_radio',
		'type'     => 'taxonomy_radio',
		'taxonomy' => 'category', // Taxonomy Slug
		// 'inline'  => true, // Toggles display to inline
	) );

	$cmb_demo->add_field( array(
		'name'     => esc_html__( 'Test Taxonomy Select', 'cmb2' ),
		'desc'     => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'       => $prefix . 'taxonomy_select',
		'type'     => 'taxonomy_select',
		'taxonomy' => 'category', // Taxonomy Slug
	) );

	$cmb_demo->add_field( array(
		'name'     => esc_html__( 'Test Taxonomy Multi Checkbox', 'cmb2' ),
		'desc'     => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'       => $prefix . 'multitaxonomy',
		'type'     => 'taxonomy_multicheck',
		'taxonomy' => 'post_tag', // Taxonomy Slug
		// 'inline'  => true, // Toggles display to inline
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Checkbox', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => $prefix . 'checkbox',
		'type' => 'checkbox',
	) );

	$cmb_demo->add_field( array(
		'name'    => esc_html__( 'Test Multi Checkbox', 'cmb2' ),
		'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => $prefix . 'multicheckbox',
		'type'    => 'multicheck',
		// 'multiple' => true, // Store values in individual rows
		'options' => array(
			'check1' => esc_html__( 'Check One', 'cmb2' ),
			'check2' => esc_html__( 'Check Two', 'cmb2' ),
			'check3' => esc_html__( 'Check Three', 'cmb2' ),
		),
		// 'inline'  => true, // Toggles display to inline
	) );

	$cmb_demo->add_field( array(
		'name'    => esc_html__( 'Test wysiwyg', 'cmb2' ),
		'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => $prefix . 'wysiwyg',
		'type'    => 'wysiwyg',
		'options' => array(
			'textarea_rows' => 5,
		),
	) );

	$cmb_demo->add_field( array(
		'name' => esc_html__( 'Test Image', 'cmb2' ),
		'desc' => esc_html__( 'Upload an image or enter a URL.', 'cmb2' ),
		'id'   => $prefix . 'image',
		'type' => 'file',
	) );

	$cmb_demo->add_field( array(
		'name'         => 'Testing Field Parameters',
		'id'           => $prefix . 'parameters',
		'type'         => 'text',
		'before_row'   => 'yourprefix_before_row_if_2', // callback.
		'before'       => '<p>Testing <b>"before"</b> parameter</p>',
		'before_field' => '<p>Testing <b>"before_field"</b> parameter</p>',
		'after_field'  => '<p>Testing <b>"after_field"</b> parameter</p>',
		'after'        => '<p>Testing <b>"after"</b> parameter</p>',
		'after_row'    => '<p>Testing <b>"after_row"</b> parameter</p>',
	) );

}

	public function chatpress_post_message() {

		$message = $_POST['data']['message'];

		$index = $_POST['data']['index'];

		// Create post object
		$my_post = array(
		  'post_title'    => $index,
		  'post_type'     => 'chatpress_message',
		  'post_content'  => $message,
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		);
		 
		// Insert the post into the database
		wp_insert_post( $my_post );

		wp_send_json_success();

	}

	public function cp_enqueue_styles() {

		wp_enqueue_style( 'cp_stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/style.css', array(), false, 'all' );

	}

	public function cp_enqueue_scripts() {

		wp_register_script( 'cp_script', plugin_dir_url( __FILE__ ) . '/library/js/chatpress.js', array( 'jquery' ), 'all', true );

		wp_localize_script( 'cp_script', 'cp_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		wp_enqueue_script( 'cp_script' );

	}

}

$instance = new ChatPress();