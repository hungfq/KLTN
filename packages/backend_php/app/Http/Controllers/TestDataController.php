<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use ElephantIO\Client;
use Laravel\Lumen\Routing\Controller as BaseController;

class TestDataController extends BaseController
{
    use Helpers;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function testSocket()
    {
        $url = 'http://localhost:8002';
        $client = new Client(Client::engine(Client::CLIENT_3X, $url));
        $client->initialize();
        $client->of('/');
        $data = ['username' => 'hung-test'];
        $client->emit('test', $data);
        $client->close();


        $data = [
            'token_type' => 'bearer',
        ];

        return response()->json($data);
    }
}
