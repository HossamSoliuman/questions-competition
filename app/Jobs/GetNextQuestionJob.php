<?php

namespace App\Jobs;

use App\Models\CompetitionQuestion;
use App\Models\CurrentCompetition;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetNextQuestionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentCompetitions = CurrentCompetition::all();
        foreach ($currentCompetitions as $currentCompetition) {
            if (!$currentCompetition->question_id) {
                continue;
            }
            if ($currentCompetition->question_start_at <= now()->subSeconds($currentCompetition->question_time)) {
                $this->setQuestionAnswered($currentCompetition->competition_id, $currentCompetition->question_id);
                $currentCompetition->update([
                    'question_id' => $this->getQuestionId($currentCompetition->competition_id),
                    'question_start_at' => Carbon::parse($currentCompetition->question_start_at)->addSeconds($currentCompetition->question_time + $currentCompetition->answer_time),
                ]);
            }
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
    public function setQuestionAnswered($competition, $question)
    {
        if ($question) {
            $question = CompetitionQuestion::where('competition_id', $competition)->where('question_id', $question)->first();
            $question->update([
                'answered' => 1
            ]);
        }
    }
}
