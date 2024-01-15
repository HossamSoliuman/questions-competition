<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Http\Resources\TestResource;
use App\Models\Category;
use App\Models\QuestionTest;
use App\Models\Group;
use App\Models\Question;
use Hossam\Licht\Controllers\LichtBaseController;
use Illuminate\Http\Request;

class TestController extends LichtBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = [
            0 => 'Comming',
            1 => 'Current',
            2 => 'Ended',
        ];
        $tests = Test::with('group')->orderBy('start_time', 'desc')->paginate(4);
        $groups = Group::all();
        return view('admin.tests', compact('tests', 'groups','status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTestRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTestRequest $request)
    {
        // return $request->validated();
        $test = Test::create($request->validated());
        return redirect()->route('tests.index');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTestRequest  $request
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTestRequest $request, Test $test)
    {
        $test->update($request->validated());
        return redirect()->route('tests.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('tests.index');
    }

    public function showQuestions(Test $test)
    {
        // Get all questions that are not associated with the current test
        $allQuestions = Question::whereDoesntHave('questionTests', function ($query) use ($test) {
            $query->where('test_id', $test->id);
        })->get();
        $categories = Category::all();
        $test->load('questions.category');
        return view('admin.test_questions', compact('allQuestions', 'test', 'categories'));
    }

    public function addQuestions(Request $request)
    {
        foreach ($request->questions_id as $question_id) {
            QuestionTest::create([
                'test_id' => $request->test_id,
                'question_id' => $question_id,
            ]);
        }
        return redirect()->route('tests.questions', ['test' => $request->test_id]);
    }
    public function removeQuestion($QuestionTest)
    {
        $QuestionTest = QuestionTest::find($QuestionTest);
        $QuestionTest->delete();
        return redirect()->route('tests.questions', ['test' => $QuestionTest->test_id]);
    }
    // all questions 23 talib 15 -> 10
    public function addQuestionsAuto(Request $request)
    {
        $number_of_questions = $request->number_of_questions;
        $test_id = $request->test_id;
        $allQuestions = Question::all()->count();
        $existQuestions = QuestionTest::where('test_id', $test_id)->get()->count();
        if ($number_of_questions > ($allQuestions - $existQuestions)) {
            $number_of_questions = $allQuestions - $existQuestions;
        }
        $question_id = $this->getRandomQuestion();
        for ($i = 0; $i < $number_of_questions; $i++) {
            while ($this->checkIfQuestionRepeated($test_id, $question_id)) {
                $question_id = $this->getRandomQuestion();
            }
            QuestionTest::create([
                'test_id' => $test_id,
                'question_id' => $question_id,
            ]);
        }
        return redirect()->route('tests.questions', ['test' => $request->test_id]);
    }
    public function getRandomQuestion()
    {
        $question = Question::inRandomOrder()->first();
        return $question->id;
    }
    public function checkIfQuestionRepeated($test_id, $question_id)
    {
        $exist = QuestionTest::where('test_id', $test_id)->where('question_id', $question_id)->first();
        if ($exist)
            return 1;
        return 0;
    }
    public function addQuestionsByCategories(Request $request)
    {
        $test_id = $request->test_id;
        $categories = $request->categories;
        $number_of_questions = $request->number_of_questions;

        // Loop through each category
        for ($i = 0; $i < count($categories); $i++) {
            $category_id = $categories[$i];
            $questionsInCategory = Question::where('category_id', $category_id)->pluck('id');
            $existQuestions = QuestionTest::where('test_id', $test_id)
                ->whereIn('question_id', function ($query) use ($category_id) {
                    $query->select('id')
                        ->from('questions')
                        ->where('category_id', $category_id);
                })
                ->count();

            // Ensure the number of questions requested is not more than available questions in the category
            if ($number_of_questions[$i] > (count($questionsInCategory) - $existQuestions)) {
                $number_of_questions[$i] = (count($questionsInCategory) - $existQuestions);
            }

            // Add questions to the test
            for ($j = 0; $j < $number_of_questions[$i]; $j++) {
                $question_id = $questionsInCategory->random();

                // Check if the question is repeated in the test
                while ($this->checkIfQuestionRepeated($test_id, $question_id)) {
                    $question_id = $questionsInCategory->random();
                }

                // Add the question to the test
                QuestionTest::create([
                    'test_id' => $test_id,
                    'question_id' => $question_id,
                ]);
            }
        }

        return redirect()->route('tests.questions', ['test' => $test_id]);
    }
}
