<?php

namespace Bleidd\Request;

use Bleidd\Router\Router;

class Request
{

    const HTTP_CODE_OK = 200;
    const HTTP_CODE_CREATED = 201;
    const HTTP_CODE_ACCEPTED = 202;
    const HTTP_CODE_NO_CONTENT = 204;

    const HTTP_CODE_MOVED_PERMANENTLY = 301;
    const HTTP_CODE_FOUND = 302;

    const HTTP_CODE_BAD_REQUEST = 400;
    const HTTP_CODE_UNAUTHORIZED = 401;
    const HTTP_CODE_FORBIDDED = 403;
    const HTTP_CODE_NOT_FOUND = 404;
    const HTTP_CODE_METHOD_NOT_ALLOWED = 405;

    const HTTP_CODE_INTERNAL_SERVER_ERROR = 500;
    const HTTP_CODE_SERVICE_UNAVAILABLE = 503;
    const HTTP_CODE_GATEWAY_TIMEOUT = 504;

    /** @var float */
    protected static $requestStart;

    /** @var Router */
    protected $router;

    /**
     * Request constructor
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;

        if (empty(self::$requestStart)) {
            self::$requestStart = microtime(true);
        }
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING));
    }

    /**
     * @return string
     */
    public function protocol(): string
    {
        $protocol = filter_input(INPUT_SERVER, 'HTTPS');
        return $protocol && $protocol != 'off' ? 'HTTPS' : 'HTTP';
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() == 'POST';
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() == 'GET';
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    /**
     * @param int|null $code
     * @return int
     */
    public function httpCode(int $code = null): int
    {
        return http_response_code($code);
    }
    
    /**
     * @param string|null $key
     * @return mixed
     */
    public function post(string $key = null)
    {
        return !empty($key) ? filter_input(INPUT_POST, $key) : filter_input_array(INPUT_POST);
    }
    
    /**
     * @param string|null $key
     * @return mixed
     */
    public function get(string $key = null)
    {
        return $key ? filter_input(INPUT_GET, $key) : filter_input_array(INPUT_GET);
    }

    /**
     * @param bool $withoutQuery
     * @return string
     */
    public function uri(bool $withoutQuery = false): string
    {
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

        return $withoutQuery ? $this->removeQueryFromUrl($uri) : $uri;
    }

    /**
     * @param bool $withoutQuery
     * @return string
     */
    public function url(bool $withoutQuery = false): string
    {
        $server = filter_input_array(INPUT_SERVER);
        $uri = $this->uri();

        if (substr($uri, 0, 1) == '/') {
            $uri = substr($uri, 1);
        }
        
        $https = isset($server['HTTPS']);
        if (isset($server['HTTP_X_FORWARDED_PROTO']) && strtolower($server['HTTP_X_FORWARDED_PROTO']) == 'https') {
            $https = true;
        }

        $url = sprintf('http%s://%s/%s', $https ? 's' : '', $server['HTTP_HOST'], $uri);

        return $withoutQuery ? $this->removeQueryFromUrl($url) : $url;
    }

    /**
     * @param bool $asArray
     * @return string|array
     */
    public function query(bool $asArray = false)
    {
        $query = parse_url($this->uri())['query'] ?? '';
        
        if (!$asArray) {
            return $query;
        }

        if (empty($query)) {
            return [];
        }

        $parts = strpos($query, '&') !== false ? explode('&', $query) : [$query];
        
        $array = [];
        foreach ($parts as $part) {
            $exploded = explode('=', $part);
            $array[$exploded[0]] = urldecode($exploded[1]);
        }
        
        return $array;
    }

    /**
     * @return bool|string
     */
    public function stdin()
    {
        return file_get_contents("php://stdin");
    }

    /**
     * @return bool|string
     */
    public function phpInput()
    {
        return file_get_contents("php://input");
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        return getallheaders();
    }

    /**
     * @return array
     */
    public function input(): array
    {
        $input = [];

        $get = $this->get();
        $post = $this->post();
        $stdin = $this->stdin();
        $phpInput = $this->phpInput();

        if (!empty($get)) {
            $input = is_array($get) ? $get : [$get];
        }
        if (!empty($post)) {
            $input = array_merge($input, is_array($post) ? $post : [$post]);
        }
        if (!empty($stdin)) {
            $stdin = !is_array($stdin) ? json_decode($stdin, true) : [$stdin];
            $input = array_merge($input, is_array($stdin) ? $stdin : [$stdin]);
        }
        if (!empty($phpInput)) {
            $phpInput = !is_array($phpInput) ? json_decode($phpInput, true) : [$phpInput];
            $input = array_merge($input, is_array($phpInput) ? $phpInput : [$phpInput]);
        }

        return $input;
    }

    /**
     * @return string
     */
    public function requestTime(): string
    {
        $time = (float) number_format(microtime(true) - self::$requestStart, 10);

        if ($time < 0.1) {
            $time = (number_format($time * 1000, 2)) . 'ms';
        } else if ($time < 1.0) {
            $time = number_format($time, 2) . 's';
        } else {
            $time = number_format($time, 1) . 's';
        }

        return $time;
    }

    /**
     * @return Router
     */
    public function router(): Router
    {
        return $this->router;
    }

    /**
     * @param string $url
     * @return string
     */
    private function removeQueryFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] ?? '';
    }

}
