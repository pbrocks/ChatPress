<?php
class chatpress_settings extends ChatPress {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'wpm_settings_init' ) );
	}

	/**
	* [wpm_settings_init A method to create the settings used by the plugin.]
	*/
	public function wpm_settings_init() {

		add_management_page(
			'ChatPress Options Page',
			'ChatPress',
			'manage_options',
			'options_page',
			[ $this, 'create_admin_page' ]
		);

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

	public function wpm_sanitize( $input ) {

		$valid = [];

		$valid['wpm_show_monitor'] = (bool) empty( $input['cp_prevent_email_cron_creation'] ) ? 0 : 1;

		return $valid;

	}

	public function create_admin_page() {

	}
}
