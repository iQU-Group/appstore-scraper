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
        PAGE_NUMBER_MIN = 1,
        PAGE_NUMBER_MAX = 10,
        FETCH_PAGE_MESSAGE = "Fetching page number %d, software id %d, country %s, sorted by %s.\n",
        CURL_ERROR_FORMAT = 'Curl request failed for the url: %s';

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
     * @throws \Exception
     * @return array All reviews for a software
     */
    public function getAllPagesReviews($countryCode, $softwareId, $sortType, array $curlOptions)
    {
        $reviews = array();
        try {
            for ($pageNumber = self::PAGE_NUMBER_MIN; $pageNumber <= self::PAGE_NUMBER_MAX; $pageNumber++) {

                $pageReviews = $this->getOnePageReviews($countryCode, $pageNumber, $softwareId, $sortType, $curlOptions);
                if (is_null($pageReviews)) {
                    return null;
                }
                $reviews = array_merge($pageReviews, $reviews);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
     * @throws \Exception
     * @return array|null
     */
    public function getOnePageReviews($countryCode, $pageNumber, $softwareId, $sortType, array $curlOptions)
    {
        $msg = $this->validateParams($countryCode, $pageNumber, $softwareId, $sortType);
        if ($msg) {
            throw new \Exception($msg);
        }

        print_r(sprintf(self::FETCH_PAGE_MESSAGE, $pageNumber, $softwareId, $countryCode, $sortType));
        $url = $this->buildUrl($countryCode, $pageNumber, $softwareId, $sortType);

        try {
            $results = $this->fetchData($url, $curlOptions);
            if (!$this->containsReviews($results) || is_null($results)) {
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

    /**
     * Parses the reviews and filters their fields. Returns the filtered review results.
     *
     * @param $reviews
     * @return array
     */
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
                'reviewId' => $currentReview->id->label,
                'voteSum' => $currentReview->{'im:voteSum'}->label,
                'voteCount' => $currentReview->{'im:voteCount'}->label
            );
        }
        return $customResults;
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

    /**
     * Validates the parameters. Returns a message that gives proper information to the user.
     * Returns an empty string if all the parameters are valid.
     *
     * @param string $countryCode
     * @param int $pageNumber
     * @param int $softwareId
     * @param string $sortType
     * @return string
     */
    private function validateParams($countryCode, $pageNumber, $softwareId, $sortType)
    {
        if (!Countries::checkIfValidCountryCode($countryCode)) {
            return 'Country code '.$countryCode.' is not valid.';
        }
        if (!$this->validatePageNumber($pageNumber)) {
            return 'Page number '.$pageNumber.' is not valid as it is outside the range ['.self::PAGE_NUMBER_MIN.', '.self::PAGE_NUMBER_MAX.'].';
        }
        if (!ctype_digit($softwareId)) {
            return 'Software Id '.$softwareId.' is not an integer.';
        }
        if (!$this->validateSortType($sortType)) {
            return 'Sort Type '.$sortType.' is not valid.';
        }
        return '';
    }

    /**
     * Validates the input sort type.
     *
     * @param string $sortType
     * @return bool
     */
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
        return '';
    }

    /**
     * Builds the url.
     *
     * @param string $countryCode
     * @param int $pageNumber
     * @param int $softwareId
     * @param string $sortType
     * @return string
     */
    private function buildUrl($countryCode, $pageNumber, $softwareId, $sortType)
    {
        return sprintf(self::ITUNES_REVIEWS_URL, $countryCode, $pageNumber, $softwareId, $sortType);
    }
}
