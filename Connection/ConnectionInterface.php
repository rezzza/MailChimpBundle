<?php

namespace Rezzza\MailChimpBundle\Connection;

use Rezzza\MailChimpBundle\Request;
use Rezzza\MailChimpBundle\Response;

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
    function execute(Request $request);
}
