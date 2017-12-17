<?php

namespace Bleidd\App\User\Repository;

use Bleidd\App\User\Model\User;
use Bleidd\Database\Repository\AbstractRepository;

class UserRepository extends AbstractRepository
{

    /** @var string */
    public $table = 'user';

    /** @var string */
    public $model = User::class;

}
