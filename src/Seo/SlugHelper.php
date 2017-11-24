<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Seo;

use Windwalker\Core\DateTime\Chronos;
use Windwalker\Filter\OutputFilter;

/**
 * The SlugHelper class.
 *
 * @since  __DEPLOY_VERSION__
 */
class SlugHelper
{
	/**
	 * Make slug safe.
	 *
	 * @param string $title
	 * @param bool   $utf8
	 *
	 * @return  string
	 */
	public static function safe($title, $utf8 = false)
	{
		$slug = static::slugify($title, $utf8);

		if (trim($slug) === '')
		{
			$slug = OutputFilter::stringURLSafe(Chronos::current('Y-m-d-H-i-s'));
		}

		return $slug;
	}

	/**
	 * slugify
	 *
	 * @param string $title
	 * @param bool   $utf8
	 *
	 * @return  string
	 */
	public static function slugify($title, $utf8 = false)
	{
		if ($utf8)
		{
			return OutputFilter::stringURLUnicodeSlug($title);
		}

		if (function_exists('transliterator_transliterate'))
		{
			$title = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $title);
		}

		return OutputFilter::stringURLSafe($title);
	}
}
