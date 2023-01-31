<?php

namespace App\Models;

use App\Core\Model;

class Users extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';
}