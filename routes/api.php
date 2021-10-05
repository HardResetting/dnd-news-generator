<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

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

//Route::get('/items', [ItemController::class, 'show']);
Route::get('/items', function () {
    $items = DB::table('items')->get();
    $types = DB::table('types')->get();
    return view('items', ['items' => $items, 'types' => $types, 'title' => 'Items']);
});

Route::post('/items', [ItemController::class, 'store']);