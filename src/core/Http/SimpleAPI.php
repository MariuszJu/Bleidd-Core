<?php

namespace Bleidd\Http;

class SimpleAPI extends AbstractRequest
{

    /** @var bool */
    protected $dump = 1;

    /** @var bool */
    protected $throwExceptions = false;

    /**
     * @param string $url
     * @param array  $params
     * @return self
     */
    public function rawGET(string $url, array $params = []): self
    {
        $this->sendGET($url, $params, true);
        return $this;
    }

    /**
     * @param bool $jsonDecode
     * @param bool $asArray
     * @return array|\stdClass|string
     */
    public function response(bool $jsonDecode = true, bool $asArray = true)
    {
        return parent::response($jsonDecode, $asArray);
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        // TODO: Implement isSuccess() method.
    }

    /**
     * @return string
     */
    public function errorMessage(): string
    {
        // TODO: Implement errorMessage() method.
    }

}
