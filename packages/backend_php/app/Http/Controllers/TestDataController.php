<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Laravel\Lumen\Routing\Controller as BaseController;

class TestDataController extends BaseController
{
    use Helpers;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function test()
    {
        $data = [
            'token_type' => 'bearer',
        ];

        return response()->json($data);
    }
}
