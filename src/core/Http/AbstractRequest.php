<?php

namespace Bleidd\Http;

use Bleidd\Cache\Cache;

abstract class AbstractRequest
{

    /** @var resource */
    private $handler;

    /** @var string */
    protected $method;

    /** @var string */
    protected $url;

    /** @var array */
    private $data = [];

    /** @var mixed */
    private $response;

    /** @var array */
    private $curlInfo;

    /** @var string */
    private $error;

    /** @var string */
    private $user;

    /** @var string */
    private $password;

    /** @var Cache */
    private $cache;

    /** @var bool */
    private $useCache = false;

    /** @var array */
    private $headers;

    /** @var bool */
    protected $dump = false;

    /** @var bool */
    protected $throwExceptions = false;

    /** @var array */
    protected $opts = [
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    ];

    /**
     * @return bool
     */
    abstract public function isSuccess(): bool;

    /**
     * @return string
     */
    abstract public function errorMessage(): string;

    /**
     * Set request params
     *
     * @param array $params
     * @return self
     */
    protected function setParams(array $params): self
    {
        if (isset($params['method'])) {
            $this->method = strtoupper($params['method']);
        }
        if (isset($params['data'])) {
            $this->data = $params['data'];
        }
        if (isset($params['url'])) {
            $url =  $params['url'];

            if (substr($url, -1, 1) != '/') {
                $url .= '/';
            }

            $this->url = $url;
        }
        if (isset($params['password']) && isset($params['user'])) {
            $this->password = $params['password'];
            $this->user = $params['user'];
        }

        return $this;
    }

    /**
     * @param Cache $cache
     * @return self
     */
    protected function setCache(Cache $cache): self
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @param mixed $opt
     * @param mixed $value
     * @return AbstractRequest
     */
    protected function setOpt($opt, $value): self
    {
        $this->opts[$opt] = $value;
        return $this;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getCacheKey(string $url): string
    {
        return str_replace(['/', '?', '=', '[', ']', ':'], '-', $url);
    }

    /**
     * @return array
     */
    private function parseHeaders(): array
    {
        $array = [];

        foreach ($this->headers as $header => $value) {
            $array[] = sprintf('%s: %s', $header, $value);
        }

        return $array;
    }

    /**
     * Make a custom request
     *
     * @throws \Exception
     * @param string      $method
     * @param string|null $url
     * @param mixed       $data
     */
    private function request(string $method, string $url = null, $data = null)
    {
        $this->error = null;
        $this->method = strtoupper($method);

        $this->data = $data;

        if ($this->method == 'GET' && !empty($data)) {
            if (substr($url, -1, 1) == '/') {
                $url = substr($url, 0, strlen($url) - 1);
            }
            $url = sprintf('%s?%s', $url, http_build_query($data));
        }

        if ($this->useCache && $this->cache->has($this->getCacheKey($url))) {
            $this->response = $this->cache->get($this->getCacheKey($url));
            $this->curlInfo['http_code'] = 200;
            $this->useCache = false;
            return;
        }

        $this->handler = curl_init();

        $headers = $this->headers;

        $opts = $this->opts + [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $this->method,
        ];

        if (!empty($this->password) && !empty($this->user)) {
            $opts[CURLOPT_USERPWD] = sprintf('%s:%s', $this->user, $this->password);
        }

        if (in_array($this->method, ['POST', 'PUT', 'PATCH']) && !empty($this->data)) {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = $this->data;

            //$headers['Content-Length'] = strlen($this->data);
        }

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = $this->parseHeaders();
        }
        
        curl_setopt_array($this->handler, $opts);
        
        $this->response = curl_exec($this->handler);
        $this->curlInfo = curl_getinfo($this->handler);

        if (curl_errno($this->handler)) {
            $this->error = curl_error($this->handler);

            if ($this->throwExceptions) {
                throw new \Exception($this->error);
            }
        }

        curl_close($this->handler);

        if ($this->dump) {
            echo 'URL:<pre>';
            print_r(urldecode($url));
            echo '</pre>';

            echo 'ERROR: <pre>';
            var_dump($this->error);
            echo '</pre>';

            if (!empty($this->data)) {
                echo 'DATA: <pre>';
                print_r($this->data);
                echo '</pre>';
            }

            echo 'CURL INFO:<pre>';
            print_r($this->curlInfo);
            echo '</pre>';

            echo 'RESPONSE: <pre>';
            print_r($this->response);
            echo '</pre>'; die;
        }

        if ($this->useCache) {
            $this->cache->set($this->getCacheKey($url), $this->response);
            $this->useCache = false;
        }
    }

    /**
     * @param string $header
     * @param mixed  $value
     * @return self
     */
    protected function addHeader(string $header, $value): self
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * Use cache for current request
     */
    protected function cacheRequest()
    {
        $this->cache instanceof Cache && ($this->useCache = true);
    }

    /**
     * @return int|null
     */
    protected function httpCode()
    {
        return $this->curlInfo['http_code'] ?? null;
    }

    /**
     * Get request response
     *
     * @param bool $jsonDecode
     * @param bool $asArray
     * @return array|\stdClass|string
     */
    protected function response(bool $jsonDecode = true, bool $asArray = true)
    {
        return $jsonDecode ? json_decode($this->response, $asArray) : $this->response;
    }

    /**
     * @return mixed
     */
    protected function rawResponse()
    {
        return $this->response;
    }

    /**
     * @return string|null
     */
    protected function error()
    {
        return $this->error;
    }

    /**
     * @param string|null $method
     * @return string
     */
    protected function method(string $method = null): string
    {
        if (!empty($method)) {
            $this->method = $method;
        }

        return $this->method;
    }

    /**
     * Send GET request
     *
     * @param string $url
     * @param array  $data
     * @param bool   $absoluteUrl
     * @return self
     */
    protected function sendGET(string $url, array $data = [], bool $absoluteUrl = false): self
    {
        $this->request('GET', $absoluteUrl ? $url : $this->url . $url, $data);
        return $this;
    }

    /**
     * Send PUT request
     *
     * @param string $url
     * @param array  $data
     * @param bool   $absoluteUrl
     * @return self
     */
    protected function sendPUT(string $url, array $data = [], bool $absoluteUrl = false): self
    {
        $this->request('PUT', $absoluteUrl ? $url : $this->url . $url, $data);
        return $this;
    }

    /**
     * Send POST request
     *
     * @param string $url
     * @param mixed  $data
     * @param bool   $absoluteUrl
     * @return self
     */
    protected function sendPOST(string $url, $data = null, bool $absoluteUrl = false): self
    {
        $this->request('POST', $absoluteUrl ? $url : $this->url . $url, $data);
        return $this;
    }

    /**
     * Send POST request
     *
     * @param string $url
     * @param mixed  $data
     * @param bool   $absoluteUrl
     * @return self
     */
    protected function sendPATCH(string $url, $data = null, bool $absoluteUrl = false): self
    {
        $this->request('PATCH', $absoluteUrl ? $url : $this->url . $url, $data);
        return $this;
    }

    /**
     * Send DELETE request
     *
     * @param string $url
     * @param mixed  $data
     * @param bool   $absoluteUrl
     * @return self
     */
    protected function sendDELETE(string $url, $data = null, bool $absoluteUrl = false): self
    {
        $this->request('DELETE', $absoluteUrl ? $url : $this->url . $url);
        return $this;
    }

}
