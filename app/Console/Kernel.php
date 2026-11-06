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
        // $schedule->command('inspire')->hourly();
        $schedule->command('fax:purge')->dailyAt('03:15')->withoutOverlapping();
        $schedule->command('listings:bing-export')->dailyAt('02:00')->timezone('America/New_York')->withoutOverlapping();
        $schedule->command('security:encrypt-legacy-passwords')->dailyAt('03:30')->withoutOverlapping();
        // 2026-11-02 Drip emails: sends nothing unless DRIP_SENDING=true AND sequence+step ACTIVE
        $schedule->command('drip:run')->everyFiveMinutes()->withoutOverlapping(30);
        // 2026-11-02 Customer email campaigns: sends nothing unless EC_SENDING=true
        $schedule->command('ec:run')->everyFiveMinutes()->withoutOverlapping(30);
        $schedule->command('ec:seq-run')->everyFiveMinutes()->withoutOverlapping(30); // customer sequences (own mailbox)
        // Audio Campaign worker (2026-11-01): only runs when AUDIO_CAMPAIGN_SENDING=true
        if (config('audio_campaign.sending')) $schedule->command('queue:work database --queue=audio-campaigns --stop-when-empty --timeout=900 --tries=1')->everyMinute()->withoutOverlapping(20)->runInBackground();
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
