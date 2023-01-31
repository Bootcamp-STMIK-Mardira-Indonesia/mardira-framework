<?php

namespace App\Core;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-Requested-With');

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
