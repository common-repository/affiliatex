<?php
namespace AffiliateX\Notice;

defined('ABSPATH') || exit;

/**
 * This class handles all necessary functionalities for admin notices,
 * like enqueue assets to admin panel to handle notices from JS side,
 * initializes all notices, vice versa.
 *
 * @package AffiliateX
 */
class NoticeHandler{

    public function __construct()
    {
        add_action('admin_init', [$this, 'set_first_applied_time']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

        new ReviewNotice();
        new CampaignNoticeHandler();
    }

    /**
     * Set first initiation time
     *
     * @return void
     */
    public function set_first_applied_time() : void
    {
        $first_initiated_at = get_option( 'affiliatex_notice_initiated' );

		if ( ! $first_initiated_at ) {
			update_option( 'affiliatex_notice_initiated', time() );
		}
    }

    /**
     * Enqueue assets to handle notices in admin panel
     *
     * @return void
     */
    public function enqueue_assets() : void
    {
        $notice_deps = include_once plugin_dir_path( AFFILIATEX_PLUGIN_FILE ) . '/build/noticesJS.asset.php';
		wp_enqueue_script('affiliatex-notices', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/noticesJS.js', $notice_deps['dependencies'], $notice_deps['version'], true);

        wp_localize_script(
			'affiliatex-notices',
			'AffiliateXNotice',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'affiliatex_ajax_nonce' ),
			]
		);
    }
}

new NoticeHandler();
