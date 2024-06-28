<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::controller(CrawlController::class)->group(function () {
    Route::get('/crawl/documents/start', 'crawlDocumentsStart')->name('crawl.documents.start');
    Route::get('/crawl/documents/in_progress', 'crawlDocumentsInProgress')->name('crawl.documents.in_progress');
    Route::get('/crawl/documents/done', 'crawlDocumentsDone')->name('crawl.documents.done');

    Route::get('/crawl/questions/start', 'crawlQuestionsStart')->name('crawl.questions.start');
    Route::get('/crawl/questions/in_progress', 'crawlQuestionsInProgress')->name('crawl.questions.in_progress');
    Route::get('/crawl/questions/done', 'crawlQuestionsDone')->name('crawl.questions.done');
    Route::get('/import/questions', 'importQuestionsToDatabase')->name('crawl.questions.import');
});
