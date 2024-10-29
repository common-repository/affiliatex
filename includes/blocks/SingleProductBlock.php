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
class SingleProductBlock {

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
		wp_enqueue_script('affiliatex-blocks-single-product', plugin_dir_url( AFFILIATEX_PLUGIN_FILE ) . 'build/blocks/single-product/index.js', array('affiliatex'), AFFILIATEX_VERSION, true);
	}

	public function register_block()
	{
		register_block_type_from_metadata(AFFILIATEX_PLUGIN_DIR . '/build/blocks/single-product', [
			'render_callback' => [$this, 'render'],
		]);
	}

	private function render_list_items($listItems) {
		$itemsHtml = '';
		foreach ($listItems as $item) {
			$itemsHtml .= sprintf(
				'<li>%s</li>',
				wp_kses_post(affx_extract_child_items($item))
			);
		}
		return $itemsHtml;
	}

    private function render_pb_stars($ratings, $productRatingColor, $ratingInactiveColor, $ratingStarSize) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $color = ($i <= $ratings) ? $productRatingColor : $ratingInactiveColor;
            $stars .= sprintf(
                '<span style="color:%s;width:%dpx;height:%dpx;display:inline-flex;">
                    <svg fill="currentColor" width="%d" height="%d" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                    </svg>
                </span>',
                esc_attr($color),
                esc_attr($ratingStarSize),
                esc_attr($ratingStarSize),
                esc_attr($ratingStarSize),
                esc_attr($ratingStarSize)
            );
        }
        return $stars;
    }

	public function render($attributes, $content)
{
    $attributes = AffiliateX_Customization_Helper::apply_customizations($attributes);
    $block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
    $productLayout = isset($attributes['productLayout']) ? $attributes['productLayout'] : '';
    $productTitle = isset($attributes['productTitle']) ? $attributes['productTitle'] : '';
	$productTitleTag = isset($attributes['productTitleTag']) ? AffiliateX_Helpers::validate_tag($attributes['productTitleTag'], 'h2') : 'h2';
    $productContent = isset($attributes['productContent']) ? $attributes['productContent'] : '';
    $productSubTitle = isset($attributes['productSubTitle']) ? $attributes['productSubTitle'] : '';
	$productSubTitleTag = isset($attributes['productSubTitleTag']) ? AffiliateX_Helpers::validate_tag($attributes['productSubTitleTag'], 'h3') : 'h3';
    $productContentType = isset($attributes['productContentType']) ? $attributes['productContentType'] : '';
    $ContentListType = isset($attributes['ContentListType']) ? $attributes['ContentListType'] : '';
    $productContentList = isset($attributes['productContentList']) ? $attributes['productContentList'] : [];
    $productImageAlign = isset($attributes['productImageAlign']) ? $attributes['productImageAlign'] : '';
    $productSalePrice = isset($attributes['productSalePrice']) ? $attributes['productSalePrice'] : '';
    $productPrice = isset($attributes['productPrice']) ? $attributes['productPrice'] : '';
    $productIconList = isset($attributes['productIconList']) ? $attributes['productIconList'] : [];
    $ratings = isset($attributes['ratings']) ? $attributes['ratings'] : '';
    $edRatings = isset($attributes['edRatings']) ? $attributes['edRatings'] : false;
    $edTitle = isset($attributes['edTitle']) ? $attributes['edTitle'] : false;
    $edSubtitle = isset($attributes['edSubtitle']) ? $attributes['edSubtitle'] : false;
    $edContent = isset($attributes['edContent']) ? $attributes['edContent'] : false;
    $edPricing = isset($attributes['edPricing']) ? $attributes['edPricing'] : false;
    $PricingType = isset($attributes['PricingType']) ? $attributes['PricingType'] : '';
    $productRatingColor = isset($attributes['productRatingColor']) ? $attributes['productRatingColor'] : '';
    $ratingInactiveColor = isset($attributes['ratingInactiveColor']) ? $attributes['ratingInactiveColor'] : '';
    $ratingContent = isset($attributes['ratingContent']) ? $attributes['ratingContent'] : '';
    $ratingStarSize = isset($attributes['ratingStarSize']) ? $attributes['ratingStarSize'] : '';
    $edButton = isset($attributes['edButton']) ? $attributes['edButton'] : false;
    $edProductImage = isset($attributes['edProductImage']) ? $attributes['edProductImage'] : false;
    $edRibbon = isset($attributes['edRibbon']) ? $attributes['edRibbon'] : false;
    $productRibbonLayout = isset($attributes['productRibbonLayout']) ? $attributes['productRibbonLayout'] : '';
    $ribbonText = isset($attributes['ribbonText']) ? $attributes['ribbonText'] : '';
    $ImgUrl = isset($attributes['ImgUrl']) ? $attributes['ImgUrl'] : '';
    $numberRatings = isset($attributes['numberRatings']) ? $attributes['numberRatings'] : '';
    $productRatingAlign = isset($attributes['productRatingAlign']) ? $attributes['productRatingAlign'] : '';
    $productStarRatingAlign = isset($attributes['productStarRatingAlign']) ? $attributes['productStarRatingAlign'] : '';
    $productImageType = isset($attributes['productImageType']) ? $attributes['productImageType'] : '';
    $productImageExternal = isset($attributes['productImageExternal']) ? $attributes['productImageExternal'] : '';
    $productImageSiteStripe = isset($attributes['productImageSiteStripe']) ? $attributes['productImageSiteStripe'] : '';
    $productPricingAlign = isset($attributes['productPricingAlign']) ? $attributes['productPricingAlign'] : '';

    $wrapper_attributes = get_block_wrapper_attributes(array(
        'id' => "affiliatex-single-product-style-$block_id",
    ));

    $TagTitle = $productTitleTag;
    $TagSubtitle = $productSubTitleTag;

    $layoutClass = '';
    if ($productLayout === 'layoutOne') {
        $layoutClass = ' product-layout-1';
    } elseif ($productLayout === 'layoutTwo') {
        $layoutClass = ' product-layout-2';
    } elseif ($productLayout === 'layoutThree') {
        $layoutClass = ' product-layout-3';
    }
	if (str_contains($content, $layoutClass)) {
		return str_replace('app/src/images/fallback', 'src/images/fallback', $content);
	}

    $ratingClass = '';
    if ($PricingType === 'picture') {
        $ratingClass = 'star-rating';
    } elseif ($PricingType === 'number') {
        $ratingClass = 'number-rating';
    }

    $imageAlign = $edProductImage ? 'image-' . $productImageAlign : '';
    $ribbonLayout = '';
    if ($productRibbonLayout === 'one') {
        $ribbonLayout = ' ribbon-layout-1';
    } elseif ($productRibbonLayout === 'two') {
        $ribbonLayout = ' ribbon-layout-2';
    }

    $imageClass = !$edProductImage ? 'no-image' : '';
    $productRatingNumberClass = $PricingType === 'number' ? 'rating-align-' . $productRatingAlign : '';
    $ImageURL = $productImageType === 'default' ? $ImgUrl : $productImageExternal;
    $isSiteStripe = 'sitestripe' === $productImageType && '' !== $productImageSiteStripe ? true : false;

	$ribbonHtml = $edRibbon ? sprintf(
		'<div class="affx-sp-ribbon%s"><div class="affx-sp-ribbon-title">%s</div></div>',
		esc_attr($ribbonLayout),
		wp_kses_post($ribbonText)
	) : '';

    $imageHtml = $edProductImage ? sprintf(
        '<div class="affx-sp-img-wrapper">%s</div>',
        $isSiteStripe ? wp_kses_post($productImageSiteStripe) : sprintf('<img src="%s" />', esc_url($ImageURL))
    ) : '';

    $titleHtml = $edTitle ? sprintf(
        '<%1$s class="affx-single-product-title">%2$s</%1$s>',
        esc_attr($TagTitle),
        wp_kses_post($productTitle)
    ) : '';

    $subtitleHtml = $edSubtitle ? sprintf(
        '<%1$s class="affx-single-product-subtitle">%2$s</%1$s>',
        esc_attr($TagSubtitle),
        wp_kses_post($productSubTitle)
    ) : '';

    $ratingHtml = '';
    if ($edRatings && $PricingType === 'picture') {
        $ratingHtml = sprintf(
            '<div class="affx-sp-pricing-pic rating-align-%s">%s</div>',
            esc_attr($productStarRatingAlign),
            $this->render_pb_stars($ratings, $productRatingColor, $ratingInactiveColor, $ratingStarSize)
        );
    } elseif ($edRatings && $PricingType === 'number') {
        $ratingHtml = sprintf(
            '<div class="affx-rating-box affx-rating-number"><span class="num">%s</span><span class="label">%s</span></div>',
            wp_kses_post($numberRatings),
            wp_kses_post($ratingContent)
        );
    }

    $pricingHtml = $edPricing ? sprintf(
        '<div class="affx-sp-price pricing-align-%s"><div class="affx-sp-marked-price">%s</div><div class="affx-sp-sale-price"><del>%s</del></div></div>',
        esc_attr($productPricingAlign),
        wp_kses_post($productSalePrice),
        wp_kses_post($productPrice)
    ) : '';

	$contentHtml = '';
	if ($edContent) {
		if ($productContentType === 'list') {
			$contentHtml = sprintf(
				'<%1$s class="affx-unordered-list affiliatex-icon affiliatex-icon-%2$s">%3$s</%1$s>',
				$ContentListType === 'unordered' ? 'ul' : 'ol',
				esc_attr($productIconList['name']),
				$this->render_list_items($productContentList)
			);
		} elseif ($productContentType === 'paragraph') {
			$contentHtml = sprintf(
				'<p class="affiliatex-content">%s</p>',
				wp_kses_post($productContent)
			);
		}

		$contentHtml = sprintf(
			'<div class="affx-single-product-content">%s</div>',
			$contentHtml
		);
	}


    $buttonHtml = $edButton ? '<div class="button-wrapper">' . $content . '</div>' : '';

    // Separate HTML for different layouts
    $layoutOneHtml = sprintf(
		'<div class="affx-sp-content %s %s">
			%s
			%s
			<div class="affx-sp-content-wrapper">
				<div class="title-wrapper affx-%s %s">
					<div class="affx-title-left">
						%s
						%s
					</div>
					%s
				</div>
				%s
				<div class="affx-single-product-content">
					%s
				</div>
				%s
			</div>
		</div>',
		esc_attr($imageAlign),
		esc_attr($imageClass),
		$ribbonHtml,
		$imageHtml,
		esc_attr($ratingClass),
		esc_attr($productRatingNumberClass),
		$titleHtml,
		$subtitleHtml,
		$ratingHtml,
		$pricingHtml,
		$contentHtml,
		$buttonHtml
	);

	$layoutTwoHtml = sprintf(
		'<div class="affx-sp-content %s %s">
			<div class="title-wrapper affx-%s %s">
				<div class="affx-title-left">
					%s
					%s
					%s
				</div>
				%s
			</div>
			%s
			%s
			%s
			%s
		</div>',
		esc_attr($imageAlign),
		esc_attr($imageClass),
		esc_attr($ratingClass),
		esc_attr($productRatingNumberClass),
		$ribbonHtml,
		$titleHtml,
		$subtitleHtml,
		$ratingHtml,
		$imageHtml,
		$pricingHtml,
		$contentHtml,
		$buttonHtml
	);

	$layoutThreeHtml = sprintf(
		'<div class="affx-sp-content %s %s">
			%s
			%s
			<div class="affx-sp-content-wrapper">
				<div class="title-wrapper affx-%s %s">
					<div class="affx-title-left">
						%s
						%s
					</div>
					%s
				</div>
				%s
				<div class="affx-single-product-content">
					%s
				</div>
			</div>
			%s
		</div>',
		esc_attr($imageAlign),
		esc_attr($imageClass),
		$ribbonHtml,
		$imageHtml,
		esc_attr($ratingClass),
		esc_attr($productRatingNumberClass),
		$titleHtml,
		$subtitleHtml,
		$ratingHtml,
		$pricingHtml,
		$contentHtml,
		$buttonHtml
	);

	$layoutHtml = '';
	if ($productLayout === 'layoutOne') {
		$layoutHtml = $layoutOneHtml;
	} elseif ($productLayout === 'layoutTwo') {
		$layoutHtml = $layoutTwoHtml;
	} elseif ($productLayout === 'layoutThree') {
		$layoutHtml = $layoutThreeHtml;
	}

	return sprintf(
		'<div %s><div class="affx-single-product-wrapper%s"><div class="affx-sp-inner">%s</div></div></div>',
		$wrapper_attributes,
		esc_attr($layoutClass),
		$layoutHtml
	);
}

}
