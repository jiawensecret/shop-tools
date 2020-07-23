<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    //
    protected $guarded = ['id'];

    public function shop()
    {
        return $this->hasMany(Shop::class);
    }
}
