<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\TemplateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('web\static\home\home');
})->name("home");

Route::get(
    '/templates/{template}/generate',
    [TemplateController::class, 'generate']
)->name('templates.generate');
Route::get(
    '/templates/generateRandom',
    [TemplateController::class, 'generateRandom']
)->name('templates.generateRandom');

Route::resource('/items', ItemController::class);
Route::resource('/types', TypeController::class);
Route::resource('/templates', TemplateController::class);