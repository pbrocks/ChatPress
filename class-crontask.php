<?php

class crontask extends ChatPress {
	public function __construct() {

		add_filter( 'cron_schedules', [ $this, 'custom_cron_schedules' ] );

		add_action( 'init', [ $this, 'cp_create_crontask' ] );

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
}

$crontask = new crontask();
