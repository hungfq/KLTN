<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use ElephantIO\Client;
use Illuminate\Support\Facades\Mail;
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

    public function testApi()
    {
        $data = [
            'internal_id' => 6969,
        ];
        return response()->json($data);
    }

    public function testMail()
    {
        $mail = $this->request->input('mail');
        if ($mail) {
            $data = array('name' => $mail);
            Mail::send('mail', $data, function ($message) use ($mail) {
                $message->to($mail, $mail)->subject('Test Mail from hungpq.click');
            });
        }
    }
}
