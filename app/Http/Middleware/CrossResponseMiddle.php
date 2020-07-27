<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CrossResponseMiddle {
    /**
     * 后置中间件 在请求头中加入允许跨域
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $response = $next($request);
        if ( $response instanceof Response)
            $response->header('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
