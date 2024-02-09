<?php

namespace App\Http\Controllers;

use App\Models\CurrentAudienceQuestion;
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
use Nette\Utils\Random;

class ManualTestController extends Controller
{
    use ApiResponse;
    public $current_server_time;
    public $show_answers_delay;
    public function __construct()
    {
        $this->show_answers_delay = env('show_answers_delay');
        $this->current_server_time = Carbon::now()->setTimezone('Asia/Bahrain');
    }
    public function index(Test $test)
    {
        $test->load(['group', 'questions' => function ($query) {
            $query->wherePivot('set', 0);
        }]);
        $testQuestions = $test->questions;
        $categories = $testQuestions->map(function ($question) {
            return [
                'id'   => $question->category->id,
                'name' => $question->category->name,
            ];
        })->unique();
        $team_id = auth()->id();
        $answerSubmitted = 0;
        $current_time = $this->current_server_time;
        $start_time = Carbon::parse($test->start_time);

        return view('admin.current_manual_test', compact('test', 'testQuestions', 'team_id', 'answerSubmitted', 'categories'));
    }


    public function setQuestion(Request $request)
    {
        $test = Test::find($request->test_id);

        if ($test->start_time > now()) {
            $message = 'test did not start yet';
            return redirect()->route('manual-tests.index', ['test' => $request->test_id])->with('message', $message);
        };

        $manualTest = $this->setTest($test);

        if ($manualTest->question_start_at) {
            $currentQuestionEndTime = Carbon::parse($manualTest->question_start_at)
                ->addSeconds($manualTest->question_time)
                ->addSeconds($manualTest->answerTime);
            if ($currentQuestionEndTime > now()) {
                $message = 'there is a question running now';
                return redirect()->route('manual-tests.index', ['test' => $request->test_id])->with('message', $message);
            };
        }
        $testQuestion = QuestionTest::where('test_id', $request->test_id)
            ->where('set', 0)
            ->whereHas('question.category', function ($query) use ($request) {
                $query->where('id', $request->category_id);
            })
            ->first();
        $question_id = $testQuestion->question_id;

        $manualTest->update([
            'question_id' => $question_id,
            'question_start_at' => now(),
        ]);
        $this->setQuestionAsSet($manualTest->test_id, $question_id);
        return redirect()->route('manual-tests.index', ['test' => $request->test_id]);
    }


    public function setTest($test)
    {
        if ($test->status != Test::CURRENT) {
            $test->update([
                'status' => Test::CURRENT,
            ]);
        }
        $manualTest = ManualTest::where('test_id', $test->id)->first();
        if ($manualTest) {
            return $manualTest;
        }
        $manualTest = ManualTest::create([
            'test_id' => $test->id,
            'group_id' => $test->group_id,
            'question_time' => $test->question_time,
            'answer_time' => $test->answer_time,
        ]);
        return $manualTest;
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




    public function endTest(Test $test)
    {
        $manualTest = ManualTest::where('test_id', $test->id)->first();
        if ($manualTest) {
            $manualTest->update([
                'question_id' => null
            ]);
        }
        $test->update([
            'status' => Test::PAST,
        ]);
        return redirect()->route('manual-tests.index', ['test' => $test->id]);
    }


    public function getQuestion($test)
    {
        $currentTest = ManualTest::where('test_id', $test)->first();
        if (!$currentTest) {
            return $this->successResponse(null);
        }
        $question = Question::find($currentTest->question_id);
        $data = $question;
        $question->load('category');
        if ($currentTest) {
            $data['question_start_at'] = $currentTest->question_start_at;
            $data['question_id'] = $currentTest->question_id;
            $data['show_answers_delay'] = $this->show_answers_delay;
            $data['category'] = $question->category->name;
            $data['server_time'] = Carbon::now()->setTimezone('Asia/Bahrain')->toDateTimeString();
        }
        return $this->successResponse($data);
    }


    public function correctAnswer($question, $test)
    {
        $question = Question::find($question);
        $data = [
            'name' => $question->name,
            'correct_answer' =>$question[$question->correct_answer],
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
        $correctAnswer = strtolower($correctAnswer);
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


    public function mainScreen(Test $test)
    {
        $test->load(['group']);
        $team_id = auth()->id();
        $answerSubmitted = 0;
        $current_time = $this->current_server_time;
        $start_time = Carbon::parse($test->start_time);

        if ($current_time >= $start_time) {
            $test_time_remaining_seconds = $start_time->diffInSeconds($current_time) * -1; // Return negative value
        } else {
            $test_time_remaining_seconds = $start_time->diffInSeconds($current_time); // Return positive value
        }

        return view('admin.main_screen', compact('test', 'team_id', 'answerSubmitted', 'test_time_remaining_seconds'));
    }


    public function getAudienceQuestions(Test $test)
    {
        $audienceQuestion = CurrentAudienceQuestion::with('question')->where('test_id', $test->id)->first();

        if (!$audienceQuestion) {
            $audienceQuestion = null;
        }
        return $this->successResponse($audienceQuestion);
    }
    public function setRandomAudienceNumber(Request $request, $test)
    {

        if (isset($request->hide)) {
            $number = 0;
        } else {
            $number = random_int(1, $request->max_audiences);
        }
        $audienceQuestion = new CurrentAudienceQuestionController;
        $audienceQuestion = $audienceQuestion->testExist($test);
        $audienceQuestion->update([
            'random_number' => $number
        ]);
        return redirect()->route('manual-tests.index', ['test' => $test]);
    }
}
