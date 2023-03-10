<?php

$api->group([
    'prefix' => 'committee',
    'namespace' => 'App\Modules\Committee\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'CommitteeController@view',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'CommitteeController@store',
    ]);

    $api->put('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'CommitteeController@update',
    ]);

    $api->delete('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'CommitteeController@delete',
    ]);

//    $api->post('/import', [
//        'as' => '',
//        'uses' => 'CommitteeController@import',
//    ]);

});
