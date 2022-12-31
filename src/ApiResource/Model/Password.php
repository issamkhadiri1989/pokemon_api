<?php

declare(strict_types=1);

namespace App\ApiResource\Model;

class Password
{
//    #[SecurityAssert\UserPassword(message: 'Wrong value for your current password')]
    private ?string $currentPassword = null;

    private ?string $newPassword = null;
}