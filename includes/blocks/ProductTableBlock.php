<?php

/**
 * AffiliateX Button Block
 *
 * @package AffiliateX
 */

namespace AffiliateX\Blocks;

defined('ABSPATH') || exit;

/**
 * Admin class
 *
 * @package AffiliateX
 */
class ProductTableBlock
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		add_action('enqueue_block_editor_assets', [$this, 'enqueue_block']);
		add_action('init', [$this, 'register_block']);
	}

	public function enqueue_block()
	{
		wp_enqueue_script('affiliatex-blocks-product-table', plugin_dir_url(AFFILIATEX_PLUGIN_FILE) . 'build/blocks/product-table/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/product-table', [
			'render_callback' => [$this, 'render'],
		]);
	}

	private function render_pt_stars($rating, $starColor, $starInactiveColor)
	{
		$output = '';
		for ($i = 0; $i < 5; $i++) {
			$color = $i < $rating ? $starColor : $starInactiveColor;
			$output .= sprintf(
				'<span style="color:%s;width:25px;height:25px;display:inline-flex;"><svg fill="currentColor" width="25" height="25" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg></span>',
				esc_attr($color)
			);
		}
		return $output;
	}

	private function render_features_list($features)
	{
		if (!is_array($features)) {
			return wp_kses_post($features);
		}

		$output = '';
		foreach ($features as $item) {
			if (is_array($item)) {
				$output .= '<li>' . wp_kses_post(affx_extract_child_items($item)) . '</li>';
			} elseif (is_string($item)) {
				$output .= '<li>' . wp_kses_post($item) . '</li>';
			}
		}
		return $output;
	}

	public function render($attributes)
	{
		$attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);

		// Extract attributes
		$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
		$productTable = isset($attributes['productTable']) ? $attributes['productTable'] : [];
		$layoutStyle = isset($attributes['layoutStyle']) ? $attributes['layoutStyle'] : 'layoutOne';
		$imageColTitle = isset($attributes['imageColTitle']) ? $attributes['imageColTitle'] : '';
		$productColTitle = isset($attributes['productColTitle']) ? $attributes['productColTitle'] : '';
		$featuresColTitle = isset($attributes['featuresColTitle']) ? $attributes['featuresColTitle'] : '';
		$ratingColTitle = isset($attributes['ratingColTitle']) ? $attributes['ratingColTitle'] : '';
		$priceColTitle = isset($attributes['priceColTitle']) ? $attributes['priceColTitle'] : '';
		$edImage = isset($attributes['edImage']) ? $attributes['edImage'] : false;
		$edCounter = isset($attributes['edCounter']) ? $attributes['edCounter'] : false;
		$edProductName = isset($attributes['edProductName']) ? $attributes['edProductName'] : false;
		$edRating = isset($attributes['edRating']) ? $attributes['edRating'] : false;
		$edRibbon = isset($attributes['edRibbon']) ? $attributes['edRibbon'] : false;
		$edPrice = isset($attributes['edPrice']) ? $attributes['edPrice'] : false;
		$edButton1 = isset($attributes['edButton1']) ? $attributes['edButton1'] : false;
		$edButton1Icon = isset($attributes['edButton1Icon']) ? $attributes['edButton1Icon'] : false;
		$button1Icon = isset($attributes['button1Icon']) ? $attributes['button1Icon'] : [];
		$button1IconAlign = isset($attributes['button1IconAlign']) ? $attributes['button1IconAlign'] : 'left';
		$edButton2 = isset($attributes['edButton2']) ? $attributes['edButton2'] : false;
		$edButton2Icon = isset($attributes['edButton2Icon']) ? $attributes['edButton2Icon'] : false;
		$button2Icon = isset($attributes['button2Icon']) ? $attributes['button2Icon'] : [];
		$button2IconAlign = isset($attributes['button2IconAlign']) ? $attributes['button2IconAlign'] : 'left';
		$starColor = isset($attributes['starColor']) ? $attributes['starColor'] : '';
		$starInactiveColor = isset($attributes['starInactiveColor']) ? $attributes['starInactiveColor'] : '';
		$productContentType = isset($attributes['productContentType']) ? $attributes['productContentType'] : '';
		$contentListType = isset($attributes['contentListType']) ? $attributes['contentListType'] : '';
		$productIconList = isset($attributes['productIconList']) ? $attributes['productIconList'] : [];

		$wrapper_attributes = get_block_wrapper_attributes(array(
			'id' => "affiliatex-pdt-table-style-$block_id"
		));

		// Assemble the table head
		$table_head = '';
		if ($layoutStyle === 'layoutOne' || $layoutStyle === 'layoutTwo') {
			$table_head = sprintf(
				'<tr>%s%s%s%s%s%s</tr>',
				$edImage ? '<td class="affx-img-col"><span>' . wp_kses_post($imageColTitle) . '</span></td>' : '',
				'<td><span>' . wp_kses_post($productColTitle) . '</span></td>',
				$layoutStyle === 'layoutOne' ? '<td><span>' . wp_kses_post($featuresColTitle) . '</span></td>' : '',
				$layoutStyle === 'layoutOne' ? '<td class="affx-price-col"><span>' . wp_kses_post($priceColTitle) . '</span></td>' : '',
				$layoutStyle === 'layoutTwo' &&  $edRating ? '<td><span>' . wp_kses_post($ratingColTitle) . '</span></td>' : '',
				$layoutStyle === 'layoutTwo' ? '<td class="affx-price-col"><span>' . wp_kses_post($priceColTitle) . '</span></td>' : ''
			);
		}

		// Assemble the table body
		$table_body = '';
		foreach ($productTable as $index => $product) {
			$counterText = $edCounter ? ($index + 1) : '';
			$ribbonText = $product['ribbon'] ?? '';
			$imageUrl = esc_url($product['imageUrl']);
			$imageAlt = esc_attr($product['imageAlt']);
			$title = $edProductName ? '<h5 class="affx-pdt-name">' . wp_kses_post($product['name']) . '</h5>' : '';
			$featuresContent = $productContentType === 'list' ?
				sprintf('<%1$s class="affx-unordered-list affiliatex-icon affiliatex-icon-%2$s">%3$s</%1$s>',
					$contentListType == 'unordered' ? 'ul' : 'ol',
					esc_attr($productIconList['name']),
					$this->render_features_list($product['featuresList'])
				) :
			sprintf('<p class="affiliatex-content">%s</p>', wp_kses_post($product['features']));
			$priceHtml = $edPrice ?
				sprintf(
					'<div class="affx-pdt-price-wrap">%s%s</div>',
					!empty($product['offerPrice']) ? '<span class="affx-pdt-offer-price">' . wp_kses_post($product['offerPrice']) . '</span>' : '',
					!empty($product['regularPrice']) ? '<del class="affx-pdt-reg-price">' . wp_kses_post($product['regularPrice']) . '</del>' : ''
				) : '';

			$button1Html = $edButton1 && !empty($product['button1']) ?
				sprintf(
					'<div class="affx-btn-inner"><a href="%s" class="affiliatex-button primary %s">%s%s%s</a></div>',
					esc_url($product['button1URL']),
					$edButton1Icon ? 'icon-btn icon-' . esc_attr($button1IconAlign) : '',
					$edButton1Icon && $button1IconAlign === 'left' ? '<i class="button-icon ' . esc_attr($button1Icon['value']) . '"></i>' : '',
					wp_kses_post($product['button1']),
					$edButton1Icon && $button1IconAlign === 'right' ? '<i class="button-icon ' . esc_attr($button1Icon['value']) . '"></i>' : ''
				) : '';

			$button2Html = $edButton2 && !empty($product['button2']) ?
				sprintf(
					'<div class="affx-btn-inner"><a href="%s" class="affiliatex-button secondary %s">%s%s%s</a></div>',
					esc_url($product['button2URL']),
					$edButton2Icon ? 'icon-btn icon-' . esc_attr($button2IconAlign) : '',
					$edButton2Icon && $button2IconAlign === 'left' ? '<i class="button-icon ' . esc_attr($button2Icon['value']) . '"></i>' : '',
					wp_kses_post($product['button2']),
					$edButton2Icon && $button2IconAlign === 'right' ? '<i class="button-icon ' . esc_attr($button2Icon['value']) . '"></i>' : ''
				) : '';

			$ratingOutput = '';
			if ($edRating && !empty($product['rating'])) {
				if ($layoutStyle === 'layoutOne') {
					$ratingOutput = sprintf(
						'<span class="star-rating-single-wrap">%s</span>',
						wp_kses_post($product['rating'])
					);
				} elseif ($layoutStyle === 'layoutTwo') {
					$ratingOutput = sprintf(
						'<div class="affx-circle-progress-container">
							<span class="circle-wrap" style="--data-deg:rotate(%sdeg);">
								<span class="circle-mask full"><span class="fill"></span></span>
								<span class="circle-mask"><span class="fill"></span></span>
							</span>
							<span class="affx-circle-inside">%s</span>
						</div>',
						esc_attr(180 * ($product['rating'] / 10)),
						wp_kses_post($product['rating'])
					);
				} elseif ($layoutStyle === 'layoutThree') {
					$ratingOutput = $this->render_pt_stars($product['rating'], $starColor, $starInactiveColor);
				}
			}
			if ($layoutStyle === 'layoutOne') {
				$table_body .= sprintf(
					'<tr>
						%s
						<td>%s</td>
						<td>%s</td>
						<td class="affx-price-col">
							%s
							<div class="affx-btn-wrapper">%s%s</div>
						</td>
					</tr>',
			$edImage ? sprintf(
			'<td class="affx-img-col">
						<div class="affx-pdt-img-container">
							%s
							%s
							<div class="affx-pdt-img-wrapper">
								<img src="%s" alt="%s">
							</div>
							%s
						</div>
					</td>',
					(!empty($ribbonText) && $edRibbon)  ? sprintf('<span class="affx-pdt-ribbon affx-ribbon-2">%s</span>', $ribbonText) : '',
							$edCounter ? sprintf('<span class="affx-pdt-counter">%s</span>', $counterText) : '',
							$imageUrl,
							$imageAlt,
							$ratingOutput,
					) : '',
					$title,
					$featuresContent,
					$priceHtml,
					$button1Html,
					$button2Html
				);
			} elseif ($layoutStyle === 'layoutTwo') {
				$table_body .= sprintf(
					'<tr>
						%s
						<td>%s</td>
						%s
						<td class="affx-price-col">
							%s
							<div class="affx-btn-wrapper">%s%s</div>
						</td>
					</tr>',
					$edImage ? sprintf(
						'<td class="affx-img-col">
							<div class="affx-pdt-img-container">
								%s
								%s
								<div class="affx-pdt-img-wrapper">
									<img src="%s" alt="%s">
								</div>
							</div>
						</td>',
						(!empty($ribbonText) && $edRibbon) ? sprintf('<span class="affx-pdt-ribbon affx-ribbon-2">%s</span>', $ribbonText) : '',
						$edCounter ? sprintf('<span class="affx-pdt-counter">%s</span>', $counterText) : '',
						$imageUrl,
						$imageAlt,
					) : '',
					$title . $featuresContent,
					$edRating ? sprintf('<td class="affx-rating-col">%s</td>', $ratingOutput) : '',
					$priceHtml,
					$button1Html,
					$button2Html
				);
			} else if ($layoutStyle === 'layoutThree') {
				$table_body .= sprintf(
					'<div class="affx-pdt-table-single">
						%s
						<div class="affx-pdt-content-wrap">
							<div class="affx-content-left">
								%s
								%s
								<h5 class="affx-pdt-name">%s</h5>
								<div class="affx-rating-wrap">%s</div>
								%s
								<div class="affx-pdt-desc">%s</div>
							</div>
							<div class="affx-pdt-button-wrap">
								<div class="affx-btn-wrapper">%s%s</div>
							</div>
						</div>
					</div>',
					$edImage ? sprintf(
						'<div class="affx-pdt-img-wrapper">
							<img src="%s" alt="%s">
						</div>',
						$imageUrl,
						$imageAlt,
					) : '',
					$edCounter ? sprintf('<span class="affx-pdt-counter">%s</span>', $counterText) : '',
					(!empty($ribbonText) && $edRibbon) ? sprintf('<span class="affx-pdt-ribbon">%s</span>', $ribbonText) : '',
					$title,
					$ratingOutput,
					$priceHtml,
					$featuresContent,
					$button1Html,
					$button2Html
				);
			}
		}

		// Assemble the final HTML output
		$output = sprintf(
			'<div %s>
				<div class="affx-pdt-table-container--free affx-block-admin %s">
					<div class="affx-pdt-table-wrapper">
						%s
						%s
					</div>
				</div>
			</div>',
			$wrapper_attributes,
			$layoutStyle === 'layoutThree' ? 'layout-3' : '',
			$layoutStyle === 'layoutThree' ? $table_body : '<table class="affx-pdt-table"><thead>' . $table_head . '</thead><tbody>' . $table_body . '</tbody></table>',
			$layoutStyle === 'layoutThree' ? '' : ''
		);

		return $output;
	}
}
