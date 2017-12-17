<?php

namespace Bleidd\Http;

use Bleidd\Util\Inflector;

abstract class OAuth1 extends AbstractRequest
{

    /** @var string */
    protected $url;

    /** @var bool */
    protected $dump = false;

    /** @var bool */
    protected $throwExceptions = false;

    /** @var string */
    private $consumerKey;

    /** @var string */
    private $consumerSecret;

    /** @var string|null */
    private $token;

    /** @var string|null */
    private $tokenSecret;

    /** @var string */
    private $signatureMethod = 'HMAC-SHA1';

    /** @var string */
    private $version = '1.0';

    /**
     * SimpleRequest constructor
     *
     * @param string $url
     * @param string $consumerKey
     * @param string $consumerSecret
     */
    public function __construct(string $url, string $consumerKey, string $consumerSecret)
    {
        $this->setParams([
            'url' => $url
        ]);

        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;

        $this->addHeader('Content-Type', 'application/json');
    }

    /**
     * @param string $url
     * @param string $type
     * @param array  $params
     * @return string
     */
    private function generateBaseString(string $url, string $type, array $params = [])
    {
        $r = array();
        ksort($params);

        foreach ($params as $key => $value) {
            $r[] = $this->encodeString($key) . "=" . $this->encodeString($value);
        }
        
        return $type . '&' . $this->encodeString($url) . '&' . $this->encodeString(implode('&', $r));
    }

    /**
     * @param string $input
     * @return string
     */
    private function encodeString(string $input): string
    {
        return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode($input)));
    }

    /**
     * @param $url
     * @param $method
     * @param $params
     * @return string
     */
    private function generateSignature($url, $method, $params)
    {
        $url = strpos($url, 'http') !== false ? $url : $this->url . $url;
        $baseString = $this->generateBaseString($url, strtoupper($method), $params);
        $signingKey = rawurlencode($this->consumerSecret) . '&';
        
        if ($this->tokenSecret) {
            $signingKey .= $this->tokenSecret;
        }
        
        return $this->encodeString(base64_encode(hash_hmac('sha1', $baseString, $signingKey, true)));
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $data
     */
    protected function prepare(string $url, string $method, array &$data = [])
    {
        $params = [];
        $params['oauth_consumer_key'] = $this->consumerKey;
        $params['oauth_signature_method'] = $this->signatureMethod;
        $params['oauth_timestamp'] = time();
        $params['oauth_nonce'] = Inflector::randomString(8);
        $params['oauth_version'] = $this->version;

        $params['oauth_signature'] = $this->generateSignature($url, $method, $params);

        $headerParams = array_map(function($key, $value) {
            return sprintf('%s="%s"', $key, $value);
        }, array_keys($params), $params);

        $this->addHeader('Authorization', sprintf('OAuth %s', implode(',', $headerParams)));
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
     * @return mixed
     */
    public function rawResponse()
    {
        return parent::rawResponse();
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $params
     * @param string $signature
     * @return bool
     */
    public function validateSignature(string $url, string $method, array $params, string $signature): bool
    {
        return $this->generateSignature($url, $method, $params) == $signature;
    }

}
