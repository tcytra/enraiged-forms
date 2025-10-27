<?php

namespace Enraiged\Forms\Builders\Traits;

use Enraiged\Builders\Secure\AssertSecure;
use Enraiged\Builders\Secure\AuthAssertions;
//use Enraiged\Builders\Secure\RoleAssertions;
//use Enraiged\Builders\Secure\UserAssertions;

trait SecurityAssertions
{
    use AssertSecure, AuthAssertions; //, RoleAssertions, UserAssertions;
}
