<?php

use App\Http\Controllers\Api\AnalyzerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/accessibility-analyze', [AnalyzerController::class, 'analyze'])->name('api.accessibility.analyze');
