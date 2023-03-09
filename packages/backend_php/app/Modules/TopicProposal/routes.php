<?php

$api->group([
    'prefix' => 'topic-proposal',
    'namespace' => 'App\Modules\TopicProposal\Controllers'
], function ($api) {

    $api->get('/', [
        'as' => '',
        'uses' => 'TopicProposalController@view',
    ]);

    $api->post('/', [
        'as' => '',
        'uses' => 'TopicProposalController@store',
    ]);

    $api->put('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'TopicProposalController@update',
    ]);

    $api->delete('/{id:[0-9]+}', [
        'as' => '',
        'uses' => 'TopicProposalController@delete',
    ]);
});
