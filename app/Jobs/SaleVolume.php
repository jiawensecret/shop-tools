<?php

namespace App\Jobs;

use App\Model\SaleVolumeJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SaleVolume implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $saleVolumeJob;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SaleVolumeJob $saleVolumeJob)
    {
        $this->saleVolumeJob = $saleVolumeJob;
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
            $service =  new \App\Services\SaleVolume($this->saleVolumeJob->month);
            $service->updateLogs();
            $service->builtOrderLog();
            $service->calculate($this->saleVolumeJob->exchange);
            $service->totalReport($this->saleVolumeJob->exchange);

            $this->saleVolumeJob->status = 1;
            $this->saleVolumeJob->save();

        } catch (\Exception $exception) {
            Log::error('saleVolume error: ' . $exception->getMessage(), $exception->getTrace());
            $this->saleVolumeJob->status = 2;
            $this->saleVolumeJob->error_msg = $exception->getMessage();
            $this->saleVolumeJob->save();
        }

    }
}
