<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $pageSize = 30;

    protected $responseData = [
        'code'    => 0,
        'message' => '',
    ];

    public function setResponseField(int $code, string $message = '') {
        $this->responseData['code'] != 0 ?: $this->responseData['code'] = $code;
        $this->responseData['message'] = $message;
    }
}
