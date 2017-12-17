<?php

namespace Bleidd\Authorization;

use Bleidd\App\User\Model\User;

interface AuthorizationAdapterInterface
{

    /**
     * @param string $area
     * @param User   $user
     * @return mixed
     */
    public function store(string $area, User $user): bool;

    /**
     * @param string $area
     * @return mixed
     */
    public function isAuthorized(string $area): bool;

    /**
     * @param string $area
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public function authorize(string $area, string $email, string $password): bool;

    /**
     * @param string $area
     * @return mixed
     */
    public function forget(string $area): bool;

}

