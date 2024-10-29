<?php

namespace AffiliateX;

use AffiliateX\Blocks\ButtonBlock;
use AffiliateX\Blocks\CtaBlock;
use AffiliateX\Blocks\NoticeBlock;
use AffiliateX\Blocks\ProductComparisonBlock;
use AffiliateX\Blocks\ProductTableBlock;
use AffiliateX\Blocks\ProsAndConsBlock;
use AffiliateX\Blocks\SingleProductBlock;
use AffiliateX\Blocks\SpecificationsBlock;
use AffiliateX\Blocks\VerdictBlock;
use AffiliateX\Blocks\VersusLineBlock;

defined('ABSPATH') || exit;

/**
 * Admin class
 *
 * @package AffiliateX
 */
class AffiliateXBlocks
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
		$blocks = [
			'ButtonBlock' => ButtonBlock::class,
			'CtaBlock' => CtaBlock::class,
			'NoticeBlock' => NoticeBlock::class,
			'ProductComparisonBlock' => ProductComparisonBlock::class,
			'ProductTableBlock' => ProductTableBlock::class,
			'ProsAndConsBlock' => ProsAndConsBlock::class,
			'SingleProductBlock' => SingleProductBlock::class,
			'SpecificationsBlock' => SpecificationsBlock::class,
			'VerdictBlock' => VerdictBlock::class,
			'VersusLineBlock' => VersusLineBlock::class,
		];


		foreach ($blocks as $class) {
			new $class();
		}
	}
}
