<?php

namespace App\Console\Commands;

use App\Model\Account;
use App\Model\PaypalReport;
use App\Services\Paypal;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetPaypalInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paypal:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取paypal info';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accounts = Account::all();

        foreach($accounts as $account) {
            if (empty($account->client_id) || empty($account->client_password)) continue;

            try {
                $model = new Paypal($account);

                $report = PaypalReport::where('account_id',$account->id)->orderBy('transaction_initiation_date','desc')->first();
                $startTime = isset($report->transaction_initiation_date)
                    ? Carbon::parse($report->transaction_initiation_date)->subDay()->toIso8601ZuluString()
                    : Carbon::parse('2020-08-01')->subDay()->toIso8601ZuluString();
                $endTime = Carbon::parse($startTime)->addDays(15)->toIso8601ZuluString();
                $data = $model->getPaypalOrder($startTime,$endTime,1);
                $model->storeOrder($data);

                while(count($data) == 500) {
                    $report = PaypalReport::where('account_id',$account->id)->orderBy('transaction_initiation_date','desc')->first();
                    $startTime = Carbon::parse($report->transaction_initiation_date)->subDay()->toIso8601ZuluString();
                    $endTime = Carbon::parse($startTime)->addDays(15)->toIso8601ZuluString();
                    $data = $model->getPaypalOrder($startTime,$endTime,1);
                    $model->storeOrder($data);
                }
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }

}
