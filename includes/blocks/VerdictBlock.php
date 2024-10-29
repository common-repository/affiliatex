<?php
/**
 * AffiliateX Button Block
 *
 * @package AffiliateX
 */

namespace AffiliateX\Blocks;

defined( 'ABSPATH' ) || exit;

use AffiliateX\Helpers\AffiliateX_Helpers;

/**
 * Admin class
 *
 * @package AffiliateX
 */
class VerdictBlock {

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
		wp_enqueue_script('affiliatex-blocks-verdict', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/blocks/verdict/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/verdict', [
			'render_callback' => [$this, 'render'],
		]);
	}

	public function render($attributes, $content)
	{
		$attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);

		$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
		$verdictLayout = isset($attributes['verdictLayout']) ? $attributes['verdictLayout'] : '';
		$verdictTitle = isset($attributes['verdictTitle']) ? $attributes['verdictTitle'] : '';
		$verdictContent = isset($attributes['verdictContent']) ? $attributes['verdictContent'] : '';
		$edverdictTotalScore = isset($attributes['edverdictTotalScore']) ? $attributes['edverdictTotalScore'] : false;
		$verdictTotalScore = isset($attributes['verdictTotalScore']) ? $attributes['verdictTotalScore'] : '';
		$ratingContent = isset($attributes['ratingContent']) ? $attributes['ratingContent'] : '';
		$edRatingsArrow = isset($attributes['edRatingsArrow']) ? $attributes['edRatingsArrow'] : false;
		$edProsCons = isset($attributes['edProsCons']) ? $attributes['edProsCons'] : false;
		$verdictTitleTag = isset($attributes['verdictTitleTag']) ? AffiliateX_Helpers::validate_tag($attributes['verdictTitleTag'], 'h2') : 'h2';
		$ratingAlignment = isset($attributes['ratingAlignment']) ? $attributes['ratingAlignment'] : 'left';

		$wrapper_attributes = get_block_wrapper_attributes(array(
			'id' => "affiliatex-verdict-style-$block_id",
		));

		$TagTitle = $verdictTitleTag;

		$layoutClass = '';
		if ($verdictLayout === 'layoutOne') {
			$layoutClass = ' verdict-layout-1';
		} elseif ($verdictLayout === 'layoutTwo') {
			$layoutClass = ' verdict-layout-2';
		}

		if (str_contains($content, $layoutClass)) {
			return $content;
		}

		$ratingClass = $edverdictTotalScore ? ' number-rating' : '';
		$arrowClass = $edRatingsArrow ? ' display-arrow' : '';

		$titleHtml = sprintf(
			'<%1$s class="verdict-title">%2$s</%1$s>',
			esc_attr($TagTitle),
			wp_kses_post($verdictTitle)
		);

		$verdictContentHtml = sprintf(
			'<p class="verdict-content">%s</p>',
			wp_kses_post($verdictContent)
		);

		$verdictTotalScoreHtml = $edverdictTotalScore ? sprintf(
			'<div class="affx-verdict-rating-number%s %s">
				<span class="num">%s</span>
				<div class="rich-content">%s</div>
			</div>',
			esc_attr($ratingClass),
			esc_attr($ratingAlignment === 'right' ? 'align-right' : 'align-left'),
			wp_kses_post($verdictTotalScore),
			wp_kses_post($ratingContent)
		) : '';

		$innerBlocksContentHtml = $edProsCons ? $content : '';

		$layoutOneHtml = sprintf(
			'<div class="main-text-holder">
				<div class="content-wrapper">
					%s
					%s
				</div>
				%s
			</div>
			%s',
			$titleHtml,
			$verdictContentHtml,
			$verdictTotalScoreHtml,
			$innerBlocksContentHtml
		);

		$layoutTwoHtml = sprintf(
			'<div class="main-text-holder">
				<div class="content-wrapper">
					%s
					%s
				</div>
			</div>
			%s',
			$titleHtml,
			$verdictContentHtml,
			$innerBlocksContentHtml
		);

		$layoutHtml = '';
		if ($verdictLayout === 'layoutOne') {
			$layoutHtml = $layoutOneHtml;
		} elseif ($verdictLayout === 'layoutTwo') {
			$layoutHtml = $layoutTwoHtml;
		}

		return sprintf(
			'<div %s>
				<div class="affblk-verdict-wrapper">
					<div class="%s%s">
						%s
					</div>
				</div>
			</div>',
			$wrapper_attributes,
			esc_attr($layoutClass),
			esc_attr($arrowClass),
			$layoutHtml
		);
	}

}
