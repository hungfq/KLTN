<?php

$api->group([
    'prefix' => 'topic',
    'namespace' => 'App\Modules\Topic\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'TopicController@view',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'TopicController@store',
    ]);

    $api->get('/{id:[0-9]+}/student', [
        'as' => '',
        'uses' => 'TopicController@viewStudent',
    ]);

    $api->put('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'TopicController@update',
    ]);

    $api->delete('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'TopicController@delete',
    ]);

    $api->post('/import', [
        'as' => '',
        'uses' => 'TopicController@import',
    ]);

});
