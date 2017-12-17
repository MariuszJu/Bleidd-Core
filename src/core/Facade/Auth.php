<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;
use Bleidd\Authorization\Authorization;

/**
 * @method isAuthorized(string $area): bool
 * @method authorize(string $area, string $email, string $password): bool
 */
final class Auth extends Facade
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @return Authorization
     */
    public static function getService(): Authorization
    {
        return Runtime::auth();
    }

}
