<?php

namespace App\Http\Middleware;

use Closure;

class ReactMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->get('pageSize')) {
            $request->offsetSet('page_size',$request->get('pageSize'));
        }
        if ($request->get('current')) {
            $request->offsetSet('page',$request->get('current'));
        }
        return $next($request);
    }
}
