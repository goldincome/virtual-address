<?php

namespace App\Http\Middleware;

use App\Enums\UserTypeEnum;
use Closure;
use Illuminate\Http\Request;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
            
        if (!$user || !in_array($user->user_type, [UserTypeEnum::ADMIN->value, UserTypeEnum::SUPER_ADMIN->value])) {
             return redirect()->route('admin.login'); //abort(403);
        }

        return $next($request);
    }
}
