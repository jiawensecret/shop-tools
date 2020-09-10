<?php

namespace App\Imports;

use App\Model\Support;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class SupportImport implements OnEachRow
{
    protected $map = [];

    /**
     * @param Collection $collection
     */
    public function onRow(Row $row)
    {
        $row = $row->toArray();
        if ($row[0] == '采购单号') {
            $this->map = array_flip($row);
        } else {
            $support = [
                'support_code' => $row[$this->map['采购单号']],
                'goods_code' => $row[$this->map['商品编码']],
                'sku' => $row[$this->map['商品SKU']],
                'price' => $row[$this->map['单价（CNY）']],
                'count' => $row[$this->map['采购数量']],
                'total_price' => $row[$this->map['商品总金额']],
                'transport_price' => $row[$this->map['运费']],
                'other_price' => $row[$this->map['其它费用']],
                'discount' => $row[$this->map['采购单折扣金额']],
                'order_code' => $row[$this->map['1688订单号']],
                'order_time' => $row[$this->map['创建时间']],
            ];

            $support = array_filter($support);

            Support::updateOrCreate(['support_code' => $row[$this->map['采购单号']],
                'sku' => $row[$this->map['商品SKU']]], $support);
        }

    }
}
