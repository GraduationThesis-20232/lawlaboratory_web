<?php

namespace App\Http\Controllers;

use App\Jobs\CrawlDocuments;
use App\Jobs\CrawlQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CrawlController extends Controller
{
    public function crawlDocumentsStart()
    {
        CrawlDocuments::dispatch();

        return response()->json(
            data: [
                'message' => 'Đã bắt đầu thu thâp văn bản luật!',
                'status' => 'STARTED',
            ],
            headers: ['Content-Type' => 'application/json'],
        );
    }

    public function crawlDocumentsInProgress()
    {
        $response = Http::get('http://127.0.0.1:8080/crawl/documents/in_progress');
        return response()->json($response->json());
    }

    public function crawlDocumentsDone()
    {
        $response = Http::get('http://127.0.0.1:8080/crawl/documents/done');
        return response()->json($response->json());
    }

    public function crawlQuestionsStart()
    {
        CrawlQuestions::dispatch();

        return response()->json(
            data: [
                'message' => 'Đã bắt đầu thu thâp bộ câu hỏi!',
                'status' => 'STARTED',
            ],
            headers: ['Content-Type' => 'application/json'],
        );
    }

    public function crawlQuestionsInProgress()
    {
        $response = Http::get('http://127.0.0.1:8080/crawl/questions/in_progress');
        return response()->json($response->json());
    }

    public function crawlQuestionsDone()
    {
        $response = Http::get('http://127.0.0.1:8080/crawl/questions/done');
        return response()->json($response->json());
    }

}
