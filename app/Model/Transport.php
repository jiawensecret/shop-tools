<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    const WAITING_SEARCH = 1,//待查询
        SEARCHING = 2,//查询中
        NO_SEARCH = 3,//未查到
        TRANSPORTING =4,//运输中
        WAITING_GET = 5,//待领取
        GOT = 6,//已签收
        TRANS_FAIL = 7,//投递失败
        TRANS_TOO_LONG = 8,//运输过久
        EXC = 9;//有异常

    const STATUS_MAP = [
        self::WAITING_SEARCH => '待查询',
        self::SEARCHING => '查询中',
        self::NO_SEARCH => '未查到',
        self::TRANSPORTING => '运输中',
        self::WAITING_GET => '到达待取',
        self::GOT => '已签收',
        self::TRANS_FAIL => '投递失败',
        self::TRANS_TOO_LONG => '运输过久',
        self::EXC => '有异常',
    ];

    protected $guarded = ['id'];
}
