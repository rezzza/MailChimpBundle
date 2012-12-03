<?php

namespace Rezzza\MailChimpBundle;

/**
 * Represents a MailChimp API request
 *
 * @author Sébastien HOUZÉ <s@verylastroom.com> 
 */
class Request
{
    protected $method;
    protected $params;

    /**
     * Constructor
     *
     * @param  string $method The API method
     * @param  array  $params An array of params
     */
    public function __construct($method, array $params)
    {
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * Defines the method
     *
     * @param  string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Returns the method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Defines the params, the existing params are cleared
     *
     * @param  array $params An array of parameters
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Defines a param
     *
     * @param  string $name  The name of the param
     * @param  string $value The value of the param
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Returns a param
     *
     * @param  string $name
     *
     * @return string
     */
    public function getParam($name)
    {
        return $this->params[$name];
    }

    /**
     * Returns all the params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
