<?php

/**
 * Class NotFoundStudioUuidException
 *
 * An exception class thrown when no Studio UUID is present.
 */
class NotFoundStudioUuidException extends \Exception
{
    public function __construct($message = "Studio UUID must be set before making any requests!", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Class NotFoundStudioApiKeyException
 *
 * An exception class thrown when no API KEY is present.
 */
class NotFoundStudioApiKeyException extends \Exception
{
    public function __construct($message = "API KEY must be set before making any requests!", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Class NotFoundStudioApiGuidException
 *
 * An exception class thrown when no API GUID is present.
 */
class NotFoundStudioApiGuidException extends \Exception
{
    public function __construct($message = "API GUID must be set before making any requests!", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Class LightenbodyService
 *
 * This class helps to connect to lightenbody's api.
 *
 * Before you start, you have to obtain api credentials from your lightenbody's account.
 * The required credentials consist of `uuid`, `api-guid`, `api-key`.
 * The additional `api-source` is not mandatory. It helps to determine where the request
 * is coming from.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version GPL v2.0 or later of the gnu.org license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @author     Grzegorz Tomasiak <grzegorz@lightenbody.com>
 * @copyright  (c) 2017 lightenbody
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPL v2.0 or later
 * @version    0.20
 * @link       http://lightenbody.com
 */
class LightenbodyService
{
    private $apiVersion = 2;
    private $apiUrl = "https://studio.lightenbody.com/<uuid>/api/v<version>/public";
    private $apiKey;
    private $apiSource;
    private $uuid;
    private $responseCode;

    /**
     * LightenbodyService constructor requires a several variables to be able to connect to the api.
     * Each of the required parameters can be obtain in your lightenbody's account.
     *
     * @param string $uuid Unique Identifier (UUID) of the Studio.
     * @param string $apiGuid Globally Unique Identifier (GUID) of the Api credentials.
     * @param string $apiKey Key retrieved from the API credentials.
     * @param string $apiSource Source retrieved from the Api credentials.
     */
    public function __construct($uuid, $apiGuid, $apiKey, $apiSource = null)
    {
        // construct the api url
        $this->apiUrl = str_replace('<uuid>', $uuid, $this->apiUrl);
        $this->apiUrl = str_replace('<version>', $this->apiVersion, $this->apiUrl);

        $this->uuid = $uuid;
        $this->apiGuid = $apiGuid;
        $this->apiKey = $apiKey;
        $this->apiSource = $apiSource;
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return array|mixed|object
     */
    public function get($endpoint, array $data = array())
    {
        return $this->call($endpoint, 'get', $data);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return array|mixed|object
     */
    public function post($endpoint, array $data = array())
    {
        return $this->call($endpoint, 'post', $data);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return array|mixed|object
     */
    public function put($endpoint, array $data = array())
    {
        return $this->call($endpoint, 'put', $data);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return array|mixed|object
     */
    public function patch($endpoint, array $data = array())
    {
        return $this->call($endpoint, 'patch', $data);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return array|mixed|object
     */
    public function delete($endpoint, array $data =  array())
    {
        return $this->call($endpoint, 'delete', $data);
    }

    /**
     * @param string $endpoint Api endpoint.
     * @param string $httpVerb Request method.
     * @param array $data An array with data to send along with the call.
     * @return array|mixed|object
     * @throws \Exception
     */
    public function call($endpoint, $httpVerb, array $data = array())
    {
        // compose the url
        $this->apiUrl = $this->apiUrl . '/' . ltrim($endpoint, '/');

        if(!$this->uuid) throw new NotFoundStudioUuidException();
        if(!$this->apiKey) throw new NotFoundStudioApiGuidException();
        if(!$this->apiGuid) throw new NotFoundStudioApiGuidException();

        return $this->request($this->apiUrl, $httpVerb, $data);
    }

    /**
     * @param $url
     * @param $httpVerb
     * @param array $data
     * @param int $timeout
     * @return array|mixed|object
     * @throws Exception
     */
    private function request($url, $httpVerb, array $data = array(), $timeout = 15)
    {
        if(function_exists('curl_init') && function_exists('curl_setopt'))
        {
            $httpVerb = strtoupper($httpVerb);

            $headers = array(
                "X-lightenbody-api-key: $this->apiKey",
                "X-lightenbody-api-source: $this->apiSource",
                "X-lightenbody-api-guid: $this->apiGuid"
            );

            // setup the curl
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($headers, array()));
            curl_setopt($curl, CURLOPT_USERAGENT, "$this->uuid/lightenbody-api:v$this->apiVersion");
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $httpVerb);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);

            if(!empty($data))
            {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            $result = curl_exec($curl);
            $info = curl_getinfo($curl);

            // assign the response code
            $this->setResponseCode($info['http_code']);

            // if error found, throw the exception
            if($error = curl_error($curl))
            {
                throw new \Exception($error);
            }

            return json_decode($result) ?: $result;
        }

        throw new \RuntimeException('curl extension is not installed!');
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getApiSource()
    {
        return $this->apiSource;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return integer
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param $code
     * @return $this
     */
    private function setResponseCode($code)
    {
        $this->responseCode = $code;
        return $this;
    }
}