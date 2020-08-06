<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const VOLUMED = 1,
        NOT_VOLUME = 0;

    protected $guarded = ['id'];
    protected $appends = ['order_goods','transport_info'];

    public function goods() {
        return $this->hasMany(OrderGoods::class);
    }

    public function transport()
    {
        return $this->hasMany(Transport::class,'order_no','order_no');
    }

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public function getOrderGoodsAttribute($value)
    {
        return $this->goods;
    }

    public function getTransportInfoAttribute($value)
    {
        return $this->transport;
    }


}
