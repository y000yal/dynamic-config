<?php


use Illuminate\Support\Facades\Route;

$this->app->router->group(['prefix' => 'admin/setting/v1/dynamic-config'], function (){
    $this->app->router->group(['middleware' => ['auth:admin']], function (){
        $this->app->router->post('/', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigController@store']);
        $this->app->router->get('/', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigController@index'
        ]);
        $this->app->router->get('/{id:[0-9]+}', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigController@show'
        ]);
        $this->app->router->delete('/{id:[0-9]+}', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigController@delete'
        ]);
        $this->app->router->patch('/{id:[0-9]+}', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigController@update'
        ]);
    });
});

$this->app->router->group(['prefix' => 'admin/setting/v1/dynamic-config-details'], function ()  {
    $this->app->router->group(['middleware' => ['auth:admin']], function ()  {
        $this->app->router->post('/', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigDetailController@store']);
        $this->app->router->get('/', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigDetailController@index'
        ]);
        $this->app->router->get('/{id:[0-9]+}', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigDetailController@show'
        ]);
        $this->app->router->delete('/{id:[0-9]+}', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigDetailController@delete'
        ]);
        $this->app->router->patch('/{id:[0-9]+}', [
                'uses' => 'GeniussystemsNp\DynamicConfig\Http\Controllers\DynamicConfigDetailController@update'
        ]);
    });
});