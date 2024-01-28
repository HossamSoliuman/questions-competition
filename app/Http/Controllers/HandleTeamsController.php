<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\CurrentTest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HandleTeamsController extends Controller
{
    public function index()
    {
        $team = auth()->id();

        $currentTests = Test::with('group.teams')->whereHas('group.teams', function ($query) use ($team) {
            $query->where('teams.id', $team);
        })->where('status', Test::CURRENT)->orderBy('start_time', 'desc')->get();

        $pastTests = Test::with('group.teams')->whereHas('group.teams', function ($query) use ($team) {
            $query->where('teams.id', $team);
        })->where('status', Test::PAST)->orderBy('start_time', 'desc')->get();

        $commingTests = Test::with('group.teams')->whereHas('group.teams', function ($query) use ($team) {
            $query->where('teams.id', $team);
        })->where('status', Test::COMMING)->orderBy('start_time', 'desc')->get();
        $team = auth()->user();
        return view('teams.index', compact('pastTests', 'commingTests', 'currentTests', 'team'));
    }
    public function viewTest(Test $test)
    {
        $currentTest = CurrentTest::where('test_id', $test->id)->first();
        if ($currentTest) {
            $test['question_start_at'] = $currentTest->question_start_at;
        }
        $answerSubmitted = 0;
        $team_id = auth()->id();
        $team_name = auth()->user()->name;

        $current_time = Carbon::now();
        $start_time = Carbon::parse($test->start_time);

        if ($current_time >= $start_time) {
            $test_time_remaining_seconds = $start_time->diffInSeconds($current_time) * -1; // Return negative value
        } else {
            $test_time_remaining_seconds = $start_time->diffInSeconds($current_time); // Return positive value
        }
        return view('teams.manual_test', compact('test', 'answerSubmitted', 'team_id','team_name','test_time_remaining_seconds'));
    }
}
