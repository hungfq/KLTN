<?php

$api->group([
    'prefix' => 'schedule',
    'namespace' => 'App\Modules\Schedule\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'ScheduleController@view',
    ]);

    $api->get('/with-topic', [
        'as' => '',
        'uses' => 'ScheduleController@viewWithTopic',
    ]);

    $api->get('/student/today', [
        'as' => '',
        'uses' => 'ScheduleController@studentViewToday',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'ScheduleController@store',
    ]);


    $api->post('/import', [
        'as' => '',
        'uses' => 'ScheduleController@import',
    ]);

    $api->group(['prefix' => '{id:[0-9]+}'], function () use ($api) {
        $api->get('/', [
            'as' => '',
            'uses' => 'ScheduleController@show',
        ]);

        $api->put('/', [
            'as' => '',
            'uses' => 'ScheduleController@update',
        ]);

        $api->put('/student', [
            'as' => '',
            'uses' => 'ScheduleController@updateStudent',
        ]);

        $api->put('/score-rate', [
            'as' => '',
            'uses' => 'ScheduleController@updateScoreRate',
        ]);

        $api->post('/student/import', [
            'as' => '',
            'uses' => 'ScheduleController@importStudent',
        ]);

        $api->delete('/', [
            'as' => '',
            'uses' => 'ScheduleController@delete',
        ]);

        $api->get('/student', [
            'as' => '',
            'uses' => 'ScheduleController@viewStudent',
        ]);

        $api->get('/criteria', [
            'as' => '',
            'uses' => 'ScheduleController@viewCriteria',
        ]);

        $api->put('/criteria', [
            'as' => '',
            'uses' => 'ScheduleController@syncCriteria',
        ]);

        $api->get('/topic/export', [
            'as' => '',
            'uses' => 'ScheduleController@exportTopic',
        ]);
    });

});
