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
class SpecificationsBlock {

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
		wp_enqueue_script('affiliatex-blocks-specifications', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/blocks/specifications/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/specifications', [
			'render_callback' => [$this, 'render'],
		]);
	}

	public function render($attributes)
{
    $attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);

    $block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
    $layoutStyle = isset($attributes['layoutStyle']) ? $attributes['layoutStyle'] : '';
    $specificationTitle = isset($attributes['specificationTitle']) ? $attributes['specificationTitle'] : '';
    $specificationTable = isset($attributes['specificationTable']) ? $attributes['specificationTable'] : [];
    $edSpecificationTitle = isset($attributes['edSpecificationTitle']) ? $attributes['edSpecificationTitle'] : false;

    $wrapper_attributes = get_block_wrapper_attributes(array(
        'id' => "affiliatex-specification-style-$block_id",
    ));

    $titleHtml = $edSpecificationTitle ? sprintf(
        '<thead>
            <tr>
                <th class="affx-spec-title" colspan="2">%s</th>
            </tr>
        </thead>',
        wp_kses_post($specificationTitle)
    ) : '';

    $tableRowsHtml = '';
    foreach ($specificationTable as $specification) {
        $tableRowsHtml .= sprintf(
            '<tr>
                <td class="affx-spec-label">%s</td>
                <td class="affx-spec-value">%s</td>
            </tr>',
            wp_kses_post($specification['specificationLabel']),
            wp_kses_post($specification['specificationValue'])
        );
    }

    return sprintf(
        '<div %s>
            <div class="affx-specification-block-container">
                <table class="affx-specification-table %s">
                    %s
                    <tbody>
                        %s
                    </tbody>
                </table>
            </div>
        </div>',
        $wrapper_attributes,
        esc_attr($layoutStyle),
        $titleHtml,
        $tableRowsHtml
    );
}
}
