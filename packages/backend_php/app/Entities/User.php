<?php

namespace App\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class User extends BaseSoftModel implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    protected $meta = [];

    use Authenticatable, Authorizable, HasRoles;

    const STATUS_ACTIVE = 'AC';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return array_merge($this->meta, [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    public function setJWTCustomClaims($meta)
    {
        $this->meta = $meta;
    }

    public static function getTableName()
    {
        return (new static)->getTable();
    }

    public function generateToken($params = [])
    {
        $this->setJWTCustomClaims([
            'token_type' => data_get($params, 'token_type', 'internal'),
            'exp' => time() + 15 * 60,
            'email' => data_get($params, 'email', 'no_email'),
            'role' => data_get($params, 'role', 'no_role'),
        ]);

        $token = JWTAuth::fromUser($this);

        return $token;
    }
}
