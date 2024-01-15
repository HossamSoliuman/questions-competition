<?php

namespace App\Jobs;

use App\Models\Competition;
use App\Models\CurrentCompetition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EndedCompetitionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $currentCompetitions = CurrentCompetition::where('question_id', null)->get();
        foreach ($currentCompetitions as $currentCompetition) {
            $competition = Competition::find($currentCompetition->competition_id);
            $competition->update([
                'status' => Competition::PAST,
            ]);
        }
        // foreach ($currentCompetitions as $currentCompetition) {
        //     $currentCompetition->delete();
        // }
    }
}
