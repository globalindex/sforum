<?php

// Adjust paths as needed
const PATH = 'C:/xampp/htdocs/kurs/php/forum/';
const BASE_URL = 'http://localhost/kurs/php/forum/';

const LIB_PATH = PATH.'lib/';
require_once LIB_PATH.'authentication.php';
require_once LIB_PATH.'database.php';
require_once LIB_PATH.'request.php';
require_once LIB_PATH.'response.php';
require_once LIB_PATH.'session.php';
require_once LIB_PATH.'view.php';

// Don't forget to adjust the DB connection details
$database = db_connect([
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'small_forum'
]);

session_start();

$errors = [];

$page_title = "FORUM :: Dashboard";
