<?php

namespace App\Imports;


use App\Model\Transport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class TransportPriceImport implements OnEachRow
{
    protected $map = [];

    /**
    * @param Collection $collection
    */
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        if ($row[0] == '发货日期') {
            $this->map = array_flip($row);
        } else {
            $transport = Transport::where('transport_no',$row[$this->map['运单号']])
                ->first();

            if ($transport) {
                $transport->transport_price = $row[$this->map['物流费用（￥）']];
                $transport->save();
            }
        }
    }
}
