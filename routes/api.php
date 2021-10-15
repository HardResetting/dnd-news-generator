<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;


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

Route::get(
    '/templates/generate/{id?}',
    [TemplateController::class, 'compile']
)->name('api.templates.generate');

Route::get(
    '/templates/show/{id?}',
    [TemplateController::class, 'show']
)->name('api.templates.show');