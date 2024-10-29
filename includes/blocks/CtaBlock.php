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
class CtaBlock {

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
		wp_enqueue_script('affiliatex-blocks-cta', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/blocks/cta/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/cta', [
			'render_callback' => [$this, 'render'],
		]);
	}

	public function render($attributes, $content)
	{
		$attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);

		$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
		$ctaTitle = isset($attributes['ctaTitle']) ? $attributes['ctaTitle'] : '';
		$ctaContent = isset($attributes['ctaContent']) ? $attributes['ctaContent'] : '';
		$ctaBGType = isset($attributes['ctaBGType']) ? $attributes['ctaBGType'] : '';
		$ctaLayout = isset($attributes['ctaLayout']) ? $attributes['ctaLayout'] : '';
		$ctaAlignment = isset($attributes['ctaAlignment']) ? $attributes['ctaAlignment'] : '';
		$columnReverse = isset($attributes['columnReverse']) ? $attributes['columnReverse'] : false;
		$ctaButtonAlignment = isset($attributes['ctaButtonAlignment']) ? $attributes['ctaButtonAlignment'] : '';
		$edButtons = isset($attributes['edButtons']) ? $attributes['edButtons'] : true;

		// Use get_block_wrapper_attributes to get the class names and other attributes.
		$wrapper_attributes = get_block_wrapper_attributes([
			'class' => "affblk-cta-wrapper",
			'id' => "affiliatex-style-$block_id"
		]);

		$layoutClass = ($ctaLayout === 'layoutOne') ? ' layout-type-1' : (($ctaLayout === 'layoutTwo') ? ' layout-type-2' : '');
		$columnReverseClass = ($columnReverse && $ctaLayout !== 'layoutOne') ? ' col-reverse' : '';
		$bgClass = ($ctaBGType == 'image') ? ' img-opacity' : ' bg-color';

		$titleHtml = !empty($ctaTitle) ? sprintf('<h2 class="affliatex-cta-title">%s</h2>', wp_kses_post($ctaTitle)) : '';
		$contentHtml = !empty($ctaContent) ? sprintf('<p class="affliatex-cta-content">%s</p>', wp_kses_post($ctaContent)) : '';
		$imageWrapperHtml = ($ctaLayout === 'layoutTwo') ? '<div class="image-wrapper"></div>' : '';

		if (str_contains($content, $layoutClass)) {
			return $content;
		}

		$buttonWrapperHtml = $edButtons ? sprintf(
			'<div class="button-wrapper cta-btn-%s">%s</div>',
			esc_attr($ctaButtonAlignment),
			$content
		) : '';

		$output = sprintf(
			'<div %s>
            <div class="%s %s %s %s">
                <div class="content-wrapper">
                    <div class="content-wrap">
                        %s
                        %s
                    </div>
                    %s
                </div>
                %s
            </div>
        	</div>',
			$wrapper_attributes,
			esc_attr($layoutClass),
			esc_attr($ctaAlignment),
			esc_attr($columnReverseClass),
			esc_attr($bgClass),
			$titleHtml,
			$contentHtml,
			$buttonWrapperHtml,
			$imageWrapperHtml
		);

		return $output;
	}
}
