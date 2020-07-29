<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SalesVolume extends Model
{
    //
    protected $guarded = ['id'];
    protected $appends = ['person_name'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function log()
    {
        return $this->hasMany(SaleVolumeOrderLog::class);
    }

    public function getPersonNameAttribute($value)
    {
        return $this->person->name ?? '';
    }


}
