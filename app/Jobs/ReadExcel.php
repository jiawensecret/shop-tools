<?php

namespace App\Jobs;

use App\Imports\OrdersImport;
use App\Imports\SupportImport;
use App\Imports\TransportPriceImport;
use App\Imports\TransportsImport;
use App\Model\ReadExcelJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReadExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $readExcelJob;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ReadExcelJob $readExcelJob)
    {
        $this->readExcelJob = $readExcelJob;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        try {
            switch ($this->readExcelJob->type) {
                case 'order':
                    Excel::import(new OrdersImport, storage_path($this->readExcelJob->filename));
                    break;
                case 'transport':
                    Excel::import(new TransportsImport, storage_path($this->readExcelJob->filename));
                    break;
                case 'transport_price':
                    Excel::import(new TransportPriceImport, storage_path($this->readExcelJob->filename));
                    break;
                case 'support':
                    Excel::import(new SupportImport, storage_path($this->readExcelJob->filename));
                    $raw = 'update supports a,(select sum(transport_price) as tranport_price,sum(other_price) as other_price,sum(discount) as discount,count(support_code) as count,support_code from supports GROUP BY support_code) b set a.total_cost = (b.tranport_price+b.other_price-b.discount)/b.count where a.support_code = b.support_code';
                    DB::update($raw);
                    break;
            }
            $this->readExcelJob->status = 1;
            $this->readExcelJob->save();

        } catch (\Exception $exception) {
            Log::error('export detail error: ' . $exception->getMessage(), $exception->getTrace());
            $this->readExcelJob->status = 2;
            $this->readExcelJob->error_msg = $exception->getMessage();
            $this->readExcelJob->save();
        }

    }
}
