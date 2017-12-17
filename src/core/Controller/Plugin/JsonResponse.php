<?php

namespace Bleidd\Controller\Plugin;

use Bleidd\Request\Request;
use Bleidd\Application\Runtime;

class JsonResponse
{

    /** @var array */
    protected $data;

    /**
     * JsonResponse constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Set HTTP header
     */
    protected function setHeader()
    {
        header('Content-Type: application/json');
    }

    /**
     * Clean output buffer
     */
    protected function cleanBuffer()
    {
        ob_clean();
    }

    /**
     * Print JSON
     */
    public function print()
    {
        $this->setHeader();
        $this->cleanBuffer();

        echo json_encode($this->data);
    }

}
