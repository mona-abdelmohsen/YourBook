<?php

namespace App\Repository;

use Illuminate\Support\Collection;

interface UserRepositoryInterface
{

    /** Get User Interests List */
    public function getUserInterests(int $user_id = null): Collection|array|null;

    /** Update User Interests List */
    public function updateUserInterests(array $data);

    /** Get Top Account  */
    public function getTopAccounts($limit = 20): Collection|array|null;

    public function markAccountAsUpdated($user_id = null);

    public function getAccountGeneralInfo($user_id = null): Collection|array|null;

}
