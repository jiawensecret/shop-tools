<?php

namespace App\Console;

use App\Console\Commands\GetOrdersFromShopify;
use App\Console\Commands\ShopifyPayment;
use App\Console\Commands\ShopifyShipping;
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
        GetOrdersFromShopify::class,
        ShopifyPayment::class,
        ShopifyShipping::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('shopify:orders')->twiceDaily(1,13);
        $schedule->command('shopify:shipping')->dailyAt('9:00');
        $schedule->command('shopify:payment')->dailyAt('5:00');
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
