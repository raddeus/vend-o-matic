<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('API')->group(function() {
    Route::put('/', 'MachineController@insertCoins');
    Route::delete('/', 'MachineController@refundCoins');
    Route::get('/inventory', 'MachineController@getInventory');
    Route::put('/inventory/{amount}', 'MachineController@purchaseItem');

    // Utility method for dev
    Route::delete('reset', 'UtilityController@reset');
    Route::get('user', 'UserController@show');
});

