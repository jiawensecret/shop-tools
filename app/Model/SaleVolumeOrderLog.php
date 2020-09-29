<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SaleVolumeOrderLog extends Model
{
    //
    protected $guarded = ['id'];

    protected $appends = ['order_no', 'exchange', 'cost_price_show','profit_show',
        'transport_price_show', 'ad_price_show', 'shop_charge_show','volume','shop_name'
    ];

    public function report()
    {
        return $this->belongsTo(SalesVolume::class, 'sales_volume_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getOrderNoAttribute($value)
    {
        return $this->order->order_no ?? '';
    }

    public function getExchangeAttribute($value)
    {
        return $this->report->exchange ?? 7;
    }

    public function getCostPriceShowAttribute($value)
    {
        return round($this->cost_price/$this->exchange,2);
    }

    public function getTransportPriceShowAttribute($value)
    {
        return round($this->transport_price/$this->exchange,2);
    }

    public function getAdPriceShowAttribute($value)
    {
        return round($this->ad_price/$this->exchange,2);
    }

    public function getShopChargeShowAttribute($value)
    {
        return round($this->shop_charge/$this->exchange,2);
    }

    public function getProfitShowAttribute($value)
    {
        return round($this->profit/$this->exchange,2);
    }

    public function getVolumeAttribute($value)
    {
        return round($this->order_price - $this->pay_charge,2);
    }

    public function getShopNameAttribute($value)
    {
        return $this->shop->shop_name;
    }

}
