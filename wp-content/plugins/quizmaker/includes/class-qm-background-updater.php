<?php
/**
 * Background Updater
 *
 * Uses https://github.com/A5hleyRich/wp-background-processing to handle DB
 * updates in the background.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( 'libraries/wp-async-request.php' );
include_once( 'libraries/wp-background-process.php' );

/**
 * WC_Background_Updater Class.
 */
class QM_Background_Updater extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'qm_updater';

	/**
	 * Dispatch updater.
	 *
	 * Updater will still run via cron job if this fails for any reason.
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();
		$logger     = new QM_Logger();

		if ( is_wp_error( $dispatched ) ) {
			$logger->add( 'qm_db_updates', sprintf( 'Unable to dispatch Quizmaker updater: %s', $dispatched->get_error_message() ) );
		}
	}

	/**
	 * Handle cron healthcheck
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running.
			return;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			$this->clear_scheduled_event();
			return;
		}

		$this->handle();
	}

	/**
	 * Schedule fallback event.
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Is the updater running?
	 * @return boolean
	 */
	public function is_updating() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param string $callback Update callback function
	 * @return mixed
	 */
	protected function task( $callback ) {
		if ( ! defined( 'QM_UPDATING' ) ) {
			define( 'QM_UPDATING', true );
		}

		$logger = new QM_Logger();

		include_once( 'qm-update-functions.php' );

		if ( is_callable( $callback ) ) {
			$logger->add( 'qm_db_updates', sprintf( 'Running %s callback', $callback ) );
			call_user_func( $callback );
			$logger->add( 'qm_db_updates', sprintf( 'Finished %s callback', $callback ) );
		} else {
			$logger->add( 'qm_db_updates', sprintf( 'Could not find %s callback', $callback ) );
		}

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		$logger = new QM_Logger();
		$logger->add( 'qm_db_updates', 'Data update complete' );
		QM_Install::update_db_version();
		
		parent::complete();
	}
}
