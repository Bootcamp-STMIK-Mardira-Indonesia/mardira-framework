<?php

namespace App\Packages;

use App\Core\Request;
use App\Core\HasTokens;
use App\Core\DotEnvKey;
use App\Core\QueryBuilder;

class Auth
{
    use HasTokens;

    public static function check(): bool
    {
        $token = Request::bearerToken();
        $user = (new self)->verifyToken($token, DotEnvKey::get('ACCESS_TOKEN_SECRET_KEY'));
        if (isset($user->data)) {
            $user = QueryBuilder::table('users')->where('id', $user->data->id)->first();
            if ($user) {
                return true;
            }
        }
        return false;
    }
}
