<?php

namespace Rezzza\MailChimpBundle;

use Rezzza\MailChimpBundle\Connection\ConnectionInterface;
use Mailchimp\MCAPI;

/**
 * Client
 *
 * @uses MCAPI
 * @author Sébastien HOUZÉ <s@verylastroom.com>
 */
class Client extends MCAPI
{
    protected $connection;
    protected $lastRequest;
    protected $lastResponse;

    /**
     * Constructor
     *
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        parent::__construct($apiKey);

        $this->lastRequest  = null;
        $this->lastResponse = null;
    }

    /**
     * Defines the connection
     *
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns the connection
     *
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritDoc}
     */
    public function callServer($method, $params)
    {
        $request  = new Request($method, $params);
        $response = $this->call($request);

        return $response->getContent();
    }

    /**
     * Perform the given Request
     *
     * @param Request $request
     *
     * @return Response
     */
    public function call(Request $request)
    {
        $this->errorMessage = null;
        $this->errorCode    = null;

        $request->setParam('apikey', $this->api_key);

        $response = $this->connection->execute($request);

        if ($response->isError()) {
            $content = $response->getContent();

            $this->errorMessage = $content['error'];
            $this->errorCode    = $content['code'];
        }

        $this->lastRequest  = $request;
        $this->lastResponse = $response;

        return $response;
    }

    /**
     * Returns the last request
     *
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * Returns the last response
     *
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
}
