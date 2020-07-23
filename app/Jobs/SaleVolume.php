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

    protected $job;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SaleVolumeJob $job)
    {
        $this->job = $job;
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
            $service =  new \App\Services\SaleVolume($this->job->month);
            $service->builtOrderLog();
            $service->calculate($this->job->exchange);
            $service->totalReport($this->job->exchange);

            $this->job->status = 1;
            $this->job->save();

        } catch (\Exception $exception) {
            Log::error('saleVolume error: ' . $exception->getMessage(), $exception->getTrace());
            $this->job->status = 2;
            $this->job->error_msg = $exception->getMessage();
            $this->job->save();
        }

    }
}
