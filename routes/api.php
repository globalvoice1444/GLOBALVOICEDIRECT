<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Global Voice Direct inbound fax (Resend email.received webhook; Svix-signed, no session/CSRF)
Route::post('/inbound/fax', 'FaxController@inbound')->middleware('throttle:120,1');
// 2026-11-02 cec: Resend bounce/complaint webhook for customer email campaigns (inactive until EC_WEBHOOK_SECRET set)
Route::post('/inbound/email-events', 'EcPublicController@webhook')->middleware('throttle:300,1');
