<?php

namespace App\Http\Middleware;

use App\Role;
use Closure;

class IsHrMiddleware
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
        $this->user = auth('web')->user();
        if($this->user->role_id != Role::HR) {
            return response()->json(['message' => 'you_do_not_have_permission'],404);
        }
        return $next($request);
    }
}
