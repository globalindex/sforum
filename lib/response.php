<?php
declare(strict_types=1);

/*
    Response helpers
    ================

    1. redirect
    -----------
    Schreibe eine Funktion, die einen Redirect ausführt.
    Die Funktion muss also einen Location header setzen.

    Anwendungsbeispiel:

    redirect('login.php');
*/

function redirect(string $url, int $response_code = 302)
{
    header("Location: $url", true, $response_code);
    exit();
}


function json_response(array $data, int $status_code = 200)
{
    http_response_code($status_code);

    header('Content-Type: application/json');
    echo json_encode($data);
}
