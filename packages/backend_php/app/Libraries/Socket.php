<?php

namespace App\Libraries;

use ElephantIO\Client;

class Socket
{
    public static function sendUpdateTaskRequest($ids, $topicId)
    {
        $client = new Client(Client::engine(Client::CLIENT_3X, env('SOCKET_URL', '')));
        $client->initialize();
        $client->of('/');
        foreach ($ids as $id) {
            $client->emit('update-task', [
                'id' => $id,
                'topicId' => $topicId,
            ]);
        }
        $client->close();
    }

    public static function sendUpdateNotificationRequest($ids)
    {
        $client = new Client(Client::engine(Client::CLIENT_3X, env('SOCKET_URL', '')));
        $client->initialize();
        $client->of('/');
        foreach ($ids as $id) {
            $client->emit('update-notify',  [
                'id' => $id,
            ]);
        }
        $client->close();
    }
}