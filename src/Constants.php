<?php

/**
 * Constants.php
 */

namespace Iqu\AppStore;


abstract class Constants
{
    const
        ITUNES_URL = 'https://itunes.apple.com/',
        LOOKUP = 'lookup?id=',
        CURL_ERROR_FORMAT = 'Curl request failed for the url: %s',
        ITUNES_REVIEWS_URL = 'https://itunes.apple.com/%s/rss/customerreviews/page=%d/id=%d/sortBy=%s/json',
        PAGE_NUMBER_MIN = 1,
        PAGE_NUMBER_MAX = 10,
        FETCH_PAGE = "Fetching page number %d, software id %d, country %s, sorted by %s.\n";
}
