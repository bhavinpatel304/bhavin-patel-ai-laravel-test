<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Log the API request
        Log::channel('api')->info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'input' => $request->except(['password', 'token']),
        ]);

        $response = $next($request);

        // Log the API response
        Log::channel('api')->info('API Response', [
            'status' => $response->status(),
            'url' => $request->fullUrl(),
        ]);

        return $response;
    }
}
