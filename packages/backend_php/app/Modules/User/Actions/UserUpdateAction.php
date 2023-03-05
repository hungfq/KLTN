<?php

namespace App\Modules\User\Actions;

use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\User\DTO\UserUpdateDTO;

class UserUpdateAction
{
    public UserUpdateDTO $dto;
    public $user;
    public $role;

    /***
     * @param UserUpdateDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->updateUser();
    }

    protected function checkData()
    {
        $this->user = User::find($this->dto->id);
        if (!$this->user) {
            throw new UserException("User is not exists!");
        }

        $isExists = User::where('email', $this->dto->email)
            ->where('id', '!=', $this->dto->id)
            ->exists();
        if ($isExists) {
            throw new UserException("Email already exists!");
        }

        return $this;
    }

    protected function updateUser()
    {
        $this->user->update($this->dto->except('status')->toArray());
        return $this;
    }
}
