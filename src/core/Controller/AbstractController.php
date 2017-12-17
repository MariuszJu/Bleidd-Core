<?php

namespace Bleidd\Controller;

use Bleidd\Request\Request;
use Bleidd\Session\Session;
use Bleidd\Application\Runtime;
use Bleidd\Controller\Plugin\Response;
use Bleidd\Controller\Plugin\Redirect;

abstract class AbstractController
{

    /**
     * @param string|null $key
     * @param mixed       $default
     * @return mixed
     */
    public function params(string $key = null, $default = null)
    {
        $currentRoute = $this->request()->router()->getMatchedRoute();
        return empty($key) ? $currentRoute->getParams() : $currentRoute->getParam($key, $default);
    }

    /**
     * @return Response
     */
    public function response(): Response
    {
        return new Response($this);
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return Runtime::request();
    }

    /**
     * @return Redirect
     */
    public function redirect(): Redirect
    {
        return new Redirect($this);
    }

    /**
     * @return Session
     */
    public function session(): Session
    {
        return Runtime::session();
    }
    
}
