<!-- write title Mardira Framework -->

<p align="center"><a href="https://demostmikmi.com" target="_blank"><img src="https://raw.githubusercontent.com/Bootcamp-STMIK-Mardira-Indonesia/mardira-framework/master/public/logo.png" width="150" alt="Mardira Logo"></a></p>

<!-- Description here -->

Mardira Framework is a PHP framework Model Controller Based for building web applications and APIs. It is designed to be simple, and fast.

![Total Downloads](https://img.shields.io/packagist/dt/mardira/mardira-framework?color=e&style=for-the-badge)
![Total Stars](https://img.shields.io/github/stars/Bootcamp-STMIK-Mardira-Indonesia/mardira-framework?color=e&style=for-the-badge)
![Total Forks](https://img.shields.io/github/forks/Bootcamp-STMIK-Mardira-Indonesia/mardira-framework?color=e&style=for-the-badge)
![Version](https://img.shields.io/packagist/v/mardira/mardira-framework?color=e&style=for-the-badge)
![License](https://img.shields.io/github/license/Bootcamp-STMIK-Mardira-Indonesia/mardira-framework?color=e&style=for-the-badge)

## Table of Contents

- [Requirements](#requirements)
- [Structure Folders](#structure-folders)
- [Installation](#installation)
- [Usage](#usage)
  - [Start Server](#start-server)
  - [Create .env](#create-env)
  - [Create Controller](#create-controller)
  - [Create Model](#create-model)
  - [Create Migration](#create-migration)
  - [Run Migration](#run-migration)
  - [Refresh Migration](#refresh-migration)
  - [Refresh Migration With Seed](#refresh-migration-with-seed)
  - [Create Seeder](#create-seeder)
  - [Run Seeder](#run-seeder)
  - [Run Seeder Specific](#run-seeder-specific)
  - [Create Authetication](#create-authetication)
  - [Refresh Authetication](#refresh-authetication)
  - [Update Framework Version](#update-framework-version)
  - [Controller](#controller)
  - [Model](#model)
  - [Migration](#migration)
  - [Seeder](#seeder)
  - [Middleware](#middleware)
  - [Route](#route)
    - [Route Group](#route-group)
  - [Query Builder](#query-builder)
    - [Select](#select)
    - [Where](#where)
    - [Or Where](#or-where)
    - [Where In](#where-in)
    - [Where Not In](#where-not-in)
    - [Where Null](#where-null)
    - [Where Not Null](#where-not-null)
    - [Order By](#order-by)
    - [Group By](#group-by)
    - [Join](#join)
    - [Insert](#insert)
    - [Update](#update)
    - [Delete](#delete)
    - [Count](#count)



## Requirements

- PHP = 7.4
- MySQL >= 5.7.8
- Apache >= 2.4.41
- Composer >= 2.0.9

## Structure Folders

```shell
mardira-framework
├── App
│   ├── Controllers
│   │   ├── AuthController.php
│   ├── Core
│   │   ├── Commands
│   ├── Database
│   │   ├── Migrations
│   │   │   ├── 2023_01_31_xxxxxx_create_table_users.php
│   │   │   ├── 2023_01_31_xxxxxx_create_table_roles.php
│   │   ├── Seeders
│   │   │   ├── GlobalSeeder.php
│   ├── Helpers
│   ├── Middleware
│   ├── Models
│   ├── Packages
│   ├── Routes
│   │   ├── Api.php
```

## Installation


### Setup

> You can create a new project using composer

```shell
composer create-project mardira/mardira-framework <your_project_name>
```
> or you can clone this project

<!-- Installation here -->

### Clone

- Clone this repo to your local machine using `git clone

```shell
  git clone https://github.com/Bootcamp-STMIK-Mardira-Indonesia/mardira-framework.git
```

> Then, install the dependencies using composer

```shell
composer install
```

> or

```shell
composer update
```

## Usage

### Start Server

```shell
php mardira serve
```

> or

```shell
php mardira serve --port=<your_port>
```

### Create .env

> You can create .env file using command

```shell
php mardira make:env
```

### Create Controller

```shell
php mardira make:controller ControllerName
```

### Create Model

```shell
php mardira make:model ModelName
```

### Create Migration

```shell

php mardira make:migration create_table_table_name
```

### Run Migration

> If database not exist, will automatically create database from .env

```shell
php mardira migrate
```

### Refresh Migration

```shell
php mardira migrate:refresh
```

### Refresh Migration With Seed

```shell
php mardira migrate:refresh --seed
```

### Create Seeder

```shell
php mardira make:seeder SeederName
```

### Run Seeder

```shell
php mardira db:seed
```

### Run Seeder Specific

```shell
php mardira db:seed --class=SeederName
```

### Create Authetication

```shell
php mardira make:auth
```

### Refresh Authetication

```shell
php mardira make:auth --refresh
```

### Update Framework Version

```shell
php mardira update
```

### Controller

> Create controller use `php mardira make:controller ControllerName`, here is example controller

```php
<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->response(200,[
            'message' => 'Hello World'
        ]);
    }
}
```

> to use controller, you can add route in `App/Routes/Api.php`

```php
<?php

use App\Core\Route;
use App\Controllers\HomeController;

Route::get('/home', [HomeController::class, 'index']);
```

### Response

> You can use response in controller

```php
$this->response(200,[
    'message' => 'Hello World'
]);

```

> return json expected

```json
{
  "message": "Hello World"
}
```

> another response example 409

```php
$this->response->json(409,[
    'message' => 'Conflict'
]);
```

### Model

> Create model use `php mardira make:model ModelName`, here is example model

```php
<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

> to use model, you can add model in `App/Controllers/ControllerName.php`

```php
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = User::all();

        $this->response(200,[
            'message' => 'Hello World',
            'data' => $user
        ]);
    }
}
```

### Migration

> Create migration use `php mardira make:migration create_table_table_name`, here is example migration

```php
<?php

namespace App\Database\Migrations;

use App\Core\Migration;

return new class extends Migration
{
    public function up()
    {
        $this->schema->create('users', function ($table) {
            $table->increment('id');
            $table->string('name', 50);
            $table->string('email',50)->unique();
            $table->string('password', 64);
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->dropIfExists('users');
    }
}
```

### Seeder

> Create seeder use `php mardira make:seeder SeederName`, here is example seeder

```php
<?php

namespace App\Database\Seeders;

use App\Core\Seeder;
use App\Core\QueryBuilder as DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role_id' => 1,
            ],
            [
                'name' => 'User',
                'username' => 'user',
                'email' => 'user@user.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role_id' => 2,
            ]
        ];
        DB::table('users')->insert($data);
    }
}

```

### Middleware

> Create middleware use `php mardira make:middleware MiddlewareName`, here is example middleware

```php
<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Auth;

class AuthMiddleware extends Middleware
{
    public function handle()
    {
        if (Auth::check()) {
            return $next();
        }
        return $this->response(401, ['message' => 'Unauthorized']);
    }
}
```

> to use middleware, you can add middleware in route

```php

Router::get('/schedules', [ScheduleController::class, 'index'], [AuthMiddleware::class]);

```

### Routing

> You can add route in `App/Routes/Api.php`

```php

<?php

use App\Core\Route;

Router::get('/home', [HomeController::class, 'index']);

```

#### Route Group

> You can add route group in `App/Routes/Api.php`

```php

<?php

use App\Core\Route;


Router::controller(ProductController::class)->group(function () {
    Router::post('/products/store', 'store');
});

```

### Query Builder

<!-- description of querybuilder from mardira framework -->

<!-- use library Query Builder -->

####

```php

use App\Core\QueryBuilder as DB;

```

#### Select

```php
DB::table('users')->select('name', 'email')->get();
```

#### Where

```php

// equal
DB::table('users')->where('id', 1)->get();

DB::table('users')->where('id', 1, '>')->get();

DB::table('users')->where('id', 1, '<')->get();

DB::table('users')->where('id', 1, '>=')->get();

DB::table('users')->where('id', 1, '<=')->get();

DB::table('users')->where('id', 1, '!=')->get();

DB::table('users')->where('id', 1, '<>')->get();

// like

DB::table('users')->where('name', 'admin', 'like')->get();

DB::table('users')->where('name', 'admin', 'not like')->get();

```

#### Or Where

```php


DB::table('users')->orWhere('id', 1)->get();

DB::table('users')->orWhere('id', 1, '>')->get();

DB::table('users')->orWhere('id', 1, '<')->get();

DB::table('users')->orWhere('id', 1, '>=')->get();

DB::table('users')->orWhere('id', 1, '<=')->get();

DB::table('users')->orWhere('id', 1, '!=')->get();

DB::table('users')->orWhere('id', 1, '<>')->get();

```

#### Where In

```php

DB::table('users')->whereIn('id', [1,2,3])->get();

DB::table('users')->whereNotIn('id', [1,2,3])->get();

```

#### Where Not In

```php

DB::table('users')->whereNotIn('id', [1,2,3])->get();

```

#### Where Null

```php

DB::table('users')->whereNull('id')->get();
```

#### Where Not Null

```php
DB::table('users')->whereNotNull('id')->get();
```

#### Order By

```php

DB::table('users')->orderBy('id', 'desc')->get();

DB::table('users')->orderBy('id', 'asc')->get();

```

#### Join Table

```php

DB::table('users')
    ->join('roles', 'users.role_id', '=', 'roles.id')
    ->select('users.*', 'roles.name as role_name')
    ->get();

```

#### Group By

```php

DB::table('users')
    ->groupBy('role_id')
    ->get();

```

#### Insert

```php

DB::table('users')->insert([
    'name' => 'user',
    'email' => 'user@user.com',
    'password' => password_hash('password', PASSWORD_DEFAULT),
]);

```

#### Update

```php

DB::table('users')->where('id', 1)->update([
    'name' => 'user',
    'email' => 'user@gmail.com',
]);

```

#### Delete

```php

DB::table('users')->where('id', 1)->delete();

```

#### Count

```php

DB::table('users')->count();

```


## Support

Reach out to me at one of the following places!

- Website at <a href="https://demostmikmi.com" target="_blank">`demostmikmi.com`</a>
