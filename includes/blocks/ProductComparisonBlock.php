<?php

/**
 * AffiliateX Button Block
 *
 * @package AffiliateX
 */

namespace AffiliateX\Blocks;
use AffiliateX\Helpers\AffiliateX_Helpers;

defined('ABSPATH') || exit;

/**
 * Admin class
 *
 * @package AffiliateX
 */
class ProductComparisonBlock
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
		wp_enqueue_script('affiliatex-blocks-product-comparison', plugin_dir_url(AFFILIATEX_PLUGIN_FILE) . 'build/blocks/product-comparison/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/product-comparison', [
			'render_callback' => [$this, 'render'],
		]);
	}

	private function render_pc_stars($rating, $starColor, $starInactiveColor)
	{
		$full_star = '<span style="color:' . esc_attr($starColor) . ';width:25px;height:25px;display:inline-flex"><svg fill="currentColor" width="25" height="25" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg></span>';
		$empty_star = '<span style="color:' . esc_attr($starInactiveColor) . ';width:25px;height:25px;display:inline-flex"><svg fill="currentColor" width="25" height="25" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg></span>';

		$stars = '';
		for ($i = 0; $i < 5; $i++) {
			if ($i < $rating) {
				$stars .= $full_star;
			} else {
				$stars .= $empty_star;
			}
		}
		return '<span class="rating-stars">' . $stars . '</span>';
	}

	public function render($attributes)
	{
		$attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);

		// Extract attributes
		$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
		$productComparisonTable = isset($attributes['productComparisonTable']) ? $attributes['productComparisonTable'] : [];
		$comparisonSpecs = isset($attributes['comparisonSpecs']) ? $attributes['comparisonSpecs'] : [];
		$pcRibbon = isset($attributes['pcRibbon']) ? $attributes['pcRibbon'] : false;
		$pcTitle = isset($attributes['pcTitle']) ? $attributes['pcTitle'] : false;
		$starColor = isset($attributes['starColor']) ? $attributes['starColor'] : '';
		$starInactiveColor = isset($attributes['starInactiveColor']) ? $attributes['starInactiveColor'] : '';
		$pcImage = isset($attributes['pcImage']) ? $attributes['pcImage'] : false;
		$pcRating = isset($attributes['pcRating']) ? $attributes['pcRating'] : false;
		$pcPrice = isset($attributes['pcPrice']) ? $attributes['pcPrice'] : false;
		$pcButton = isset($attributes['pcButton']) ? $attributes['pcButton'] : false;
		$pcButtonIcon = isset($attributes['pcButtonIcon']) ? $attributes['pcButtonIcon'] : false;
		$buttonIconAlign = isset($attributes['buttonIconAlign']) ? $attributes['buttonIconAlign'] : '';
		$buttonIcon = isset($attributes['buttonIcon']) ? $attributes['buttonIcon'] : [];
		$pcTitleTag = isset( $attributes['pcTitleTag'] ) ? AffiliateX_Helpers::validate_tag( $attributes['pcTitleTag'] ) : 'h2';
		$pcTitleAlign = isset($attributes['pcTitleAlign']) ? $attributes['pcTitleAlign'] : 'center';

		// Get block wrapper attributes
		$wrapper_attributes = get_block_wrapper_attributes([
			'id' => "affiliatex-product-comparison-blocks-style-$block_id"
		]);

		// Render the HTML
		$table_head = '';
		$table_body = '';

		foreach ($productComparisonTable as $item) {
			$imageUrl = esc_url($item['imageUrl']);
			$imageUrl = str_replace('app/src/images/fallback.jpg', 'src/images/fallback.jpg', $imageUrl);
			$imageAlt = esc_attr($item['imageAlt']);
			$ribbonText = $pcRibbon && !empty($item['ribbonText']) ? sprintf('<span class="affx-pc-ribbon">%s</span>', wp_kses_post($item['ribbonText'])) : '';
			$title = wp_kses_post($item['title']);
			$price = wp_kses_post($item['price']);
			$rating = wp_kses_post($item['rating']);
			$buttonURL = esc_url($item['buttonURL']);
			$buttonText = wp_kses_post($item['button']);
			$buttonIconHtml = $pcButtonIcon ? '<i class="button-icon ' . esc_attr($buttonIcon['value']) . '"></i>' : '';

			$table_head .= sprintf(
				'<th class="affx-product-col" style="width:%s%%;">
                %s
                <div class="affx-versus-product">
                    %s
                    <div class="affx-product-content">
                        %s
                        %s
                        <div class="affx-rating-wrap">%s</div>
                        %s
                    </div>
                </div>
            </th>',
				92 / (count($productComparisonTable) + 1),
				$ribbonText,
				$pcImage ? '<div class="affx-versus-product-img"><img src="' . $imageUrl . '" alt="' . $imageAlt . '"></div>' : '',
				$pcTitle ? sprintf(
					'<div class="affx-product-title-wrap"><%1$s class="affx-comparison-title" style="text-align: %2$s;">%3$s</%1$s></div>',
					esc_attr($pcTitleTag),
					esc_attr($pcTitleAlign),
					wp_kses_post($title)
				) : '',
				$pcPrice ? '<div class="affx-price-wrap"><span class="affx-price">' . $price . '</span></div>' : '',
				$pcRating ? $this->render_pc_stars($rating, $starColor, $starInactiveColor) : '',
				$pcButton ? sprintf(
					'<div class="affx-btn-wrap">
                            <a href="%s" class="affiliatex-button affx-winner-button %s">
                                %s%s%s
                            </a>
                        </div>',
					$buttonURL,
					$pcButtonIcon ? 'icon-btn icon-' . esc_attr($buttonIconAlign) : '',
					$buttonIconAlign === 'left' ? $buttonIconHtml : '',
					$buttonText,
					$buttonIconAlign === 'right' ? $buttonIconHtml : ''
				) : '',
			);
		}

		foreach ($comparisonSpecs as $index => $item) {
			$specs = '';
			foreach ($productComparisonTable as $countIndex => $count) {
				if ($countIndex === 0) {
					$specs .= sprintf(
						'<td class="data-label">%s</td><td>%s</td>',
						wp_kses_post($item['title']),
						wp_kses_post($item['specs'][$countIndex] ?? '')
					);
				} else {
					if(isset($item['specs'][$countIndex])) {
						$specs .= sprintf('<td>%s</td>', wp_kses_post($item['specs'][$countIndex]));
					}
				}
			}
			$table_body .= sprintf('<tr>%s</tr>', $specs);
		}

		return sprintf(
			'<div %s><div class="affx-product-comparison-block-container affx-versus-block-container"><div class="affx-versus-table-wrap"><table class="affx-product-versus-table layout-1"><thead><tr><th class="data-label" style="width:%s%%;"></th>%s</tr></thead><tbody>%s</tbody></table></div></div></div>',
			$wrapper_attributes,
			92 / (count($productComparisonTable) + 1),
			$table_head,
			$table_body
		);
	}
}
