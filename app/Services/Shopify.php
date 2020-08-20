<?php


namespace App\Services;


use App\Model\Order;
use App\Model\Shop;

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

    public function getOrders($startTime,$limit = 100)
    {
        $query = [
            'limit' => $limit,
            'created_at_min' => $startTime,
            'status' => 'any',
        ];

        $url = sprintf(config('shopify.orders'), $this->code) . http_build_query($query);
        $this->setHeader();

        $res = \Requests::get($url, $this->header,['timeout' => 65, 'connect_timeout' => 10]);

        preg_match("/(?<=\<)[^>]+/", $res->headers['link'], $match);


        $url = $match[0] ?? '';


        $data = json_decode($res->body, true);

        return [$data['orders'], $url];

    }

    public function getOrdersByUrl($url)
    {
        $res = \Requests::get($url, $this->header,['timeout' => 65, 'connect_timeout' => 10]);

        preg_match_all("/(?<=\<)[^>]+/", $res->headers['link'], $match);

        $urls = $match[0] ?? [];

        if (count($urls) == 2) {
            $nextUrl = $urls[1];
        } else {
            $nextUrl = '';
        }

        $data = json_decode($res->body, true);

        return [$data['orders'], $nextUrl];
    }

    public function dealOrder($data)
    {
        $orderData = [
            'order_no' => $this->shop->dxm_id . '-' . $data['order_number'],
            'shop_id' => $this->shop->id,

            'status_text' => $data['financial_status'],
            'channel' => 'shopify',
            'order_time' => $data['created_at'],
            'pay_time' => $data['processed_at'],

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
                'sku' => $item['sku'],
                'count' => $item['quantity'],
                'product_no' => $item['product_id'],
                'product_code' => $item['variant_id'],
                'product_name' => $item['name'],
                'price' =>  $item['price'],
                'size' => $item['variant_title'],
                'shopify_order_goods_id' => $item['id'],
            ];
            $goodsData = array_filter($goodsData);

            $order->goods()->updateOrCreate([
                'order_id' => $order->id,
                'order_no' => $orderData['order_no'],
                'sku' => $item['sku']
            ],$goodsData);
        }
    }


}
