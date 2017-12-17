<?php

namespace Bleidd\Controller\Plugin;

use Bleidd\Request\Request;
use Bleidd\Application\Application;
use Bleidd\Controller\AbstractController;

class Response
{

    /** @var AbstractController */
    protected $controller;

    /** @var array */
    protected $headers;

    /** @var string */
    protected $content;

    /** @var int */
    protected $code;

    /**
     * Response constructor
     *
     * @param AbstractController $controller
     */
    public function __construct(AbstractController $controller)
    {
        $this->headers = [];
        $this->controller = $controller;
        $this->code = Request::HTTP_CODE_OK;
    }

    /**
     * @param array $data
     * @return self
     */
    public function json(array $data = []): self
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->content = json_encode($data);

        return $this;
    }

    /**
     * @param int $code
     * @return self
     */
    public function code(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param $header
     * @param mixed $value
     * @return self
     */
    public function setHeader(string $header, $value): self
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @param Application $application
     */
    public function prepareResponse(Application $application)
    {
        $this->cleanBuffer();
        $this->setHeaders();
        $this->setResponseCode();
        $this->printResponse();
    }

    /**
     * set HTTP headers
     */
    private function setHeaders()
    {
        foreach ($this->headers as $header => $value) {
            header(sprintf('%s: %s', $header, $value));
        }
    }

    /**
     * Set HTTP response code
     */
    private function setResponseCode()
    {
        http_response_code(!empty($this->code) ? $this->code : Request::HTTP_CODE_OK);
    }

    /**
     * Clean output buffer
     */
    private function cleanBuffer()
    {
        ob_clean();
    }

    /**
     * Print response
     */
    private function printResponse()
    {
        echo $this->content;
    }

}
