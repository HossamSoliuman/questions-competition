<?php

namespace App\Console;

use App\Jobs\CheckCompetitionsJob;
use App\Jobs\EndedCompetitionsJob;
use App\Jobs\GetNextQuestionJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // App/Console/Kernel.php

    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new CheckCompetitionsJob)->everyMinute();
        $schedule->job(new GetNextQuestionJob)->everyTwoSeconds();
        $schedule->job(new EndedCompetitionsJob)->everyFiveSeconds();
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
