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
 * @since  1.3
 */
class SlugHelper
{
    /**
     * Make slug safe.
     *
     * @param string   $title
     * @param bool     $utf8
     * @param int|null $limit
     *
     * @return  string
     * @throws \Exception
     */
    public static function safe($title, $utf8 = false, ?int $limit = 8)
    {
        $slug = static::slugify($title, $utf8, $limit);

        if (trim($slug) === '') {
            $slug = OutputFilter::stringURLSafe(Chronos::current('Y-m-d-H-i-s'));
        }

        return $slug;
    }

    /**
     * slugify
     *
     * @param string   $title
     * @param bool     $utf8
     * @param int|null $limit
     *
     * @return  string
     */
    public static function slugify($title, $utf8 = false, ?int $limit = 8)
    {
        if ($limit) {
            $title = str($title)->truncate($limit)->__toString();
        }
        
        if ($utf8) {
            return OutputFilter::stringURLUnicodeSlug($title);
        }

        if (function_exists('transliterator_transliterate')) {
            $title = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $title);
        }

        return OutputFilter::stringURLSafe($title);
    }
}
