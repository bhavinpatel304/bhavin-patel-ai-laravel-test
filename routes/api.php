<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;


Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
Route::get('/tickets/list', [TicketController::class, 'list']);

Route::get('/tickets/{id}', [TicketController::class, 'detail']);
Route::patch('/tickets/{id}', [TicketController::class, 'update']);
Route::patch('/tickets/{id}/update-category', [TicketController::class, 'updateCategory']);
Route::post('/tickets', [TicketController::class, 'create']);


Route::post('/tickets/{id}/classify', [TicketController::class, 'dispatchQueueJob']);

