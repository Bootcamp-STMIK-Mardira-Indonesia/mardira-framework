<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\TimeHelper;
use App\Core\Validator;

class AuthController extends Controller
{
    public function login(): void
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $validator = Validator::validate([
            'username' => [
                'required' => true,

            ],
            'password' => [
                'required' => true,
            ]
        ], [
            'username' => $username,
            'password' => $password
        ]);

        if ($validator->fails()) {
            $this->response(400, [
                'message' => 'Login failed',
                'errors' => $validator->errors()
            ]);
        }

        $user = $this->model('Users')->findWhere([
            'username' => $username,
            'password' => md5($password)
        ]);
        if ($user) {
            $expiredTime = TimeHelper::setMinutes(30);
            $payload = [
                'exp' => $expiredTime,
                'data' => [
                    'id' => $user->user_id,
                    'username' => $user->username,
                ]
            ];
            $token = $this->generateToken($payload, $this->env->get('ACCESS_TOKEN_SECRET_KEY'));
            $this->response(200, [
                'token' => $token,
                'message' => 'Login success',
                'expired_time' => date('Y-m-d H:i:s', $expiredTime)
            ]);
        } else {
            $this->response(401, [
                'message' => 'Login failed'
            ]);
        }
    }
}
