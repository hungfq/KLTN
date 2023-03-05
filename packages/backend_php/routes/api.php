<?php

/** @var  $api noinspection Php * */

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->group([
        'prefix' => 'auth',
        'middleware' => ['logApi'],
        'namespace' => 'App\Modules\Auth\Controllers'
    ], function ($api) {
        $api->get('/test', function () {
            return ['data' => 'API OK!'];
        });

        $api->post('/login', [
            'as' => '',
            'uses' => 'AuthController@loginWithGoogleAccessToken',
        ]);

        $api->get('/{locationTypeId:[0-9]+}', [
            'as' => 'locationType.view',
            'uses' => 'LocationTypeController@show',
        ]);
    });

    $api->group([
        'prefix' => 'v1',
        'middleware' => ['auth', 'logApi']
    ], function ($api) {
        $api->get('/', function ($api) {
            return ['data' => 'Token OK!'];
        });

        $modules = [
            'User',
//            'Documents',
//            'Warehouse',
//            'Language',
//            'Customer',
//            'LocationTypes',
//            'ZoneTypes',
//            'Statuses',
//            'Location',
//            'Zone',
//            'Items',
//            'SpcHdl',
//            'Pallets',
        ];

        foreach ($modules as $module) {
            require_once base_path("app/Modules/{$module}/routes.php");
        }
    });
});
