<?php

namespace App\Middleware;

use Closure;
use App\Core\Middleware;
use App\Packages\Auth;

class AuthMiddleware extends Middleware
{
    public function handle(Closure $next)
    {
        $user = Auth::check();
        if ($user) {
            return $next();
        }
        return $this->response(401, ['message' => 'Unauthorized']);
    }
}
