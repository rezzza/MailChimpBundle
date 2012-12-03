<?php

namespace Rezzza\MailChimpBundle\Connection;

use Rezzza\MailChimpBundle\Api\Request;
use Rezzza\MailChimpBundle\Api\Response;

/**
 * Interface that must be implemented by the connection classes
 */
interface ConnectionInterface
{
    /**
     * Executes the given request
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function execute(Request $request);
}
