<?php

namespace App\Http\Middleware;

use App\Models\ApiAccessControl;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiAccessTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        $accessControl = ApiAccessControl::where('api_endpoint', $path)->first();

        if (!$accessControl) {
            return response()->json(['message' => 'API access control not found.'], 404);
        }

        $now = Carbon::now();

        if ($now->lessThan($accessControl->access_start) || $now->greaterThan($accessControl->access_end)) {
            return response()->json(['message' => 'API access is not allowed at this time.'], 403);
        }

        return $next($request);
    }
}
