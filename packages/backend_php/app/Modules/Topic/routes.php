<?php

$api->group([
    'prefix' => 'topic',
    'namespace' => 'App\Modules\Topic\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'TopicController@view',
    ]);

    $api->get('/student/result', [
        'as' => '',
        'uses' => 'TopicController@studentViewResult',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'TopicController@store',
    ]);

    $api->group(['prefix' => '{id:[0-9]+}'], function ($api) {

        $api->get('/', [
            'as' => '',
            'uses' => 'TopicController@show',
        ]);

        $api->get('/students', [
            'as' => '',
            'uses' => 'TopicController@viewStudent',
        ]);

        $api->put('/', [
            'as' => '',
            'uses' => 'TopicController@update',
        ]);

        $api->delete('/', [
            'as' => '',
            'uses' => 'TopicController@delete',
        ]);

        $api->post('/register', [
            'as' => '',
            'uses' => 'TopicController@studentRegister',
        ]);

        $api->delete('/register', [
            'as' => '',
            'uses' => 'TopicController@studentUnRegister',
        ]);

        $api->post('/lecturer/approve', [
            'as' => '',
            'uses' => 'TopicController@lecturerApprove',
        ]);

        $api->post('/critical/approve', [
            'as' => '',
            'uses' => 'TopicController@criticalApprove',
        ]);
    });

    $api->post('/import', [
        'as' => '',
        'uses' => 'TopicController@import',
    ]);

});
