# Laravel 5.4 Socket.io Chat/MicroBlog

## Requirements (tested with)

+ PHP 7.1
+ MySqL 5.7
+ Redis 3.2
+ Node.js 3.10


## Installation and usage

+ Clone this repository
+ Create .env at root folder (Use .env.example)
+ Install dependencies:

``composer install``

``npm install``

+ Apply migrations (at root project directory):

`` php artisan migrate ``

+ Create symlink for uploaded images:

`` php artisan storage:link ``

+ Init Laravel-echo-server:

`` laravel-echo-server init ``

+ Config Laravel-echo-server (Socket.io server) at <b>laravel-echo-server.json</b>

+ Start Socket.io server:

`` laravel-echo-server start ``

+ Configure your webserver to <root_folder>/public (there is entry point)