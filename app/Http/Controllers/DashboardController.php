<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $countLaws = DB::connection('mongodb')->table('laws')->count();
        $countCodes = DB::connection('mongodb')->table('codes')->count();
        $countConstitutions = DB::connection('mongodb')->table('constitution')->count();
        $countLawsCorpus = [
            ['name' => 'Hiến pháp', 'count' => $countConstitutions],
            ['name' => 'Bộ luật', 'count' => $countCodes],
            ['name' => 'Luật', 'count' => $countLaws],
        ];

        $countQuestions = DB::connection('mongodb')->collection('questions')
            ->raw(function ($collection) {
                return $collection->aggregate([
                    [
                        '$group' => [
                            '_id' => '$field',
                            'count' => ['$sum' => 1]
                        ]
                    ]
                ]);
            });

        $countQuestionsCorpus = [];
        foreach ($countQuestions as $question) {
            $countQuestionsCorpus[] = [
                'field' => $question->_id,
                'count' => $question->count
            ];
        }

        $nearestDate = DB::connection('mongodb')->table('questions')
            ->where('date_answer', '<=', now()->toDateString())
            ->orderBy('date_answer', 'desc')
            ->first();

        return Inertia::render('Dashboard', [
            'countLawsCorpus' => $countLawsCorpus,
            'countQuestionsCorpus' => $countQuestionsCorpus,
            'nearestDateAnswer' => $nearestDate['date_answer'],
        ]);
    }
}
