<?php


namespace App\Middleware;

use Closure;
use App\Core\Middleware;
use App\Packages\Auth;

class AdminMiddleware extends Middleware
{
    public function handle(Closure $next)
    {
        $user = Auth::user();
        $role = $user->role_id;

        if ($role == 1) {
            return $next();
        }
        return $this->response(401, ['message' => 'Only Admin can access this route']);
    }
}
