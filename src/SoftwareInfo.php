<?php

/**
 * GameInfo.php
 */

namespace Iqu\AppStore;

/**
 * Class that deals with connecting to iTunes and fetching data for a particular software.
 *
 * @package Iqu\AppStore
 * @author Evangelos Karvounis
 */
class SoftwareInfo
{
    const
        ITUNES_URL = 'https://itunes.apple.com/',
        LOOKUP = 'lookup?id=',
        CURL_ERROR_FORMAT = 'Curl request failed for the url: %s',
        CHECK_COUNTRY_REGEX = '/\A\D{2,3}\z/';

    /**
     * Gets the basic information, keeps only the desired fields and returns the filtered results
     *
     * @param $softwareId Unique id for the software given to it by Apple
     * @param array $options curl request options
     * @param array $filters Collection of fields to be returned
     * @param string $country
     * @return array|null Filtered information
     */
    public function getFilteredInfo($softwareId, array $options, array $filters, $country = "us")
    {
        if (!$this->validateParams($softwareId, $country)) {
            return null;
        }
        $filteredResults = array();
        try {
            $output = $this->fetchData($softwareId, $country, $options);
            if (is_null($output)) {
                return null;
            }
            $result = json_decode($output);
            if (!$result->resultCount) {
                return null;
            }
            $result = $result->results[0];

            foreach ($filters as $filter) {
                if (array_key_exists($filter, $result)) {
                    $filteredResults[$filter] = $result->$filter;
                }
            }
        } catch (\Exception $ex) {
            print_r($ex);
            $filteredResults = null;
        }
        return $filteredResults;
    }

    /**
     * Returns all the information.
     *
     * @param $softwareId Unique id for the software given to it by Apple
     * @param array $options curl request options
     * @param string $country
     * @return mixed|null Unfiltered information
     */
    public function getAllInfo($softwareId, array $options, $country = 'us')
    {
        if (!$this->validateParams($softwareId, $country)) {
            return null;
        }

        $results = null;
        try {
            $results = $this->fetchData($softwareId, $country, $options);
        } catch (\Exception $ex) {
            print_r($ex);
            $results = null;
        }
        return $results;
    }

    /**
     * Prepares and executes the curl to the iTunes url.
     *
     * @param $softwareId Unique id for the software given to it by Apple
     * @param array $options curl request options
     * @param string $country
     * @return mixed|null Returns the response or null if something went wrong.
     */
    private function fetchData($softwareId, array $options, $country = 'us')
    {
        $url = $this->buildUrl($softwareId, $country);
        $results = null;

        try {
            $curlHandler = curl_init($url);
            curl_setopt_array($curlHandler, $options);
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
     * Builds the url.
     *
     * @param $softwareId Unique id for the software given to it by Apple
     * @param string $country
     * @return string
     */
    private function buildUrl($softwareId, $country = 'us')
    {
        return self::ITUNES_URL.$country.'/'.self::LOOKUP.$softwareId;
    }

    /**
     * Validates if the parameters are correct.
     * softwareId should be an integer and country code should be a 2 or 3-letter word.
     * @param $softwareId Unique id for the software given to it by Apple
     * @param string $country
     * @return bool
     */
    private function validateParams($softwareId, $country)
    {
        return ctype_digit($softwareId) && preg_match(self::CHECK_COUNTRY_REGEX, $country);
    }
}
