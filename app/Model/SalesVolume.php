<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SalesVolume extends Model
{
    //
    protected $guarded = ['id'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function log()
    {
        return $this->hasMany(SaleVolumeOrderLog::class);
    }


}
