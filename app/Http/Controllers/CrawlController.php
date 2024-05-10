<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrawlController extends Controller
{
    public function crawlDataLaws()
    {
        $response = \Illuminate\Support\Facades\Http::post('http://127.0.0.1:8080/crawl/laws');
        return redirect()->back();
    }

    public function crawlDataQuestions()
    {
        $response = \Illuminate\Support\Facades\Http::post('http://127.0.0.1:8080/crawl/questions');
        dd($response->body());
        return redirect()->back();
    }
}
