<?php

namespace App\Modules\User\Actions;

use App\Entities\Role;
use App\Entities\User;
use App\Modules\User\DTO\UserViewDTO;

class UserViewAction
{
    /**
     * @param $dto UserViewDTO
     */
    public function handle($dto)
    {
        $query = User::query()
            ->with(['createdBy', 'updatedBy'])
            ->join('user_has_roles', 'user_has_roles.user_id', '=', 'users.id')
            ->orderBy('created_at', 'DESC');

        if ($search = $dto->search) {
            $query->where('code', $search)
                ->orWhere('name', $search)
                ->orWhere('email', $search);
        }


        if ($type = $dto->type) {
            $role = Role::where('name', $type)->first();
            $query->where('user_has_roles.role_id', data_get($role, 'id'));
        }

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}