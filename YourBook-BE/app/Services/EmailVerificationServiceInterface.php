<?php

namespace App\Services;

use App\Models\Auth\User;

interface EmailVerificationServiceInterface
{

    /**
     * @param User $user
     * @param bool $newData
     * @return void
     */
    public function sendEmailVerificationCode(User $user, bool $newData = false): void;

    /**
     * @param User $user
     * @param string $code
     * @return array
     */
    public function verify(User $user, string $code): array;

}
