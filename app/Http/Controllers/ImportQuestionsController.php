<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Question;
use League\Csv\Reader;
use Maatwebsite\Excel\Facades\Excel;

class ImportQuestionsController extends Controller
{
    public function showUploadForm()
    {
        return view('admin.upload_questions');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx'
        ]);

        $file = $request->file('file');

        // Determine file type
        $fileType = $file->getClientOriginalExtension();

        // Store the uploaded file
        $filePath = $file->storeAs('uploads', $file->getClientOriginalName());

        // Read the file based on its type
        if ($fileType === 'csv') {
            $csv = Reader::createFromPath(storage_path('app/' . $filePath), 'r');
            $csv->setHeaderOffset(0);
            $rows = $csv;
        }
        $firstCategory = 'Audience';
        Category::firstOrCreate(['name' => $firstCategory]);

        // Import questions
        foreach ($rows as $row) {
            $categoryId = Category::firstOrCreate(['name' => $row['category']])->id;

            Question::create([
                'name'           => $row['question'],
                'a'              => $row['a'],
                'b'              => $row['b'],
                'c'              => $row['c'],
                'd'              => $row['d'],
                'correct_answer' => $row['correct_answer'],
                'category_id'    => $categoryId,
                'repeated'       => 0,
            ]);
        }

        // Delete the uploaded file
        unlink(storage_path('app/' . $filePath));

        return redirect()->back()->with('success', 'Questions imported successfully.');
    }
}
