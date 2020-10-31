<?php


namespace App\Services;


use App\Model\Account;
use App\Model\PaypalReport;
use Carbon\Carbon;
use Requests_Auth_Basic;

class Paypal
{
    const AUTH_URL = 'https://api.paypal.com/v1/oauth2/token';

    const ORDER_URL = 'https://api.paypal.com/v1/reporting/transactions?';

    protected $account_id;

    protected $client_id = '';

    protected $client_password = '';

    protected $access_token = '';

    public function __construct(Account $account) {
        $this->account_id = $account->id;
        $this->client_id = $account->client_id;
        $this->client_password = $account->client_password;
        $this->access_token = $this->getToken();
    }

    public function getToken(){
        $res = \Requests::post(self::AUTH_URL,['Content-Type' => 'application/x-www-form-urlencoded'] ,['grant_type'=>'client_credentials'], ['auth' => new Requests_Auth_Basic([$this->client_id,$this->client_password])]);
        $data = json_decode($res->body, true);
        return $data['access_token'];
    }

    public function getPaypalOrder($startTime,$endTime,$page)
    {
        dump($url = self::ORDER_URL."start_date={$startTime}&end_date={$endTime}&&fields=transaction_info&page_size=500&page={$page}");
        try{
            $url = self::ORDER_URL."start_date={$startTime}&end_date={$endTime}&&fields=transaction_info&page_size=500&page={$page}";
            $res = \Requests::get($url,['Content-Type' => 'application/json','Authorization' => "Bearer {$this->access_token}"],['timeout' => 650, 'connect_timeout' => 100]);
            $data = json_decode($res->body, true);
        } catch (\Exception $exception) {
            return [];
        }

        return $data['transaction_details'] ?? [];
    }

    public function storeOrder($data)
    {
        foreach($data as $item) {
            $value = [
                'account_id' => $this->account_id,
                'paypal_account_id' => $item['transaction_info']['paypal_account_id'] ?? '',
                'transaction_id' => $item['transaction_info']['transaction_id'] ?? '',
                'paypal_reference_id' => $item['transaction_info']['paypal_reference_id'] ?? '',
                'transaction_event_code' => $item['transaction_info']['transaction_event_code'] ?? '',
                'transaction_amount' => $item['transaction_info']['transaction_amount']['value'] ?? 0,
                'fee_amount' => $item['transaction_info']['fee_amount']['value'] ?? 0,
                'invoice_id' => $item['transaction_info']['invoice_id'] ?? '',
                'transaction_initiation_date' => Carbon::parse($item['transaction_info']['transaction_initiation_date']) ?? '',
                'transaction_updated_date' => Carbon::parse($item['transaction_info']['transaction_updated_date']) ?? '',
            ];
            PaypalReport::updateOrCreate(['transaction_id' => $item['transaction_info']['transaction_id']],$value);
        }
    }
}
