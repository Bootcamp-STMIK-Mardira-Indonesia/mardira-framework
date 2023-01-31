<?php

namespace App\Core;

trait Responses
{

    /**
     * Response and status code
     *
     * @param  mixed $code
     * @param  mixed $data
     * @return void
     */
    public static function response(int $code, array $data): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }


}
