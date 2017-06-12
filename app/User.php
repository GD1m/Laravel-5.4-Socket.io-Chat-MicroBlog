<?php

namespace App;

use Illuminate\Auth\GenericUser;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends GenericUser implements AuthorizableContract
{
    use Authorizable;
}
