<?php

namespace App\Http\Controllers;

use App\Models\CurrentTest;
use App\Models\GroupTeam;
use App\Models\ManualTest;
use App\Models\Question;
use App\Models\QuestionTest;
use App\Models\Team;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Hossam\Licht\Traits\ApiResponse;


class ManualTestController extends Controller
{
    use ApiResponse;
    public function index(Test $test)
    {
        // Load test with groups and questions (where set is 0)
        $test->load(['group', 'questions' => function ($query) {
            $query->wherePivot('set', 0);
        }]);

        // Get the questions associated with the test
        $testQuestions = $test->questions;

        // Get the categories for the questions with name and id
        $categories = $testQuestions->map(function ($question) {
            return [
                'id'   => $question->category->id,
                'name' => $question->category->name,
            ];
        })->unique();

        // Other variables
        $team_id = auth()->id();
        $answerSubmitted = 0;

        return view('admin.current_manual_test', compact('test', 'testQuestions', 'team_id', 'answerSubmitted', 'categories'));
    }



    public function setQuestion(Request $request)
    {
        $testQuestion = QuestionTest::where('test_id', $request->test_id)
            ->where('set', 0)
            ->whereHas('question.category', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            })
            ->first();
        $question_id = $testQuestion->question_id;

        $manualTest = $this->started($request->test_id);
        $startTime = $this->calculateQuestionStartTime($manualTest->test_id, $manualTest);
        $manualTest->update([
            'question_id' => $question_id,
            'question_start_at' => $startTime,
        ]);
        $this->setQuestionAsSet($manualTest->test_id, $question_id);
        return redirect()->route('manual-tests.index', ['test' => $request->test_id]);
    }

    public function setQuestionAsSet($test_id, $question_id)
    {
        $testQuestion = QuestionTest::where('test_id', $test_id)->where('question_id', $question_id)->first();
        $testQuestion->update([
            'set' => 1,
        ]);
        $question = Question::find($question_id);
        $question->update([
            'repeated' => $question->repeated + 1,
        ]);
        return;
    }

    public function started($test)
    {
        $manualTest = ManualTest::where('test_id', $test)->first();
        if ($manualTest) {
            return $manualTest;
        }
        $test = Test::find($test);
        $manualTest = ManualTest::create([
            'test_id' => $test->id,
            'group_id' => $test->group_id,
            'question_time' => $test->question_time,
            'answer_time' => $test->answer_time,
        ]);
        return $manualTest;
    }

    public function calculateQuestionStartTime($testId, $manualTest)
    {
        $test = Test::find($testId);
        if ($test) {
            $startTime = max(Carbon::now(), $test->start_time);
            if ($test->status != Test::CURRENT) {
                if (Carbon::now() > $test->start_time) {
                    $test->update([
                        'status' => Test::CURRENT,
                    ]);
                }
            }

            if ($manualTest->question_start_at) {
                $currentQuestionEndTime = Carbon::parse($manualTest->question_start_at)
                    ->addSeconds($manualTest->question_time)
                    ->addSeconds($manualTest->answerTime);
                $startTime = max($startTime, $currentQuestionEndTime);
            }
            return $startTime;
        }
    }


    public function endTest(Test $test)
    {
        $manualTest = ManualTest::where('test_id', $test->id)->first();
        $manualTest->update([
            'question_id' => null
        ]);
        $test->update([
            'status' => Test::PAST,
        ]);
        // $manualTest->delete();
        return redirect()->route('manual-tests.index', ['test' => $test->id]);
    }


    public function getQuestion($test)
    {
        $currentTest = ManualTest::where('test_id', $test)->first();
        $question = Question::find($currentTest->question_id);
        $data = $question;
        if ($currentTest) {
            $data['question_start_at'] = $currentTest->question_start_at;
            $data['question_id'] = $currentTest->question_id;
        }
        return $this->successResponse($data);
    }


    public function correctAnswer($question, $test)
    {
        $question = Question::find($question);
        $data = [
            'name' => $question->name,
            'correct_answer' => $question->correct_answer . ') ' . $question[$question->correct_answer],
        ];

        $TestQuestion = QuestionTest::where('test_id', $test)->where('question_id', $question->id)->first();
        $isAnswered = $TestQuestion->answered;
        if (!$isAnswered) {
            $TestQuestion->update([
                'answered' => 1,
            ]);
        }
        $correctTeamAnswerId = $TestQuestion->team_id;
        $correctTeamAnswer = Team::find($correctTeamAnswerId);
        if ($correctTeamAnswer != null) {
            $corrcorrectTeamAnswer = $correctTeamAnswer->name;
        } else
            $corrcorrectTeamAnswer = 'no one';

        $data['corrcorrectTeamAnswer'] = $corrcorrectTeamAnswer;

        return $this->successResponse($data);
    }


    public function sendAnswer(Request $request, Test $test)
    {
        if (session('lastAnswer') == $request->question_id)
            return;
        session(['lastAnswer' => $request->question_id]);
        $question = Question::find($request->question_id);
        $correctAnswer = $question->correct_answer;
        $isCorrect = $correctAnswer == $request->answer ? 1 : 0;
        if ($isCorrect) {
            $TestQuestion = QuestionTest::where('Test_id', $test->id)->where('question_id', $request->question_id)->first();
            $isAnswered = $TestQuestion->answered;
            if (!$isAnswered) {
                $TestQuestion->update([
                    'answered' => 1,
                    'team_id' => $request->team_id,
                ]);
                $group_id = $test->group_id;
                $groupTeam = GroupTeam::where('group_id', $group_id)->whereTeamId($request->team_id)->first();
                $groupTeam->update([
                    'points' => $groupTeam->points + 1,
                ]);
            }
        }
        return;
    }
}
