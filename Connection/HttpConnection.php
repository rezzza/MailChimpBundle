<?php

namespace Rezzza\MailChimpBundle\Connection;

use Rezzza\MailChimpBundle\Api\Request;
use Rezzza\MailChimpBundle\Api\Response;
use Guzzle\Http\Client as HttpClient;

/**
 * HTTP connection for the MailChimp API client
 */
class HttpConnection implements ConnectionInterface
{

    const HTTP_CODE_CONNECTION_TIMED_OUT = 118;
    const INTERNAL_CODE_UNKNOWN_EXCEPTION_ERROR = -100;
    const INTERNAL_CODE_GENERAL_ERROR = -99;
    const INTERNAL_CODE_TIMEOUT = -98;
    const INTERNAL_CODE_PARSE_EXCEPTION = -101;
    const API_URL = 'https://api.mailchimp.com/2.0/';

    protected $secure;
    protected $client;

    /**
     * Constructor
     *
     * @param  boolean    $secure Whether to use the secure API
     * @param  HttpClient $client An optional HttpClient instance
     */
    public function __construct($secure = false, HttpClient $client = null)
    {
        if (null === $client) {
            $client = new HttpClient();
        }

        $this->secure = $secure;
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Request $request)
    {
        $uri = $this->getUri($request->getMethod(), $request->getParam('apikey'));
        $httpRequest = $this->client
                ->post($uri, array('Content-Type' => 'application/json'))
                ->setBody(json_encode($request->getParams()));

        $rawResponse = false;
        try {
            $rawResponse = $httpRequest->send();

            $response = $this->parseResponse($rawResponse);
        } catch (\Exception $e) {
            // unknown exception
            $response = array(
                'error' => 'An error occurred: ' . $e->getMessage(),
                'code' => self::INTERNAL_CODE_UNKNOWN_EXCEPTION_ERROR
            );
        }

        if (false === $response) {
            $response = $this->handleEdgeCase($rawResponse);
        }

        if (is_array($response) && isset($response['error'])) {
            // return an error response
            return new Response($response, Response::STATUS_ERROR);
        }

        // return a success response
        return new Response($response);
    }

    /**
     * Check if response is Timeout
     * @param mixed $rawResponse
     *
     * @return bool
     */
    private function isReponseTimeout($rawResponse)
    {
        return $rawResponse instanceof \Guzzle\Http\Message\Response && self::HTTP_CODE_CONNECTION_TIMED_OUT == $rawResponse->getStatusCode();
    }

    private function getUri($method, $apiKey)
    {
        $dc = null;
        if (strstr($apiKey, '-')) {
            list($key, $dc) = explode('-', $apiKey, 2);            
        }
        if (empty($dc) === true) {
            $dc = 'us1';
        }

        $scheme = $this->secure ? 'https://' : 'http://';
        $parts = parse_url(self::API_URL);

        $uri = $scheme . $dc . '.' . $parts['host'] . $parts['path'] . $method . '.php';
        if (isset($parts['query']) === true) {
            $uri .= '?' . $parts['query'];
        }
        
        return $uri;
    }

    /**
     * Handle any not yet treated response
     * @param mixed $rawResponse
     *
     * @return array
     */
    private function handleEdgeCase($rawResponse)
    {
        // bad response
        return array(
            'error' => 'Bad Response. Got this: ' . $rawResponse->getBody(),
            'code' => self::INTERNAL_CODE_GENERAL_ERROR
        );
    }

    /**
     * Check if parameter is serialized
     * @param mixed $data parameter
     *
     * @return bool
     */
    private function isSerialized($data)
    {
        return false !== @unserialize($data);
    }

    /**
     * Try to parse given deserializable response
     * @param mixed $rawResponse
     *
     * @return array|mixed
     */
    private function parseResponse($rawResponse)
    {
        if (false === $rawResponse) {
            return false;
        }

        if ($this->isReponseTimeout($rawResponse)) {
            // timeout exception
            return array(
                'error' => 'Could not read response (timed out)',
                'code' => self::INTERNAL_CODE_TIMEOUT
            );
        }

        if ($this->isSerialized($rawResponse->getBody())) {
            return @unserialize($rawResponse->getBody());
        }

        return array(
            'error' => 'An error occurred: Unable to parse response',
            'code' => self::INTERNAL_CODE_PARSE_EXCEPTION
        );
    }

}
