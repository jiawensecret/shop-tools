<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SaleVolumeOrderLog extends Model
{
    //
    protected $guarded = ['id'];

    public function report()
    {
        return $this->belongsTo(SalesVolume::class);
    }
}
