<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdPrice extends Model
{
    //
    protected $guarded = ['id'];
    protected $appends = ['shop_name'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getShopNameAttribute($value)
    {
        return $this->shop->name ?? '';
    }
}
