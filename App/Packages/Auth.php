<?php

namespace App\Packages;

use App\Core\Request;
use App\Core\HasTokens;
use App\Core\DotEnvKey;
use App\Core\QueryBuilder;

class Auth
{
    use HasTokens;


    private static function verify(string $token)
    {
        $user = (new self)->verifyToken($token, DotEnvKey::get('ACCESS_TOKEN_SECRET_KEY'));
        if (isset($user->data)) {
            return $user->data;
        }
        return false;
    }

    public static function check(): bool
    {
        $token = Request::bearerToken();
        $user = self::verify($token);
        if ($user) {
            $user = QueryBuilder::table('users')->where('id', $user->id)->first();
            if ($user) {
                return true;
            }
        }
        return false;
    }

    public static function user()
    {
        $token = Request::bearerToken();
        $user = self::verify($token);
        if ($user) {
            $user = QueryBuilder::table('users')->where('id', $user->id)->first();
            if ($user) {
                return $user;
            }
        }
        return false;
    }
}
