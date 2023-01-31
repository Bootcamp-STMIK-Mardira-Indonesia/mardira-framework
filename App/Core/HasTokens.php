<?php

namespace App\Core;

use Firebase\JWT\JWT;

trait HasTokens
{
    protected string $hash = 'HS256';

    /**
     * Generate a token for the user and return it to the user
     *
     * @param  mixed $payload
     * @param  mixed $key
     * @return string
     */
    public function generateToken(array $payload, string $key): string
    {
        $token = $this->encodeToken($payload, $key);
        return $token;
    }

    /**
     * Verify the token and return the payload if the token is valid
     *
     * @param  mixed $token
     * @param  mixed $key
     * @return object
     */
    public function verifyToken(string $token, string $key) : object
    {
        if (strpos($token, 'Bearer') !== false) {
            $token = explode(' ', $token)[1];
        }
        $decoded = $this->decodeToken($token, $key);
        return $decoded->data;
    }

    /**
     * Encode the token using the JWT library
     *
     * @param  mixed $payload
     * @param  mixed $key
     * @return string
     */
    private function encodeToken(array $payload, string $key): string
    {
        return JWT::encode($payload, $key, $this->hash);
    }

    /**
     * Decode the token using the JWT library
     *
     * @param  mixed $token
     * @param  mixed $key
     * @return object
     */
    private function decodeToken(string $token, string $key): object
    {
        try {
            $decoded = JWT::decode($token, $key, $this->hash);
            return $decoded;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
