<?php

class Crontask extends ChatPress {
	public function __construct() {

		add_filter( 'cron_schedules', [ $this, 'custom_cron_schedules' ] );

		if ( ! wp_next_scheduled( 'cp_delete_old_messages' ) ) {
			wp_schedule_event( time(), 'weekly', 'cp_delete_old_messages' );
		}

		add_action( 'cp_delete_old_messages', [ $this, 'cp_delete_old_messages' ] );

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

	public function cp_delete_old_messages() {
			$the_query = new WP_Query( [
				'post_type' => 'chatpress_message',
				'date_query' => array(
					'before' => date('Y-m-d', strtotime('-7 days'))
    		)
			 ] );

			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					wp_delete_post( get_the_ID() );
				}

				wp_reset_postdata();
			}

		}

}

$crontask = new crontask();
