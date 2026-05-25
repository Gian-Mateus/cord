<?php

use Illuminate\Support\Facades\Route;
use Cord\Pages\TestPage;

Route::middleware(['web'])->group(function () {
    Route::get('/cord-test', TestPage::class)->name('cord.test');
});
