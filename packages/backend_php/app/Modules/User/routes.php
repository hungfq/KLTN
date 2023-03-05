<?php

$api->group([
    'prefix' => 'user',
    'namespace' => 'App\Modules\User\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'UserController@view',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'UserController@store',
    ]);

    $api->put('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'UserController@update',
    ]);
});
