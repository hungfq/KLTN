<?php

$api->group([
    'prefix' => 'task',
    'namespace' => 'App\Modules\Task\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'TaskController@view',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'TaskController@store',
    ]);

    $api->group(['prefix' => '{id:[0-9]+}'], function ($api) {

        $api->get('/', [
            'as' => '',
            'uses' => 'TaskController@show',
        ]);

        $api->put('/', [
            'as' => '',
            'uses' => 'TaskController@update',
        ]);

        $api->post('/comment', [
            'as' => '',
            'uses' => 'TaskController@addComment',
        ]);

        $api->delete('/comment/{commentId:[0-9]+}', [
            'as' => '',
            'uses' => 'TaskController@deleteComment',
        ]);
    });

});
