<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ImportQuestions extends Command
{
    protected $signature = 'import:questions';
    protected $description = 'Import questions from CSV file and associate with categories';

    public function handle()
    {
        // Specify the path to your CSV file
        $csvFilePath = public_path('q2.csv');

        // Read the CSV file
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);

        // Get distinct categories from the CSV file
        $distinctCategories = iterator_to_array($csv->fetchColumn('category'));

        // Insert distinct categories into the categories table
        foreach (array_unique($distinctCategories) as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
        }

        // Associate category IDs with questions and insert questions into the questions table
        foreach ($csv as $row) {
            $categoryId = Category::where('name', $row['category'])->value('id');

            // Insert the question with the associated category ID
            Question::create([
                'name'           => $row['question'],
                'a'              => $row['a'],
                'b'              => $row['b'],
                'c'              => $row['c'],
                'd'              => $row['d'],
                'correct_answer' => $row['correct_answer'],
                'category_id'    => $categoryId,
                'repeated'       => 0, // You can adjust this based on your requirements
            ]);
        }

        $this->info('Questions imported successfully.');
    }
}
