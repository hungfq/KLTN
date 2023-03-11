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

    $api->get('/{id:[0-9]+}/student', [
        'as' => '',
        'uses' => 'ScheduleController@viewStudent',
    ]);

    $api->put('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'ScheduleController@update',
    ]);

    $api->delete('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'ScheduleController@delete',
    ]);

    $api->post('/import', [
        'as' => '',
        'uses' => 'ScheduleController@import',
    ]);

});
