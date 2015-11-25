<?php

/**
 * SoftwareReviews.php
 */

namespace Iqu\AppStore;

/**
 * Class that deals with fetching the reviews from AppStore.
 *
 * @package Iqu\AppStore
 * @author Evangelos Karvounis
 */
class SoftwareReviews
{
    const
        ITUNES_REVIEWS_URL = 'https://itunes.apple.com/%s/rss/customerreviews/page=%d/id=%d/sortBy=%s/json',
        CURL_ERROR_FORMAT = 'Curl request failed for the url: %s',
        PAGE_NUMBER_MIN = 1,
        PAGE_NUMBER_MAX = 10,
        FETCH_PAGE = "Fetching page number %d, software id %d, country %s, sorted by %s.\n";

    private $sortTypes = array(
        'mostRecent',
        'mostHelpful'
        );

    /**
     * Returns the reviews for a software id that can be found in all the pages.
     *
     * @param string $countryCode 2-letter country code
     * @param int $softwareId Unique software id
     * @param string $sortType The type the reviews are going to be sorted
     * @param array $curlOptions curl options
     * @return array All reviews for a software
     */
    public function getAllPagesReviews($countryCode, $softwareId, $sortType, array $curlOptions)
    {
        $reviews = array();
        for ($pageNumber = self::PAGE_NUMBER_MIN; $pageNumber <= self::PAGE_NUMBER_MAX; $pageNumber++) {
            $pageReviews = $this->getOnePageReviews($countryCode, $pageNumber, $softwareId, $sortType, $curlOptions);
            $reviews = array_merge($pageReviews, $reviews);
        }
        return $reviews;
    }

    /**
     * Returns the reviews for a software id that can be found in a single page.
     *
     * @param string $countryCode 2-letter country code
     * @param int $pageNumber Page that contains the reviews. Contains 50 reviews at most.
     * @param int $softwareId Unique software id
     * @param string $sortType The type the reviews are going to be sorted
     * @param array $curlOptions curl options
     * @return array|null
     */
    public function getOnePageReviews($countryCode, $pageNumber, $softwareId, $sortType, array $curlOptions)
    {
        print_r(sprintf(self::FETCH_PAGE, $pageNumber, $softwareId, $countryCode, $sortType));
        $url = $this->buildUrl($countryCode, $pageNumber, $softwareId, $sortType);

        try {
            $results = $this->fetchData($url, $curlOptions);
            if (!$this->containsReviews($results)) {
                return null;
            }
            $results = json_decode($results);
            $customResults = $this->parseReviews($results);
        } catch (\Exception $ex) {
            print_r($ex);
            $customResults = null;
        }
        return $customResults;
    }

    //TODO::CHECK IF THE REQUEST IS ACTUALLY RETURNING SOMETHING
    private function parseReviews($reviews)
    {
        $reviews = $reviews->feed->entry;
        $customResults = array();

        $reviewsNumber = count($reviews);
        for ($i = 1; $i < $reviewsNumber; $i++) {
            $currentReview = $reviews[$i];
            $reviewerUri = $currentReview->author->uri->label;

            $customResults[] = array(
                'reviewerId' => $this->filterReviewerId($reviewerUri),
                'reviewerUri' => $reviewerUri,
                'reviewerName' => $currentReview->author->name->label,
                'reviewTitle' => $currentReview->title->label,
                'reviewContent' => $currentReview->content->label,
                'rating' => $currentReview->{'im:rating'}->label,
                'reviewId' => $currentReview->id->label
            );
        }
        return $customResults;
    }

    /**
     * Filters the reviewer's URI in order to extract the unique reviewer's id.
     *
     * @param string $reviewerUri
     * @return mixed
     */
    private function filterReviewerId($reviewerUri)
    {
        $regex = '/id(\d+)/';
        if (preg_match($regex, $reviewerUri, $matches)) {
            return $matches[1];
        }
    }

    /**
     * Checks whether the response results are valid.
     *
     * @param $results
     * @return bool
     */
    private function containsReviews($results)
    {
        $results = json_decode($results);
        return array_key_exists('entry', $results->feed);
    }

    /**
     * Connects to the iTunes url and fetches the response.
     *
     * @param string $url
     * @param array $curlOptions curl options
     * @return mixed|null
     */
    private function fetchData($url, array $curlOptions)
    {
        $results = null;
        try {
            $curlHandler = curl_init($url);
            curl_setopt_array($curlHandler, $curlOptions);
            $results = curl_exec($curlHandler);

            curl_close($curlHandler);
            if (false === $results) {
                $msg = sprintf(self::CURL_ERROR_FORMAT, $url);
                throw new \Exception($msg);
            }
        } catch (\Exception $ex) {
            $results = null;
        }
        return $results;
    }

    private function validateParams($country, $pageNo, $softId, $sortType)
    {
        if (!Countries::checkIfValidCountryCode($country)) {
            return 'Country code '.$country.' is not valid.';
        }
        if (!$this->validatePageNumber($pageNo)) {
            return 'Page number '.$pageNo.' is not valid as it is outside the range ['.self::PAGE_NUMBER_MIN.', '.self::PAGE_NUMBER_MAX.'].';
        }
        if (!ctype_digit($softId)) {
            return 'Software Id '.$softId.' is not an integer.';
        }
        if (!$this->validateSortType($sortType)) {
            return 'Sort Type '.$sortType.' is not valid.';
        }
        return '';
    }

    private function validateSortType($sortType)
    {
        foreach ($this->sortTypes as $types) {
            if ($types === $sortType) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validates if the page number is inside the valid range.
     *
     * @param int $pageNumber
     * @return bool
     */
    private function validatePageNumber($pageNumber)
    {
        return ($pageNumber >= self::PAGE_NUMBER_MIN && $pageNumber <= self::PAGE_NUMBER_MAX);
    }

    /**
     * Builds the url.
     *
     * @param string $country
     * @param int $pageNo
     * @param int $softId
     * @param string $sort
     * @return string
     */
    private function buildUrl($country, $pageNo, $softId, $sort)
    {
        return sprintf(self::ITUNES_REVIEWS_URL, $country, $pageNo, $softId, $sort);
    }
}
