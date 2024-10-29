<?php
/**
 * AffiliateX Button Block
 *
 * @package AffiliateX
 */

namespace AffiliateX\Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class
 *
 * @package AffiliateX
 */
class VersusLineBlock {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init();
	}

	public function init()
	{
		add_action('enqueue_block_editor_assets', [$this, 'enqueue_block']);
		add_action('init', [$this, 'register_block']);
	}

	public function enqueue_block()
	{
		wp_enqueue_script('affiliatex-blocks-versus-line', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/blocks/versus-line/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/versus-line', [
			'render_callback' => [$this, 'render'],
		]);
	}

	public function render($attributes)
	{
		$attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);
		$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
		$versusTable = isset($attributes['versusTable']) ? $attributes['versusTable'] : [];
		$vsLabel = isset($attributes['vsLabel']) ? $attributes['vsLabel'] : '';

		$wrapper_attributes = get_block_wrapper_attributes(array(
			'id' => "affiliatex-versus-line-style-$block_id",
			'class' => "affx-versus-line-block-container",
		));


		$rowsHtml = '';
		foreach ($versusTable as $item) {
			$rowsHtml .= sprintf(
				'<tr>
					<td class="data-label">
						<span>%s</span>
						<span class="data-info">%s</span>
					</td>
					<td>%s</td>
					<td>
						<span class="affx-vs-icon">%s</span>
					</td>
					<td>%s</td>
				</tr>',
				wp_kses_post($item['versusTitle']),
				wp_kses_post($item['versusSubTitle']),
				wp_kses_post($item['versusValue1']),
				wp_kses_post($vsLabel),
				wp_kses_post($item['versusValue2'])
			);
		}

		return sprintf(
			'<div %s>
				<div class="affx-versus-table-wrap">
					<table class="affx-product-versus-table">
						<tbody>%s</tbody>
					</table>
				</div>
			</div>',
			$wrapper_attributes,
			$rowsHtml
		);
	}
}
