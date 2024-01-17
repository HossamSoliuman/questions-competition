<?php
// App/Jobs/CheckCompetitionsJob.php

namespace App\Jobs;

use App\Models\Competition;
use App\Models\CompetitionQuestion;
use App\Models\CurrentCompetition;
use App\Models\CurrentTest;
use App\Models\Test;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckCompetitionsJob
{
    public function handle()
    {
        $currentTime = Carbon::now()->addMinute();

        // return $currentTime;
        $competitionsToStart = Competition::where('start_time', '<=', $currentTime)
            ->where('status', 0)
            ->get();

        foreach ($competitionsToStart as $competition) {
            CurrentTest::create([
                'competition_id' => $competition->id,
                'question_id' => $this->getQuestionId($competition->id),
                'group_id' => $competition->group_id,
                'question_start_at' => $competition->start_time,
                'question_time' => $competition->question_time,
                'answer_time' => $competition->answer_time,
            ]);

            $competition->update(['status' => Test::CURRENT]);
        }
    }
    public function getQuestionId($competition)
    {
        $question = CompetitionQuestion::where('competition_id', $competition)->where('answered', 0)->first();
        if ($question) {
            return $question->question_id;
        }
        return null;
    }
}
