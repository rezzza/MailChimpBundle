<?php

namespace Rezzza\MailChimpBundle\Connection;

use Rezzza\MailChimpBundle\Request;
use Rezzza\MailChimpBundle\Response;

/**
 * Stub connection for test purposes
 */
class StubConnection implements ConnectionInterface
{
    protected $requests  = array();
    protected $responses = array();

    /**
     * Adds a response to the stack
     *
     * @param  Response $response
     */
    public function addResponse(Response $response)
    {
        $this->responses[] = $response;
    }

    /**
     * Returns the performed requests
     *
     * @return array
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Request $request)
    {
        $this->requests[] = $request;

        if (0 === count($this->responses)) {
            throw new \RuntimeException('There is no response to be returned.');
        }

        return array_pop($this->responses);
    }
}
