<?php

/**
 * Ajax of AffiliateX.
 *
 * @package AffiliateX
 */

namespace AffiliateX;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class
 *
 * @package AffiliateX
 */
class AffiliateX_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {
		// Initialize hooks.
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_get_block_settings', array( $this, 'get_block_settings' ) );
		add_action( 'wp_ajax_save_block_settings', array( $this, 'save_block_settings' ) );
		add_action( 'wp_ajax_get_customization_settings', array( $this, 'get_customization_settings' ) );
		add_action( 'wp_ajax_save_customization_settings', array( $this, 'save_customization_settings' ) );

	}


	/**
	 * Get Block Settings values.
	 */
	public function get_block_settings() {
		check_ajax_referer( 'affiliatex_ajax_nonce', 'security' );

		$data = affx_get_block_settings( true );
		wp_send_json_success( $data );
	}

	/**
	 * Save Block Settings values.
	 */
	public function save_block_settings() {
		check_ajax_referer( 'affiliatex_ajax_nonce', 'security' );

		$data = isset( $_POST['data'] ) ? affx_clean_vars( json_decode( stripslashes_deep( $_POST['data'] ) ), true, 512, JSON_OBJECT_AS_ARRAY ) : array();
		update_option( 'affiliatex_block_settings', json_encode( $data ) );

		wp_send_json_success( __( 'Saved successfully.', 'affiliatex' ) );
	}

	/**
	 * Get Customization Settings values.
	 */
	public function get_customization_settings() {
		check_ajax_referer( 'affiliatex_ajax_nonce', 'security' );

		$data = affx_get_customization_settings( true );
		wp_send_json_success( $data );
	}

	/**
	 * Save Customization Settings values.
	 */
	public function save_customization_settings() {
		check_ajax_referer( 'affiliatex_ajax_nonce', 'security' );

		$data = isset( $_POST['data'] ) ? affx_clean_vars( json_decode( stripslashes_deep( $_POST['data'] ) ), true, 512, JSON_OBJECT_AS_ARRAY ) : array();

		update_option( 'affiliatex_customization_settings', json_encode( $data ) );

		wp_send_json_success( __( 'Saved successfully.', 'affiliatex' ) );
	}
}

new AffiliateX_Ajax();
