<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\CurrentTest;
use App\Models\GroupTeam;
use App\Models\Question;
use App\Models\QuestionTest;
use App\Models\Team;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Hossam\Licht\Traits\ApiResponse;

class CurrentTestController extends Controller
{
    use ApiResponse;
    public function getQuestion($test)
    {
        $currentTest = CurrentTest::where('test_id', $test)->first();
        $question = Question::find($currentTest->question_id);
        $data = $question;
        if ($currentTest) {
            $data['question_start_at'] = $currentTest->question_start_at;
            $data['question_id'] = $currentTest->question_id;
        }
        // return $data;
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
