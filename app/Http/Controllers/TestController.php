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
        return view('admin.tests', compact('tests', 'groups', 'status'));
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

        $test->load('questions.category', 'group.competition');
        $competitionId = $test->group->competition->id;

        $allQuestions = Question::where('category_id', '!=', 1)->whereDoesntHave('tests.group.competition', function ($query) use ($competitionId) {
            $query->where('id', $competitionId);
        })->get();

        $categories = Category::where('id', '!=', 1)->get();
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


    public function addQuestionsAuto(Request $request)
    {
        $maxRepeats = $request->max_repeats;
        $numberOfQuestions = $request->number_of_questions;
        $testId = $request->test_id;

        $test = Test::with('group.competition')->findOrFail($testId);
        $competitionId = $test->group->competition->id;

        $existingQuestionIds = QuestionTest::whereHas('test.group.competition', function ($query) use ($competitionId) {
            $query->where('id', $competitionId);
        })->pluck('question_id')->toArray();

        $questions = Question::inRandomOrder()
            ->where('category_id', '!=', 1)
            ->where('repeated', '<=', $maxRepeats)
            ->whereNotIn('id', $existingQuestionIds)
            ->limit($numberOfQuestions)
            ->get();

        foreach ($questions as $question) {
            QuestionTest::create([
                'test_id' => $testId,
                'question_id' => $question->id,
            ]);
        }

        return redirect()->route('tests.questions', ['test' => $testId]);
    }


    public function addQuestionsByCategories(Request $request)
    {
        $test_id = $request->test_id;
        $categories = $request->categories;
        $number_of_questions = $request->number_of_questions;
        $max_repeats = $request->max_repeats;

        $test = Test::with('group.competition')->findOrFail($test_id);
        $competitionId = $test->group->competition->id;

        // Loop through each category
        for ($i = 0; $i < count($categories); $i++) {
            $category_id = $categories[$i];
            $questionsInCategory = Question::where('category_id', $category_id)
                ->where('repeated', '<=', $max_repeats[$i])
                ->pluck('id');

            $existingQuestionIds = QuestionTest::whereHas('test.group.competition', function ($query) use ($competitionId) {
                $query->where('id', $competitionId);
            })->pluck('question_id')->toArray();

            // Filter questions not in the test, not repeated, and below max_repeats
            $availableQuestions = $questionsInCategory->diff($existingQuestionIds);

            // Ensure the number of questions requested is not more than available questions in the category
            $numberToAdd = min($number_of_questions[$i], count($availableQuestions));

            // Add questions to the test
            for ($j = 0; $j < $numberToAdd; $j++) {
                $question_id = $availableQuestions->random();

                QuestionTest::create([
                    'test_id' => $test_id,
                    'question_id' => $question_id,
                ]);

                // Remove the added question from the available questions list
                $availableQuestions = $availableQuestions->diff([$question_id]);
            }
        }

        return redirect()->route('tests.questions', ['test' => $test_id]);
    }
}
