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
class ButtonBlock {

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
		wp_enqueue_script('affiliatex-blocks-button', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/blocks/buttons/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/buttons', [
			'render_callback' => [$this, 'render'],
		]);
	}

	public function render($attributes) {
		$attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);

		// Extract attributes
		$buttonLabel = isset($attributes['buttonLabel']) ? $attributes['buttonLabel'] : 'Button';
		$buttonSize = isset($attributes['buttonSize']) ? $attributes['buttonSize'] : 'medium';
		$buttonWidth = isset($attributes['buttonWidth']) ? $attributes['buttonWidth'] : 'flexible';
		$buttonURL = isset($attributes['buttonURL']) ? $attributes['buttonURL'] : '';
		$iconPosition = isset($attributes['iconPosition']) ? $attributes['iconPosition'] : 'left';
		$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
		$ButtonIcon = isset($attributes['ButtonIcon']['value']) ? $attributes['ButtonIcon']['value'] : '';
		$edButtonIcon = isset($attributes['edButtonIcon']) ? $attributes['edButtonIcon'] : false;
		$btnRelSponsored = isset($attributes['btnRelSponsored']) ? $attributes['btnRelSponsored'] : false;
		$openInNewTab = isset($attributes['openInNewTab']) ? $attributes['openInNewTab'] : false;
		$btnRelNoFollow = isset($attributes['btnRelNoFollow']) ? $attributes['btnRelNoFollow'] : false;
		$buttonAlignment = isset($attributes['buttonAlignment']) ? $attributes['buttonAlignment'] : 'center';
		$btnDownload = isset($attributes['btnDownload']) ? $attributes['btnDownload'] : false;
		$layoutStyle = isset($attributes['layoutStyle']) ? $attributes['layoutStyle'] : 'layout-type-1';
		$priceTagPosition = isset($attributes['priceTagPosition']) ? $attributes['priceTagPosition'] : '';
		$productPrice = isset($attributes['productPrice']) ? $attributes['productPrice'] : '';

		$wrapper_attributes = get_block_wrapper_attributes(array(
			'class' => 'affx-btn-wrapper',
			'id' => "affiliatex-blocks-style-$block_id"
		));

		// Construct class names
		$classNames = [
			'affiliatex-button',
			'btn-align-' . $buttonAlignment,
			'btn-is-' . $buttonSize,
			$buttonWidth === 'fixed' ? 'btn-is-fixed' : '',
			$buttonWidth === 'full' ? 'btn-is-fullw' : '',
			$buttonWidth === 'flexible' ? 'btn-is-flex-' . $buttonSize : '',
			$layoutStyle === 'layout-type-2' && $priceTagPosition === 'tagBtnleft' ? 'left-price-tag' : '',
			$layoutStyle === 'layout-type-2' && $priceTagPosition === 'tagBtnright' ? 'right-price-tag' : '',
			$edButtonIcon && $iconPosition === 'axBtnright' ? 'icon-right' : 'icon-left'
		];
		$classNames = implode(' ', array_filter($classNames));

		// Construct rel attribute
		$rel = ['noopener'];
		if ($btnRelNoFollow) $rel[] = 'nofollow';
		if ($btnRelSponsored) $rel[] = 'sponsored';
		$rel = implode(' ', $rel);

		// Construct target attribute
		$target = $openInNewTab ? ' target="_blank"' : '';

		// Construct download attribute
		$download = $btnDownload ? ' download="affiliatex"' : '';

		// Construct icon HTML
		$iconLeft = $edButtonIcon && $iconPosition === 'axBtnleft' ? '<i class="button-icon ' . esc_attr($ButtonIcon) . '"></i>' : '';
		$iconRight = $edButtonIcon && $iconPosition === 'axBtnright' ? '<i class="button-icon ' . esc_attr($ButtonIcon) . '"></i>' : '';

		// Construct button HTML
		$buttonHTML = sprintf(
			'<a href="%s" class="%s" rel="%s"%s%s>%s<span class="affiliatex-btn">%s</span>%s%s</a>',
			esc_url($buttonURL),
			esc_attr($classNames),
			esc_attr($rel),
			$target,
			$download,
			$iconLeft,
			wp_kses_post($buttonLabel),
			$iconRight,
			$layoutStyle === 'layout-type-2' && $priceTagPosition ? sprintf(
				'<span class="price-tag">%s</span>',
				wp_kses_post($productPrice)
			) : ''
		);

		// Return the full HTML
		return sprintf(
			'<div %s><div class="affx-btn-inner">%s</div></div>',
			$wrapper_attributes,
			$buttonHTML
		);
	}
}
