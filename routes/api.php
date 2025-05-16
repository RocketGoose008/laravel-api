<?php
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ReceiverController;
use App\Http\Controllers\SellOrdersController;

Route::post('/member/register', [MemberController::class, 'register']);

Route::post('/receiver/create', [ReceiverController::class, 'createReceiver']);
Route::post('/receiver/list', [ReceiverController::class, 'receiverList']);

Route::post('/transactions/list', [TransactionsController::class, 'transactionsList']);

Route::post('/sell_orders/create', [SellOrdersController::class, 'sellOrdersCreate']);
Route::post('/sell_orders/list', [SellOrdersController::class, 'sellOrdersList']);
