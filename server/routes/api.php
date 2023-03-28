<?php

use App\Http\Controllers\Api\CompanyController;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

foreach (File::allFiles(__DIR__ . '/modules') as $partial) {
    require_once $partial->getPathname();
}

Route::get('exchange-rates', function (Request $request) {
    $rates = ExchangeRate::today()->get();
    return response()->json($rates);
})->middleware('checkTime');
