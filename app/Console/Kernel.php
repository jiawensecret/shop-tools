<?php

namespace App\Console;

use App\Console\Commands\GetOrdersFromShopify;
use App\Console\Commands\initSku;
use App\Console\Commands\SetSupplierPrice;
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
        ShopifyShipping::class,
        SetSupplierPrice::class,
        initSku::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('shopify:orders')->twiceDaily(1,13);

        $schedule->command("shopify:orders --pid=1")->everySixHours();
        $schedule->command("shopify:orders --pid=2")->everySixHours();
        $schedule->command("shopify:orders --pid=3")->everySixHours();
        $schedule->command("shopify:orders --pid=4")->everySixHours();
        $schedule->command("shopify:orders --pid=5")->everySixHours();
        $schedule->command("shopify:orders --pid=6")->everySixHours();
        $schedule->command("shopify:orders --pid=7")->everySixHours();
        $schedule->command("shopify:orders --pid=8")->everySixHours();
        $schedule->command("shopify:orders --pid=9")->everySixHours();


        $schedule->command("shopify:shipping --pid=1")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=2")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=3")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=4")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=5")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=6")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=7")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=8")->dailyAt('14:30');
        $schedule->command("shopify:shipping --pid=9")->dailyAt('14:30');

        $schedule->command("shopify:payment --pid=1")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=2")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=3")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=4")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=5")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=6")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=7")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=8")->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=9")->dailyAt('5:00');


        $schedule->command("setSupplierPrice")->hourly();
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
