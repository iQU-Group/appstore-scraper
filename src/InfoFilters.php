<?php

/**
 * InfoFilters.php
 */

namespace Iqu\AppStore;

/**
 * Class that contains the filters for the software information results.
 * These filters map to the fields of the response.
 * You can add filters here or create custom ones.
 *
 * @package Iqu\AppStore
 * @author Evangelos Karvounis
 */
abstract class InfoFilters
{
    private static $filters = array(
    'trackName',
    'sellerName',
    'trackViewUrl',
    'version',
    'description',
    'genres',
    'artworkUrl100',
    'icon',
    'video',
    'screenshotUrls'
    );

    public static function getFilters()
    {
        return self::$filters;
    }
}
