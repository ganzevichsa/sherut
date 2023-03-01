<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('scraping:aminadav')->weekly();
         $schedule->command('scraping:batami')->weekly();
         $schedule->command('scraping:hiburhadash')->weekly();
         $schedule->command('scraping:midrashot')->weekly();
         $schedule->command('scraping:ofekmashu')->weekly();
         $schedule->command('scraping:shel:org')->weekly();
         $schedule->command('scraping:sherutleumi')->weekly();
         $schedule->command('scraping:shlomit')->weekly();
         $schedule->command('clear:job:images')->weekly();
         $schedule->command('calculate:jobs:positions')->everyFiveMinutes();
         $schedule->command('clear:duplicate:jobs')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
