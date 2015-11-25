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
        CURL_ERROR_FORMAT = 'Curl request failed for the url: %s';

    /**
     * Gets the basic information for the software, keeps only the desired fields and returns the filtered results
     *
     * @param int $softwareId Unique id for the software given to it by Apple
     * @param array $curlOptions curl request options
     * @param array $filters Collection of fields to be returned
     * @param string $countryCode 2-letter country code
     * @throws \Exception
     * @return array|null Filtered information
     */
    public function getFilteredInfo($softwareId, array $curlOptions, array $filters, $countryCode = 'us')
    {
        $msg = $this->validateParams($softwareId, $countryCode);
        if ($msg) {
            throw new \Exception($msg);
        }
        $url = $this->buildUrl($softwareId, $countryCode);

        $filteredResults = array();
        try {
            $output = $this->fetchData($url, $curlOptions);
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
     * Returns all the information unfiltered.
     *
     * @param int $softwareId Unique id for the software given to it by Apple
     * @param array $curlOptions curl request options
     * @param string $countryCode
     * @throws \Exception
     * @return mixed|null Unfiltered information
     */
    public function getAllInfo($softwareId, array $curlOptions, $countryCode = 'us')
    {
        $msg = $this->validateParams($softwareId, $countryCode);
        if ($msg) {
            throw new \Exception($msg);
        }
        $url = $this->buildUrl($softwareId, $countryCode);

        $results = null;
        try {
            $results = $this->fetchData($url, $curlOptions);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
        return $results;
    }

    /**
     * Prepares and executes the curl to the iTunes url.
     *
     * @param string $url
     * @param array $curlOptions curl request options
     * @return mixed|null Returns the response or null if something went wrong.
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
     * Builds the url.
     *
     * @param int $softwareId Unique id for the software given to it by Apple
     * @param string $countryCode
     * @return string
     */
    private function buildUrl($softwareId, $countryCode = 'us')
    {
        return self::ITUNES_URL.$countryCode.'/'.self::LOOKUP.$softwareId;
    }

    /**
     * Validates if the parameters are correct.
     * Returns a message that gives proper information to the user.
     * Returns an empty string if all the parameters are valid.
     *
     * @param int $softwareId
     * @param string $countryCode
     * @return string
     */
    private function validateParams($softwareId, $countryCode)
    {
        if (!ctype_digit($softwareId)) {
            return 'Software Id '.$softwareId.' is not an integer.';
        }
        if (!Countries::checkIfValidCountryCode($countryCode)) {
            return 'Country code '.$countryCode.' is not valid.';
        }
        return '';
    }
}
