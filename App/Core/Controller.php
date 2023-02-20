<?php

namespace App\Core;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

use App\Core\Input;
use App\Core\DotEnvKey;
use App\Core\HasTokens;
use App\Core\Responses;


class Controller
{
    use HasTokens;
    use Responses;
    protected object $input;
    protected object $env;

    public function __construct()
    {
        $this->input = new Input();
        $this->env = new DotEnvKey();
    }

    /**
     * Model method to load the model
     *
     * @param  mixed $model
     * @return object
     */
    public function model(string $model): object
    {
        if (is_array($model)) {
            $model = array_map(function ($model) {
                $model = 'App\\Models\\' . $model;
                $model = $model . 'Models';
                return new $model;
            }, $model);
            return $model;
        }
        $model = 'App\\Models\\' . $model;
        return new $model;
    }
}
