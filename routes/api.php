<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Laravue\Faker;
use \App\Laravue\JsonResponse;
use \App\Laravue\Acl;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function() {
      
    Route::post('auth/login', 'AuthController@login');




    Route::group(['middleware' => 'auth:sanctum'], function () {
        // Auth routes
        Route::get('auth/user', 'AuthController@user');
        Route::post('auth/logout', 'AuthController@logout');

        Route::get('/user', function (Request $request) {
            return new UserResource($request->user());
        });

        // Api resource routes
        Route::apiResource('roles', 'RoleController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_USER_MANAGE);
        Route::apiResource('permissions', 'PermissionController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

        // Custom routes
        Route::put('users/{user}', 'UserController@update');
        Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

    });

    Route::post('/upload-bill-of-quantity', 'BillofQuantityController@upload');
    Route::get('/upload-bill-of-quantity', 'BillofQuantityController@upload');
    //materials used routes
    Route::get('/materials-used', 'BillofQuantityController@materialsUsed');
    Route::post('/materials-used-create', 'BillofQuantityController@materialsUsedCreate');
    Route::post('/materials-used-update', 'BillofQuantityController@materialsUsedUpdate');
    Route::post('/materials-used-delete', 'BillofQuantityController@materialsUsedDelete');


    //Work done routes
    Route::get('/work-done', 'BillofQuantityController@workDone');
    Route::post('/work-done-create', 'BillofQuantityController@workDoneCreate');
    Route::post('/work-done-update', 'BillofQuantityController@workDoneUpdate');
    Route::post('/work-done-delete', 'BillofQuantityController@workDoneDelete');
    //labour
    Route::get('/labour', 'BillofQuantityController@labour');
    Route::post('/labour-create', 'BillofQuantityController@labourCreate');
    Route::post('/labour-update', 'BillofQuantityController@labourUpdate');
    Route::post('/labour-delete', 'BillofQuantityController@labourDelete');

    Route::get('/fetch-quantity-work-done', 'BillofQuantityController@fetchQuantityWorkDone');

    //equipment
    Route::get('/equipment', 'BillofQuantityController@equipment');
    Route::post('/equipment-create', 'BillofQuantityController@equipmentCreate');
    Route::post('/equipment-update', 'BillofQuantityController@equipmentUpdate');
    Route::post('/equipment-delete', 'BillofQuantityController@equipmentDelete');

    Route::get('/spreadsheet-list', 'BillofQuantityController@spreadsheetList');    
    Route::get('/spreadsheet-level', 'BillofQuantityController@spreadsheetLevel');
    Route::get('/spreadsheet-level-one', 'BillofQuantityController@spreadsheetLevelOne');
    Route::get('/spreadsheet-cells', 'BillofQuantityController@spreadsheetCells');

    //Route::get('/fetch-work', 'BillofQuantityController@fetchWork');
    Route::get('/generate-work-excel', 'WorkController@generateWorkExcel');
    Route::get('/work-summary-pdf', 'PDFController@workSummaryPDF');

    //create project
    Route::get('/projects', 'ProjectManagementController@projects');
    Route::post('/create-project', 'ProjectManagementController@createProject');

    //gantt
    Route::get('/fetch-projects-gantt', 'ProjectManagementController@fetchProjectGantt');
    Route::post('/create-task', 'ProjectManagementController@createTask');
    Route::post('/create-building', 'ProjectManagementController@createBuilding');
    Route::get('/buildings', 'ProjectManagementController@buildings');

    Route::post('/update-bq-totals', 'BillofQuantityController@updateBqTotals');
});

// Fake APIs
Route::get('/table/list', function () {
    $rowsNumber = mt_rand(20, 30);
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'author' => Faker::randomString(mt_rand(5, 10)),
            'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
            'id' => mt_rand(100000, 100000000),
            'pageviews' => mt_rand(100, 10000),
            'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
            'title' => Faker::randomString(mt_rand(20, 50)),
        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data]));
});

Route::get('/orders', function () {
    $rowsNumber = 8;
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'order_no' => 'LARAVUE' . mt_rand(1000000, 9999999),
            'price' => mt_rand(10000, 999999),
            'status' => Faker::randomInArray(['success', 'pending']),
        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data]));
});



Route::get('/articles', function () {
    $rowsNumber = 10;
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'id' => mt_rand(100, 10000),
            'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
            'title' => "",//Faker::randomString(mt_rand(20, 50)),
            'author' => "",//Faker::randomString(mt_rand(5, 10)),
            'comment_disabled' => "",//Faker::randomBoolean(),
            'content' => "",//Faker::randomString(mt_rand(100, 300)),
            'content_short' => "",//Faker::randomString(mt_rand(30, 50)),
            'status' => "",//Faker::randomInArray(['deleted', 'published', 'draft']),
            'forecast' => "",//mt_rand(100, 9999) / 100,
            'image_uri' => "",//'https://via.placeholder.com/400x300',
            'importance' => "",//mt_rand(1, 3),
            'pageviews' => "",//mt_rand(10000, 999999),
            'reviewer' => "",//Faker::randomString(mt_rand(5, 10)),
            'timestamp' => Faker::randomDateTime()->getTimestamp(),
            'type' => "",//Faker::randomInArray(['US', 'VI', 'JA']),

        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data, 'total' => mt_rand(1000, 10000)]));
});

Route::get('articles/{id}', function ($id) {
    $article = [
        'id' => $id,
        'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
        'title' => Faker::randomString(mt_rand(20, 50)),
        'author' => Faker::randomString(mt_rand(5, 10)),
        'comment_disabled' => Faker::randomBoolean(),
        'content' => Faker::randomString(mt_rand(100, 300)),
        'content_short' => Faker::randomString(mt_rand(30, 50)),
        'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
        'forecast' => mt_rand(100, 9999) / 100,
        'image_uri' => 'https://via.placeholder.com/400x300',
        'importance' => mt_rand(1, 3),
        'pageviews' => mt_rand(10000, 999999),
        'reviewer' => Faker::randomString(mt_rand(5, 10)),
        'timestamp' => Faker::randomDateTime()->getTimestamp(),
        'type' => Faker::randomInArray(['US', 'VI', 'JA']),

    ];

    return response()->json(new JsonResponse($article));
});

Route::get('articles/{id}/pageviews', function ($id) {
    $pageviews = [
        'PC' => mt_rand(10000, 999999),
        'Mobile' => mt_rand(10000, 999999),
        'iOS' => mt_rand(10000, 999999),
        'android' => mt_rand(10000, 999999),
    ];
    $data = [];
    foreach ($pageviews as $device => $pageview) {
        $data[] = [
            'key' => $device,
            'pv' => $pageview,
        ];
    }

    return response()->json(new JsonResponse(['pvData' => $data]));
});
