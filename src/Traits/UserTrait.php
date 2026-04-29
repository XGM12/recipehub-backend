<?php

namespace App\Traits;

trait UserTrait
{
    public function getUserGroups(): array
    {
        return ['groups' => 'login:read'];
    }
}