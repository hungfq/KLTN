<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Auth\Actions\AuthLoginWithGoogleAccessTokenAction;
use App\Modules\Auth\Validators\AuthLoginWithGoogleAccessTokenValidator;
use Illuminate\Support\Facades\DB;


class AuthController extends ApiController
{
    public function loginWithGoogleAccessToken(AuthLoginWithGoogleAccessTokenValidator $validator,
                                               AuthLoginWithGoogleAccessTokenAction    $action)
    {
        $validator->validate($this->request->all());

        $result = DB::transaction(function () use ($action, $validator) {
            return $action->handle($validator->toDTO());
        });

        return response()->json($result);
    }
}
