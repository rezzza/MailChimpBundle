<?php

namespace Rezzza\MailChimpBundle;

/**
 * Response
 *
 * @author Sébastien HOUZÉ <s@verylastroom.com> 
 */
class Response
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';

    /**
     * Constructor
     *
     * @param  mixed  $content
     * @param  string $status
     */
    public function __construct($content, $status = self::STATUS_SUCCESS)
    {
        $this->setContent($content);
        $this->setStatus($status);
    }

    /**
     * Indicates whether the response is successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return self::STATUS_SUCCESS === $this->status;
    }

    /**
     * Indicates whether the response is an error
     *
     * @return boolean
     */
    public function isError()
    {
        return self::STATUS_ERROR === $this->status;
    }

    /**
     * Defines the content of the response
     *
     * @param  mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the content
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Defines the status
     *
     * @param  string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
