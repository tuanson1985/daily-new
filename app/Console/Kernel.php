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
	    'App\Console\Commands\DefragmentSan',
        'App\Console\Commands\Defragment',
        'App\Console\Commands\AutoSyncServer',
        'App\Console\Commands\DeleteImageNickCompleteS3',
        'App\Console\Commands\CheckBalanceAllUser',
	    'App\Console\Commands\ReportBalance',
	    'App\Console\Commands\ExpiredTimeDomain',
        'App\Console\Commands\CompleteManualServiceOrders',
        'App\Console\Commands\CheckToolRoblox',
        'App\Console\Commands\CheckToolHugePsxRoblox',
        'App\Console\Commands\CheckToolGempet99',
        'App\Console\Commands\CheckToolGemUnist',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('defragment:cron')->everyTenMinutes()->withoutOverlapping(1);
        $schedule->command('defragment:cron')->everyMinute()->withoutOverlapping(1);
        $schedule->command('defragmentsan:cron')->everyMinute()->withoutOverlapping(1);

        $schedule->command('completeManualServiceOrders:cron')->everyMinute()->withoutOverlapping(1);
//        $schedule->command('deleteImageNickCompleteS3:cron')->everyMinute()->withoutOverlapping(1);
//
        $schedule->command('deleteImageNickCompleteS3:cron')
            ->timezone('Asia/Ho_Chi_Minh')
            ->hourly()
            ->between('2:00', '6:00')->withoutOverlapping(1);
//        $schedule->command('checkToolRoblox:cron')->everyMinute()->withoutOverlapping(1);
        $schedule->command('checkToolHugePsxRoblox:cron')->everyMinute()->withoutOverlapping(1);
        $schedule->command('checkToolGemUnist:cron')->everyMinute()->withoutOverlapping(1);
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
