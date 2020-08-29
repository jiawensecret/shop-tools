<?php


namespace App\Services;


use App\Model\Order;
use App\Model\Shop;
use App\SystemShopifyLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Shopify
{
    protected $clientId;

    protected $clientSecret;

    protected $code;

    protected $header;

    protected $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->clientId = $shop->client_id;
        $this->clientSecret = $shop->client_password;
        $this->code = $shop->code;
    }

    protected function setHeader()
    {
        $this->header = ['X-Shopify-Access-Token' => $this->clientSecret];
    }

    public function getOrders($startTime, $limit = 240)
    {
        $query = [
            'limit' => $limit,
            'created_at_min' => $startTime,
            'status' => 'any',
        ];

        $url = sprintf(config('shopify.orders'), $this->code) . http_build_query($query);
        $this->setHeader();

        Log::info('【orders】 url:'.$url);

        $res = \Requests::get($url, $this->header, ['timeout' => 65, 'connect_timeout' => 10]);

        preg_match("/(?<=\<)[^>]+/", $res->headers['link'], $match);


        $url = $match[0] ?? '';
        Log::info('【orders】 url:'.$url);

        $data = json_decode($res->body, true);

        return [$data['orders'], $url];

    }

    public function getOrdersByUrl($url,SystemShopifyLog $shopifyLog)
    {
        try {
            $res = \Requests::get($url, $this->header, ['timeout' => 650, 'connect_timeout' => 100]);
            $shopifyLog->is_success = 1;
            $shopifyLog->save();
        } catch (\Exception $exception) {
            $shopifyLog->is_success = 2;
            $shopifyLog->save();
            return [];
        }

        preg_match_all("/(?<=\<)[^>]+/", $res->headers['link'], $match);

        $urls = $match[0] ?? [];

        if (count($urls) == 2) {
            $nextUrl = $urls[1];
        } else {
            $nextUrl = '';
        }

        Log::info('【orders】 link:'.json_encode($res->headers['link']));
        Log::info('【orders】 url:'.$nextUrl);

        $data = json_decode($res->body, true);

        return [$data['orders'], $nextUrl];
    }

    public function getShippingByOrder($orderId)
    {
        $url = sprintf(config('shopify.shipping'), $this->code, $orderId);

        $this->setHeader();
        $res = \Requests::get($url, $this->header, ['timeout' => 65, 'connect_timeout' => 10]);

        $data = json_decode($res->body, true);
        return $data['fulfillments'];
    }

    public function getPaymentByOrder($orderId)
    {
        $url = sprintf(config('shopify.payment'),$this->code,$orderId);

        $this->setHeader();
        $res = \Requests::get($url, $this->header, ['timeout' => 65, 'connect_timeout' => 10]);

        $data = json_decode($res->body, true);
        return $data['transactions'];
    }

    public function dealOrder($data)
    {
        $orderData = [
            'order_no' => $this->shop->dxm_id . '-' . $data['order_number'],
            'shop_id' => $this->shop->id,

            'status_text' => $data['financial_status'],
            'channel' => 'shopify',
            'order_time' => Carbon::parse($data['created_at'])->toDateTimeString(),
            'pay_time' => Carbon::parse($data['created_at'])->toDateTimeString(),

            'pay_type' => $data['gateway'] ?: '',
            'order_price' => $data['total_price'] ?: '',


            'custom_account' => $data['customer']['email'] ?: '',
            'custom_name' => $data['customer']['first_name'] . ' ' . $data['customer']['last_name'],
            'custom_email' => $data['customer']['email'] ?: '',
            'custom_transport_name' => $data['customer']['email'] ?: '',
            'transport_name' => $data['customer']['email'] ?: '',

            'consignee' => $data['shipping_address']['name'],
            'consignee_address' => $data['shipping_address']['address1'],
            'consignee_city' => $data['shipping_address']['city'],
            'consignee_province' => $data['shipping_address']['province'],
            'consignee_code' => $data['shipping_address']['zip'],
            'consignee_country' => $data['shipping_address']['country'] ?: '',
            'consignee_country_code' => $data['shipping_address']['country_code'] ?: '',
            'consignee_phone' => $data['shipping_address']['phone'] ?: '',

            'shopify_order_id' => $data['id'],
            'checkout_id' => $data['checkout_id'],
        ];

        $order = Order::where('shopify_order_id', $orderData['shopify_order_id'])->first();

        $orderData = array_filter($orderData);
        if ($order) {
            $order->update($orderData);
        } else {
            $order = Order::create($orderData);
        }

        $order = $order->refresh();

        foreach($data['line_items'] as $item) {
            $goodsData = [
                'order_id' => $order->id,
                'order_no' => $orderData['order_no'],
                'count' => $item['quantity'] ?: 1,
                'product_no' => $item['product_id'],
                'product_code' => $item['variant_id'],
                'product_name' => $item['name'],
                'price' =>  $item['price'],
                'size' => $item['variant_title'],
                'shopify_order_goods_id' => $item['id'],
            ];
            $goodsData = array_filter($goodsData);

            $sku = empty($item['sku']) ? '' : $item['sku'];
            $goodsData['sku'] = $sku;

            $order->goods()->updateOrCreate([
                'order_id' => $order->id,
                'order_no' => $orderData['order_no'],
                'sku' => $sku
            ],$goodsData);
        }
    }

    public function dealShipping(Order $order,$data)
    {
        foreach($data as $value) {
            $info = [
                'transport_no' => $value['tracking_number'],
                'order_no' => $order->order_no,
                'transport_name' => $value['tracking_company'] ?: '',
                'status_text' => $value['shipment_status'] ?: '',
                'status' => ($value['shipment_status'] == 'delivered') ? 6: 0,
                'shopify_fulfillment_id' => $value['id'],
                'shopify_fulfillment_status' => $value['status'],
                'order_id' => $order->id,
            ];

            $order->transport()->updateOrCreate([
                'shopify_fulfillment_id' => $value['id'],
            ],$info);

            if ($info['status'] == 6) {
                $order->shipping_status = 1;
                $order->save();
            }
        }
    }

    public function dealPayment(Order $order,$data)
    {
        foreach ($data as $value) {
            $order->sale_no = $value['authorization'];
            $order->fee_amount = $value['receipt']['fee_amount'] ?? 0;
            $order->pay_time = Carbon::parse($value['receipt']['payment_date'])->toDateTimeString();
            $order->is_transactions = 1;
            $order->save();
        }
    }


}
