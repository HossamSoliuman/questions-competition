<?php

namespace App\Http\Controllers;

use App\Models\CompetitionAudienceQuestion;
use App\Models\CurrentAudienceQuestion;
use App\Models\Question;
use App\Models\QuestionTest;
use App\Models\Test;
use Illuminate\Http\Request;

class CurrentAudienceQuestionController extends Controller
{
    public function set(Request $request)
    {
        $currrentQuestion = $this->testExist($request->test_id);
        $question_id = $this->getRandomQuestionId($request->test_id);
        $currrentQuestion->update([
            'question_id' => $question_id,
        ]);
        return redirect()->route('manual-tests.index', ['test' => $request->test_id]);
    }


    public function showQuestion($testId, $show)
    {
        $testQuestion = CurrentAudienceQuestion::where('test_id', $testId)->first();
        $testQuestion->update([
            'show_question' => $show,
        ]);
        return response()->json(['message' => 'Success']);
    }

    public function showAnswer($testId, $show)
    {
        $testQuestion = CurrentAudienceQuestion::where('test_id', $testId)->first();
        $testQuestion->update([
            'show_answer' => $show,
        ]);
        return response()->json(['message' => 'Success']);
    }


    public function testExist($test_id)
    {
        $test = CurrentAudienceQuestion::where('test_id', $test_id)->first();
        if ($test) {
            return $test;
        }
        $test = CurrentAudienceQuestion::create([
            'test_id' => $test_id,
            'show_question' => 0,
            'show_answer' => 0,
        ]);
        return $test;
    }
    public function getRandomQuestionId($test_id)
    {

        $test = Test::findOrFail($test_id);
        $competitionId = $test->group->competition->id;

        $existingQuestionIds = CompetitionAudienceQuestion::where('competition_id', $competitionId)
            ->pluck('question_id')->toArray();

        $randomQuestion = Question::inRandomOrder()
            ->whereNotIn('id', $existingQuestionIds)
            ->where('category_id', 1)
            ->first();

        if (!$randomQuestion)
            return null;
        if ($randomQuestion) {
            CompetitionAudienceQuestion::create([
                'competition_id' => $competitionId,
                'question_id' => $randomQuestion->id,
            ]);
            $randomQuestion->update([
                'repeated' => $randomQuestion->repeated + 1,
            ]);
            return $randomQuestion->id;
        }
        return null;
    }
}
