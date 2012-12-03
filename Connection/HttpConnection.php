<?php

namespace Rezzza\MailChimpBundle\Connection;

use Rezzza\MailChimpBundle\Request;
use Rezzza\MailChimpBundle\Response;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;

/**
 * HTTP connection for the MailChimp API client
 */
class HttpConnection implements ConnectionInterface
{
    const API_URL = 'http://api.mailchimp.com/1.3/?output=php';

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
        $this->client->setCookies(array());
        $this->client->resetParameters();
        $this->client->setUri($this->getUri(
            $request->getMethod(),
            $request->getParam('apikey')
        ));
        $this->client->setParameterPost($request->getParams());
        $this->client->setMethod(HttpRequest::METHOD_POST);

        try {
            $rawResponse = $this->client->send();
            $response = unserialize($rawResponse->getBody());
            if (false === $response) {
                // bad response
                $response = array(
                    'error' => 'Bad Response. Got this: ' . $rawResponse->getBody(),
                    'code'  => -99
                );
            }
        } catch (\Exception $e) {
            if ($e instanceof TimeoutException) {
                // timeout exception
                $response = array(
                    'error' => 'Could not read response (timed out)',
                    'code'  => -98
                );
            } else {
                // unknown exception
                $response = array(
                    'error' => 'An error occured: ' . $e->getMessage(),
                    'code'  => -99
                );
            }
        }

        if (is_array($response) && isset($response['error'])) {
            // return an error response
            return new Response($response, Response::STATUS_ERROR);
        }

        // return a success response
        return new Response($response);
    }

    private function getUri($method, $apiKey)
    {
        $dc = 'us1';
        if (strstr($apiKey, '-')){
            list($key, $dc) = explode('-', $apiKey, 2);
            if (!$dc) $dc = 'us1';
        }

        $scheme = $this->secure ? 'https://' : 'http://';

        $parts = parse_url(self::API_URL);

        return $scheme . $dc . '.' . $parts['host'] . $parts['path'] . '?' . $parts['query'] . '&method=' . $method;
    }
}
