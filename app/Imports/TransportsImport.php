<?php

namespace App\Imports;


use App\Model\OrderGoods;
use App\Model\Transport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class TransportsImport implements OnEachRow
{
    protected $map = [];

    /**
    * @param Collection $collection
    */
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        if ($row[0] == '物流方式') {
            $this->map = array_flip($row);
        } else {
            $statusMap = array_flip(Transport::STATUS_MAP);

            $transport = Transport::where('package_code',$row[$this->map['包裹号']])
                ->where('order_no',$row[$this->map['订单号']])
                ->first();

            $data = [
                'package_code' => $row[$this->map['包裹号']],
                'transport_no' => $row[$this->map['运单号']],
                'order_no' => $row[$this->map['订单号']],
                'transport_name' => $row[$this->map['物流方式']],
                'new_info' => $row[$this->map['最新信息']],
                'country' => $row[$this->map['国家']],
                'consignee' => $row[$this->map['收件人']],
                'status_text' => $row[$this->map['运输状态']],
                'status' => $statusMap[$row[$this->map['运输状态']]] ?? 0,
            ];

            $data = array_filter($data);
            if ($transport) {
                $transport->update($transport);
            } else {
                $transport = Transport::create($data);
            }

        }
    }
}
