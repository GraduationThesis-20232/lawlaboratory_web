<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function download($filename)
    {
        if (strpos($filename, 'pdf')) {
            $filePath = config('app.path_to_storage') . 'pdf\\' . $filename;
        } elseif (strpos($filename, 'docx')) {
            $filePath = config('app.path_to_storage') . 'docx\\' . $filename;
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath, $filename);
    }

    public function show($filename)
    {
        if (strpos($filename, 'pdf')) {
            $filePath = config('app.path_to_storage') . 'pdf\\' . $filename;
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->file($filePath);
    }
}
