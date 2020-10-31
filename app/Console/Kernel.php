<?php

namespace App\Console;

use App\Console\Commands\BuildRefundOrder;
use App\Console\Commands\GetOrdersFromShopify;
use App\Console\Commands\GetOrdersTest;
use App\Console\Commands\GetOrdersWithTime;
use App\Console\Commands\GetPaypalInfo;
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
        GetOrdersTest::class,
        GetOrdersWithTime::class,
        GetPaypalInfo::class,
        BuildRefundOrder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('shopify:orders')->everySixHours();

        $schedule->command("shopify:orders --pid=1")->withoutOverlapping()->dailyAt('14：00');
        $schedule->command("shopify:orders --pid=2")->withoutOverlapping()->dailyAt('14：02');
        $schedule->command("shopify:orders --pid=3")->withoutOverlapping()->dailyAt('14：04');
        $schedule->command("shopify:orders --pid=4")->withoutOverlapping()->dailyAt('14：06');
        $schedule->command("shopify:orders --pid=5")->withoutOverlapping()->dailyAt('14：08');
        $schedule->command("shopify:orders --pid=6")->withoutOverlapping()->dailyAt('14：10');
        $schedule->command("shopify:orders --pid=7")->withoutOverlapping()->dailyAt('14：12');
        $schedule->command("shopify:orders --pid=8")->withoutOverlapping()->dailyAt('14：14');
        $schedule->command("shopify:orders --pid=9")->withoutOverlapping()->dailyAt('14：16');


        $schedule->command("shopify:shipping --pid=1")->withoutOverlapping()->dailyAt('14:45');
        $schedule->command("shopify:shipping --pid=2")->withoutOverlapping()->dailyAt('14:55');
        $schedule->command("shopify:shipping --pid=3")->withoutOverlapping()->dailyAt('15:15');
        $schedule->command("shopify:shipping --pid=4")->withoutOverlapping()->dailyAt('15:30');
        $schedule->command("shopify:shipping --pid=5")->withoutOverlapping()->dailyAt('15:45');
        $schedule->command("shopify:shipping --pid=6")->withoutOverlapping()->dailyAt('15:00');
        $schedule->command("shopify:shipping --pid=7")->withoutOverlapping()->dailyAt('16:45');
        $schedule->command("shopify:shipping --pid=8")->withoutOverlapping()->dailyAt('16:00');
        $schedule->command("shopify:shipping --pid=9")->withoutOverlapping()->dailyAt('16:15');

        $schedule->command("shopify:payment --pid=1")->withoutOverlapping()->dailyAt('5:00');
        $schedule->command("shopify:payment --pid=2")->withoutOverlapping()->dailyAt('5:02');
        $schedule->command("shopify:payment --pid=3")->withoutOverlapping()->dailyAt('5:04');
        $schedule->command("shopify:payment --pid=4")->withoutOverlapping()->dailyAt('5:06');
        $schedule->command("shopify:payment --pid=5")->withoutOverlapping()->dailyAt('5:08');
        $schedule->command("shopify:payment --pid=6")->withoutOverlapping()->dailyAt('5:10');
        $schedule->command("shopify:payment --pid=7")->withoutOverlapping()->dailyAt('5:12');
        $schedule->command("shopify:payment --pid=8")->withoutOverlapping()->dailyAt('5:14');
        $schedule->command("shopify:payment --pid=9")->withoutOverlapping()->dailyAt('5:16');


        $schedule->command("setSupplierPrice")->hourly();

        $schedule->command("refund:order")->twiceMonthly();
        $schedule->command("paypal:info")->daily();
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
