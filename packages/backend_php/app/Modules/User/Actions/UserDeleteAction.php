<?php

namespace App\Modules\User\Actions;

use App\Entities\User;
use App\Exceptions\UserException;

class UserDeleteAction
{

    /***
     * @return void
     */
    public function handle($id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new UserException("User is not exists!");
        }

        $user->delete();
    }
}
