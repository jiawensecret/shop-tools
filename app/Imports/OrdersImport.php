<?php

namespace App\Imports;

use App\Model\Order;
use App\Model\OrderGoods;
use App\Model\Person;
use App\Model\Shop;
use App\Model\WarningOrderGoods;
use App\TestExcel;
use Carbon\Carbon;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class OrdersImport implements OnEachRow
{
    protected $shop = [];

    protected $map = [];

    protected function setShop($shopCode){
        $shop = Shop::where('code',$shopCode)->first();

        if ($shop) {
            $shop = $shop->toArray();
            $this->shop[$shopCode] = $shop['id'];
            return $shop['id'];
        } else {
            return 0;
        }
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function onRow(Row $row)
    {
        $index = $row->getIndex();
        $row = $row->toArray();
        if ($row[0] == '包裹号') {
            $this->map = array_flip($row);
        } else {
            $shopId = $this->shop[$row[$this->map['店铺账号']]] ?? $this->setShop($row[$this->map['店铺账号']]);

            $order = Order::where('order_no', $row[$this->map['订单号']])->first();

            $orderData = [
                'order_no' => $row[$this->map['订单号']],
                'sale_no' => $row[$this->map['交易号']],
                'status_text' => $row[$this->map['订单状态']],
                'channel' => $row[$this->map['平台渠道']],
                'desc' => $row[$this->map['订单备注']],
                'order_time' => $row[$this->map['下单时间']],
                'pay_time' => $row[$this->map['付款时间']],
                'post_time' => $row[$this->map['提交时间']] ?: '',
                'refund_time' => $row[$this->map['退款时间']] ?: '',
                'pay_type' => $row[$this->map['付款方式']] ?: '',
                'order_price' => $row[$this->map['订单金额']] ?: '',
                'sale_transport_price' => $row[$this->map['买家支付运费']] ?: '',
                'refund_price' => $row[$this->map['退款金额']] ?: '',

                'custom_account' => $row[$this->map['买家账号']] ?: '',
                'custom_name' => $row[$this->map['买家姓名']] ?: '',
                'custom_email' => $row[$this->map['买家Email']] ?: '',
                'custom_transport_name' => $row[$this->map['买家指定物流']] ?: '',
                'transport_name' => $row[$this->map['物流方式']] ?: '',

                'consignee' => $row[$this->map['收货人姓名']] ?: '',
                'consignee_address' => $row[$this->map['详细地址']] ?: '',
                'consignee_city' => $row[$this->map['收货人城市']] ?: '',
                'consignee_province' => $row[$this->map['收货人州/省']] ?: '',
                'consignee_code' => $row[$this->map['邮编']] ?: '',
                'consignee_country' => $row[$this->map['收货人国家']] ?: '',
                'consignee_country_code' => $row[$this->map['国家二字码']] ?: '',
                'consignee_phone' => $row[$this->map['收货人电话']] ?: '',
                'consignee_tel' => $row[$this->map['收货人手机']] ?: '',
            ];

            $orderData = array_filter($orderData);
            if ($order) {
                $order->update($orderData);
            }

            $order = $order->refresh();

            $goodsData = [
                'order_id' => $order->id,
                'order_no' => $row[$this->map['订单号']],
                'package_code' => $row[$this->map['包裹号']],
                'transport_no' => $row[$this->map['运单号']],
                'sku' => $row[$this->map['SKU']],
                'product_no' => $row[$this->map['产品ID']],
                'product_code' => $row[$this->map['商品编码']],
                'product_name' => $row[$this->map['产品名称']],
                'price' => $row[$this->map['产品售价']],
                'count' => $row[$this->map['产品数量']],
                'size' => $row[$this->map['产品规格']],
                'pic' => $row[$this->map['图片网址']],
                'sale_name' => $row[$this->map['商品名称']],
            ];

            $goodsData = array_filter($goodsData);
            $goodsData['supplier_price'] = $row[$this->map['商品采购价']] ?: 0;

            $orderGoods = OrderGoods::where('order_no',$row[$this->map['订单号']])
                ->where('sku',$row[$this->map['SKU']])
                ->first();

            if ($orderGoods) {
                $orderGoods->update($goodsData);
            }
//            $order->goods()->updateOrCreate($goodsData);
        }
    }
}
