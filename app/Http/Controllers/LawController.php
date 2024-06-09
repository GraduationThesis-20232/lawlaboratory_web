<?php

namespace App\Http\Controllers;

use App\Models\Law;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use MongoDB\BSON\ObjectId;

class LawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attribute = ['name', 'identifier', 'ministries', 'issued_date', 'effective_date', 'effect_status'];

        $laws = DB::connection('mongodb')->table('laws')->get($attribute);
        $codes = DB::connection('mongodb')->table('codes')->get($attribute);
        $constitutions = DB::connection('mongodb')->table('constitution')->get($attribute);

        return Inertia::render('Law/Index', [
            'laws' => $laws,
            'codes' => $codes,
            'constitutions' => $constitutions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($identifier)
    {
        $fileDocx = config('app.path_to_storage') . 'docx\\' . $identifier . '.docx';
        $filePdf = config('app.path_to_storage') . 'pdf\\' . $identifier . '.pdf';

        $identifier = str_replace('_', '/', $identifier);

        $law = DB::connection('mongodb')->table('laws')->where('identifier', '=', $identifier)->first();

        if (isset($law)) {
            $data = $law;
        } else {
            $code = DB::connection('mongodb')->table('codes')->where('identifier', '=', $identifier)->first();
            if (isset($code)) {
                $data = $code;
            } else {
                $data = DB::connection('mongodb')->table('constitution')->where('identifier', '=', $identifier)->first();
            }
        }

        if(file_exists($fileDocx)) {
            $data['docx'] = $fileDocx;
        } else $data['docx'] = null;

        if (file_exists($filePdf)) {
            $data['pdf'] = $filePdf;
        } else $data['pdf'] = null;

        return Inertia::render('Law/Show', [
           'law' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $identifier)
    {
        $identifier = str_replace('_', '/', $identifier);
        $lines = explode("\n", $request->get('content'));

        $table = '';
        if (DB::connection('mongodb')->table('laws')->where('identifier', '=', $identifier)->exists()) {
            $table = 'laws';
        } else if (DB::connection('mongodb')->table('codes')->where('identifier', '=', $identifier)->exists()) {
            $table = 'codes';
        } else {
            $table = 'constitution';
        }

        $law = DB::connection('mongodb')->table($table)->where('identifier', '=', $identifier)->first();

        $lawId = new ObjectId($law['_id']);

        if (isset($law['chapters'])) {
            $keyChapters = explode('-', $request->get('key'));
            $chapter_index = $keyChapters[1];
            $article_index = $keyChapters[2];

            if (isset($law['chapters'][$chapter_index]['articles'][$article_index])) {
                $article = $law['chapters'][$chapter_index]['articles'][$article_index];
                $article['name'] = $request->get('title');
                $article['content'] = $lines;

                $law['chapters'][$chapter_index]['articles'][$article_index] = $article;

                DB::connection('mongodb')
                    ->table($table)
                    ->where('_id', $lawId)
                    ->update([
                        '$set' => [
                            'chapters' => $law['chapters']
                        ]
                    ]);
            }

        } else if (isset($law['articles'])) {
            $keyChapters = explode('-', $request->get('key'));
            $article_index = $keyChapters[1];

            if (isset($law['articles'][$article_index])) {
                $article = $law['articles'][$article_index];
                $article['name'] = $request->get('title');
                $article['content'] = $lines;

                $law['articles'][$article_index] = $article;

                DB::connection('mongodb')
                    ->table($table)
                    ->where('_id', $lawId)
                    ->update([
                        '$set' => [
                            'articles' => $law['articles']
                        ]
                    ]);
            }
        } else if(isset($law['parts'])) {
            $keyPart = explode('-', $request->get('key'));
            $part_index = $keyPart[1];
            $chapter_index = $keyPart[2];
            $article_index = $keyPart[3];

            if (isset($law['parts'][$part_index]['chapters'][$chapter_index]['articles'][$article_index])) {
                $article = $law['parts'][$part_index]['chapters'][$chapter_index]['articles'][$article_index];
                $article['name'] = $request->get('title');
                $article['content'] = $lines;

                $law['parts'][$part_index]['chapters'][$chapter_index]['articles'][$article_index] = $article;

                DB::connection('mongodb')
                    ->table($table)
                    ->where('_id', $lawId)
                    ->update([
                        '$set' => [
                            'parts' => $law['parts']
                        ]
                    ]);
            }
        }

        return Inertia::location(route('laws.show', ['identifier' => str_replace('/', '_', $identifier)]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
