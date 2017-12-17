<?php

namespace Bleidd\Authorization\Adapter;

use Bleidd\App\User\Model\User;
use Bleidd\App\User\Repository\UserRepository;
use Bleidd\Application\Runtime;
use Bleidd\Authorization\AuthorizationAdapterInterface;

class Session implements AuthorizationAdapterInterface
{

    /**
     * @param string $area
     * @param User   $user
     * @return bool
     */
    public function store(string $area, User $user): bool
    {
        Runtime::session()->set($this->sessionKey($area), $user);
        return true;
    }

    /**
     * @param string $area
     * @return bool
     */
    public function isAuthorized(string $area): bool
    {
        return Runtime::session()->has($this->sessionKey($area));
    }

    /**
     * @param string $area
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authorize(string $area, string $email, string $password): bool
    {
        if (!($user = (new UserRepository())
            ->find($email, 'email'))
        ) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);

        if (password_verify($password, $hash)) {
            $this->store($area, $user);
            return true;
        }

        return false;
    }

    /**
     * @param string $area
     * @return bool
     */
    public function forget(string $area): bool
    {
        Runtime::session()->unset($this->sessionKey($area));
        return true;
    }

    /**
     * @param string $area
     * @return string
     */
    protected function sessionKey(string $area): string
    {
        return sprintf('auth.%s', $area);
    }

}
