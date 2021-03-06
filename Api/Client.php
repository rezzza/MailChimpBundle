<?php

namespace Rezzza\MailChimpBundle\Api;

use Rezzza\MailChimpBundle\Connection\ConnectionInterface;

/**
 * Client
 *
 * @uses \Mailchimp
 * @author Sébastien HOUZÉ <s@verylastroom.com>
 */
class Client extends \Mailchimp
{
    /** @var ConnectionInterface */
    protected $connection;

    /** @var Request */
    protected $lastRequest;

    /** @var Response */
    protected $lastResponse;

    /**
     * @var string
     * @deprecated Prefer using Client::getLastErrorMessage()
     */
    public $errorMessage;

    /**
     * @var int
     * @deprecated Prefer using Client::getLastErrorCode()
     */
    public $errorCode;

    /**
     * @var \Mailchimp_Lists
     * @deprecated Prefer using Client::getMailChimpLists()
     */
    public $lists;

    /**
     * Constructor
     *
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        parent::__construct($apiKey, array(
            'CURLOPT_FOLLOWLOCATION' => true,
        ));

        $this->lastRequest = null;
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
    public function call($method, $params)
    {
        $request = new Request($method, $params);
        $response = $this->callServer($request);

        return $response->getContent();
    }

    /**
     * Perform the given Request
     *
     * @param Request $request
     *
     * @return Response
     */
    private function callServer(Request $request)
    {
        $this->errorMessage = null;
        $this->errorCode = null;

        $request->setParam('apikey', $this->apikey);

        $response = $this->connection->execute($request);

        if ($response->isError()) {
            $content = $response->getContent();

            $this->errorMessage = $content['error'];
            $this->errorCode = $content['code'];
        }

        $this->lastRequest = $request;
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

    /**
     * @return string
     */
    public function getLastErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return int
     */
    public function getLastErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Allow to send email in batch
     *
     * @return \Mailchimp_Lists
     */
    public function getMailChimpLists()
    {
        return $this->lists;
    }
}
