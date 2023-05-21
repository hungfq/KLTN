<?php

$api->group([
    'prefix' => 'criteria',
    'namespace' => 'App\Modules\Criteria\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'CriteriaController@view',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'CriteriaController@store',
    ]);

    $api->group(['prefix' => '{id:[0-9]+}'], function ($api) {

        $api->get('/', [
            'as' => '',
            'uses' => 'CriteriaController@show',
        ]);

        $api->put('/', [
            'as' => '',
            'uses' => 'CriteriaController@update',
        ]);
    });

});
