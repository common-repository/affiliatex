<?php
/**
 * AffiliateX Button Block
 *
 * @package AffiliateX
 */

namespace AffiliateX\Blocks;

use AffiliateX\Helpers\AffiliateX_Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class
 *
 * @package AffiliateX
 */
class NoticeBlock {

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
		wp_enqueue_script('affiliatex-blocks-notice', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/blocks/notice/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/notice', [
			'render_callback' => [$this, 'render'],
		]);
	}

	function render($attributes) {
		$attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);

		$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
		$titleTag1 = isset($attributes['titleTag1']) ? AffiliateX_Helpers::validate_tag($attributes['titleTag1']) : 'h2';
		$layoutStyle = isset($attributes['layoutStyle']) ? $attributes['layoutStyle'] : 'layout-type-1';
		$noticeTitle = isset($attributes['noticeTitle']) ? $attributes['noticeTitle'] : 'Notice';
		$noticeTitleIcon = isset($attributes['noticeTitleIcon']['name']) ? $attributes['noticeTitleIcon']['name'] : '';
		$noticeListItems = isset($attributes['noticeListItems']) ? $attributes['noticeListItems'] : [];
		$noticeListType = isset($attributes['noticeListType']) ? $attributes['noticeListType'] : 'unordered';
		$noticeContent = isset($attributes['noticeContent']) ? $attributes['noticeContent'] : '';
		$noticeContentType = isset($attributes['noticeContentType']) ? $attributes['noticeContentType'] : 'list';
		$noticeListIcon = isset($attributes['noticeListIcon']['name']) ? $attributes['noticeListIcon']['name'] : '';
		$noticeunorderedType = isset($attributes['noticeunorderedType']) ? $attributes['noticeunorderedType'] : 'icon';
		$edTitleIcon = isset($attributes['edTitleIcon']) ? $attributes['edTitleIcon'] : false;
		$titleAlignment = isset($attributes['titleAlignment']) ? $attributes['titleAlignment'] : 'left';

		$wrapper_attributes = get_block_wrapper_attributes(array(
			'class' => 'affx-notice-wrapper',
			'id' => "affiliatex-notice-style-$block_id"
		));

		$titleIconClass = $edTitleIcon ? "affiliatex-icon-{$noticeTitleIcon}" : '';
		$Tag1 = wp_kses_post($titleTag1);

		$listItemsHtml = '';
		if ($noticeContentType === 'list') {
			$listTag = $noticeListType === 'unordered' ? 'ul' : 'ol';
			$listClass = $noticeunorderedType === 'icon' ? "affiliatex-list icon affiliatex-icon affiliatex-icon-{$noticeListIcon}" : 'affiliatex-list bullet';

			$listItemsHtml = "<{$listTag} class='{$listClass}'>";
			foreach ($noticeListItems as $item) {
				if (isset($item['props']) && is_array($item)) {
					$listItemsHtml .= sprintf('<li>%s</li>', wp_kses_post(affx_extract_child_items($item)));
				} else {
					$listItemsHtml .= '';
				}
			}
			$listItemsHtml .= "</{$listTag}>";
		} elseif ($noticeContentType == 'paragraph') {
			$listItemsHtml = sprintf(
				'<p class="affiliatex-content">%s</p>',
				wp_kses_post($noticeContent)
			);
		}

		return sprintf(
			'<div %s>
				<div class="affx-notice-inner-wrapper %s">
					%s
					<div class="affx-notice-inner">
						<%s class="affiliatex-notice-title affiliatex-icon afx-icon-before %s" style="text-align: %s;">
            			%s
        				</%s>
						<div class="affiliatex-notice-content">
							<div class="list-wrapper">
								%s
							</div>
						</div>
					</div>
				</div>
			</div>',
			$wrapper_attributes,
			esc_attr($layoutStyle),
			$layoutStyle === 'layout-type-3' ? sprintf('<span class="affiliatex-notice-icon affiliatex-icon afx-icon-before %s"></span>', esc_attr($titleIconClass)) : '',
			$Tag1,
			esc_attr($titleIconClass),
			esc_attr($titleAlignment),
			wp_kses_post($noticeTitle),
			$Tag1,
			$listItemsHtml
		);
	}
}
