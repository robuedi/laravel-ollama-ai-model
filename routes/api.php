<?php

use App\Http\Controllers\Api\v1\OllamaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('ollama/tags', [OllamaController::class, 'indexTags']);
    Route::post('ollama/show-model', [OllamaController::class, 'showModel']);
    Route::post('ollama/pull-model', [OllamaController::class, 'pullModel']);
    Route::post('ollama/create-model', [OllamaController::class, 'createModel']);
    Route::delete('ollama/delete-model', [OllamaController::class, 'deleteModel']);
    Route::post('ollama/generate', [OllamaController::class, 'generateText']);
    Route::get('ollama/models', [OllamaController::class, 'indexModels']);
});
