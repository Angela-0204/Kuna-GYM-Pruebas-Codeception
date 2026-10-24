<?php

use Dotenv\Dotenv;

$_SERVER['HTTP_HOST'] = 'localhost';

//Cargar .env
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$_ENV['DB_HOST'] = $_SERVER['DB_HOST'] = 'localhost';
$_ENV['DB_USER'] = $_SERVER['DB_USER'] = 'root';
$_ENV['DB_PASS'] = $_SERVER['DB_PASS'] = '';
$_ENV['DB_APP'] = $_SERVER['DB_APP'] = 'gym_app_test';
$_ENV['DB_SEGURIDAD'] = $_SERVER['DB_SEGURIDAD'] = 'gym_seguridad_test';

putenv('DB_HOST=localhost');
putenv('DB_USER=root');
putenv('DB_PASS=');
putenv('DB_APP=gym_app_test');
putenv('DB_SEGURIDAD=gym_seguridad_test');

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../config/config.php';
