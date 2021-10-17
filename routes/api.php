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
    '/templates/{id}/compile',
    [TemplateController::class, 'compile']
)->name('api.templates.compile');

Route::get(
    '/templates/compileRandom',
    [TemplateController::class, 'compile']
)->name('api.templates.compileRandom');

Route::put(
    '/templates/{template}',
    [TemplateController::class, 'update']
)->name('api.templates.update');

Route::get(
    '/templates/{id}/show',
    [TemplateController::class, 'show']
)->name('api.templates.show');