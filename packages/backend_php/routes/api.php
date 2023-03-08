<?php

/** @var  $api noinspection Php * */

$api = app('Dingo\Api\Routing\Router');

$api->version('v2', function ($api) {

    $api->group([
        'prefix' => 'v2',
        'middleware' => ['logApi'],
        'namespace' => 'App\Modules'
    ], function ($api) {
        $api->get('/test', function () {
            return ['data' => 'API OK!'];
        });

        $api->post('/auth/login', [
            'as' => '',
            'uses' => 'Auth\Controllers\AuthController@loginWithGoogleAccessToken',
        ]);

        $api->get('/template', [
            'as' => '',
            'uses' => 'Template\Controllers\TemplateController@download',
        ]);
    });

    $api->group([
        'prefix' => 'v2',
        'middleware' => ['auth', 'logApi']
    ], function ($api) {
        $api->get('/', function ($api) {
            return ['data' => 'Token OK!'];
        });

        $modules = [
            'User',
            'Schedule',
            'Topic',
            'Committee',
            'Notification',
        ];

        foreach ($modules as $module) {
            require_once base_path("app/Modules/{$module}/routes.php");
        }
    });
});
