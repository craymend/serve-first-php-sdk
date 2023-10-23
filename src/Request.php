<?php

namespace Craymend\ServeFirst;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Perform API calls
 */
final class Request
{
    const MODE_SANDBOX = 'sandbox';
    const MODE_PRODUCTION = 'production';

    const DEFAULT_API_VERSION = 'v2';

    const BASE_URL_SANDBOX = 'https://api.sandbox.mysfsgateway.com/api';
    const BASE_URL_PRODUCTION = 'https://api.mysfsgateway.com/api';
    
    /**
     * @var string - "SANDBOX" | "PRODUCTION"
     */
    private $mode;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var string
     */
    private $sourceKey;

    /**
     * @var string
     */
    private $pin;

    /**
     * Defualts to "PRODUCTION" mode
     * 
     * @return null
     */
    public function __construct($sourceKey='', $pin='', $mode = '', $apiVersion=''){
        $this->sourceKey = $sourceKey;
        $this->pin = $pin;
        
        $this->mode = $mode ? $mode : self::MODE_PRODUCTION;
        $this->apiVersion = $apiVersion ? $apiVersion : self::DEFAULT_API_VERSION;

        $this->setMode($this->mode, $this->apiVersion); // set baseUrl

        return null;
    }

    /**
     * @return null
     */
    public function setMode($mode, $apiVersion=''){
        $apiVersion = $apiVersion ? $apiVersion : self::DEFAULT_API_VERSION;

        if($mode == self::MODE_SANDBOX){
            $this->baseUrl = self::BASE_URL_SANDBOX . '/' . $apiVersion;
        }else{
            $this->baseUrl = self::BASE_URL_PRODUCTION . '/' . $apiVersion;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getBaseUrl(){
        return $this->baseUrl;
    }

    /**
	 * @return Response
	 */
    public function get($path, array $params)
    {
        $url = $this->baseUrl . $path;
        return $this->sendRequest('GET', $url, $params);
    }

    /**
	 * @return Response
	 */
    public function post($path, array $body)
    {
        $url = $this->baseUrl . $path;
        return $this->sendRequest('POST', $url, $body);
    }

    /**
	 * @return Response
	 */
    public function put($path, array $body)
    {
        $url = $this->baseUrl . $path;
        return $this->sendRequest('PUT', $url, $body);
    }

    /**
	 * @return Response
	 */
    public function delete($path)
    {
        $url = $this->baseUrl . $path;
        return $this->sendRequest('DELETE', $url);
    }

    /**
     * Reliable test/example of aruguments and endpoint.
     * Jumpstart the use of the API from here.
     * 
     * Will return an empty array if no products have been created in the Serve First account.
     * 
	 * @return Response
	 */
    public function testEndpoint()
    {
        $uri = '/products';

        $data = [];

        $response = $this->get($uri, $data);
        
        return $response->getStatus();
    }

    /**
     * @return Response
     */
    private function sendRequest($method, $url, array $data = null)
    {
        $requestOptions = [];
        $headers = [];

        $authKey = $this->sourceKey . ':' . $this->pin;
        $base64AuthKey = base64_encode($authKey);

        // set headers
        if($this->sourceKey){
            $headers['Authorization'] = 'Basic ' . $base64AuthKey;
        }
        if ($method === 'POST' && null !== $data) {
            $headers['content-type'] = 'application/json';
        }
        if(count($headers) > 0){
            $requestOptions[RequestOptions::HEADERS] = $headers;
        }

        // set data
        if ($method === 'POST' && null !== $data) {
            $requestOptions[RequestOptions::JSON] = $data;
        }else if($method === 'GET' && null !== $data){
            $requestOptions[RequestOptions::QUERY] = $data;
        }

        // send request
        try {
            $client = new Client();

            $response = $client->request($method, $url, $requestOptions);

            $data = (array) json_decode($response->getBody(), true);

            return new Response(true, $data);
        }catch (\Exception $e) {
            $errors['errors'] = [$e->getMessage()];

            return new Response(false, [], $errors);
        }
    }
}