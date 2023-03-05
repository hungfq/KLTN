<?php

namespace App\Modules\Auth\Actions;

use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Auth\DTO\AuthLoginWithGoogleAccessTokenDTO;
use Illuminate\Support\Facades\Http;

class AuthLoginWithGoogleAccessTokenAction
{
    public AuthLoginWithGoogleAccessTokenDTO $dto;
    public $body;
    public $user;
    public $token;

    /**
     * handle
     * @param AuthLoginWithGoogleAccessTokenDTO $dto
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->getUserInfo()
            ->validateEmail()
            ->updateUserPicture();

        return [
            'userInfo' => [
                '_id' => $this->user->id,
                'email' => $this->user->email,
                'code' => $this->user->code,
                'name' => $this->user->name,
                'gender' => $this->user->gender,
                'notificationIds' => [],
                'picture' => $this->user->picture,
            ],
            'role' => data_get($this->user, 'roles.0.name'),
            'accessToken' => $this->token,
        ];
    }

    private function getUserInfo()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->dto->access_token
        ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if ($response->status() != 200) {
            throw new UserException('Invalid access_token');
        }

        $this->body = json_decode($response->body());

        return $this;
    }

    private function validateEmail()
    {
        $email = data_get($this->body, 'email');

        $this->user = User::where('email', $email)->first();
        if (!$this->user) {
            throw new UserException('User not found');
        }

        return $this;
    }

    private function updateUserPicture()
    {
        $this->token = $this->user->generateToken([
            'email' => $this->user->email,
            'role' => data_get($this->user, 'roles.0.name'),
        ]);

        if ($picture = data_get($this->body, 'picture')) {
            $this->user->picture = $picture;
            $this->user->updated_by = $this->user->id;
            $this->user->save();
        }
    }
}