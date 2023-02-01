<?php

namespace App\Core;

use Closure;
use App\Core\Responses;

class Middleware
{
    use Responses;

    public function handle(Closure $next)
    {
        return $next();
    }
}
