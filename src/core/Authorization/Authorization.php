<?php

namespace Bleidd\Authorization;

use Bleidd\Application\Application;
use Bleidd\Authorization\Adapter\Session;

final class Authorization
{

    const AREA_SYSTEM = 'backend';
    const AREA_PAGE = 'frontend';

    /** @var AuthorizationAdapterInterface */
    private $adapter;

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Authorization constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {

    }

    /**
     * @param AuthorizationAdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(AuthorizationAdapterInterface $adapter): self
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return AuthorizationAdapterInterface
     */
    public function getAdapter(): AuthorizationAdapterInterface
    {
        if (empty($this->adapter)) {
            $this->adapter = new Session();
        }

        return $this->adapter;
    }

    /**
     * @param string $area
     * @return bool
     */
    public function isAuthorized(string $area): bool
    {
        return $this
            ->getAdapter()
            ->isAuthorized($area);
    }

    /**
     * @param string $area
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authorize(string $area, string $email, string $password): bool
    {
        return $this
            ->getAdapter()
            ->authorize($area, $email, $password);
    }

    /**
     * @param string $area
     * @return bool
     */
    public function forget(string $area): bool
    {
        return $this
            ->getAdapter()
            ->forget($area);
    }

}
