<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\CurrentTest;
use Illuminate\Http\Request;

class HandleTeamsController extends Controller
{
    public function index()
    {
        $team = auth()->id();
        $pastTests = Test::with('group.teams')->whereHas('group.teams', function ($query) use ($team) {
            $query->where('teams.id', $team);
        })->where('status', Test::PAST)->orderBy('start_time','desc')->get();
        $commingTests = Test::with('group.teams')->whereHas('group.teams', function ($query) use ($team) {
            $query->where('teams.id', $team);
        })->where('status', Test::COMMING)->orderBy('start_time','desc')->get();

        return view('teams.index', compact('pastTests', 'commingTests'));
    }
    public function viewTest(Test $test)
    {
        $currentTest = CurrentTest::where('test_id', $test->id)->first();
        if ($currentTest) {
            $test['question_start_at'] = $currentTest->question_start_at;
        }
        $answerSubmitted = 0;
        $team_id = auth()->id();
        return view('teams.manual_test', compact('test', 'answerSubmitted', 'team_id'));
    }
}
