<?php

namespace App\Http\Middleware;

use Closure;

class BlockBots
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
        
        if ($this->isBot($request)) {
           
            return response()->json(['error' => 'Acceso denegado para bots'], 403);
        }

        return $next($request);
    }

    /**
     * Verificar si la solicitud proviene de un bot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isBot($request)
    {
        
        $ip = $request->ip();

       

       
        $maxRequestsPerMinute = 10;

       
        $requests = cache()->get("requests:$ip", []);

        
        $requests[] = now()->timestamp;

        
        $requests = array_filter($requests, function ($timestamp) {
            return $timestamp > now()->subMinutes(1)->timestamp;
        });

       
        cache()->put("requests:$ip", $requests, 1);

       
        return count($requests) > $maxRequestsPerMinute;
    }
}