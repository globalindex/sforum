<?php
declare(strict_types=1);

/*
    Request helpers
    ===============
    
    1. request_method
    -----------------
    
    request_method() : string

    Gibt die HTTP Methode des Requests zurück.

    Anwendungsbeispiel:

    if (request_method() === 'POST') {
        // ...
    }
*/

function request_method() : string
{
    return $_SERVER['REQUEST_METHOD'];
}


/*
    2. request_is
    -------------

    request_is(string $method) : bool

    Überprüft, ob der Request das angegebene HTTP Verb benutzt.
    
    Anwendungsbeispiel:
    
    if (request_is('get')) {
        // ...``
    }
*/

function request_is(string $method) : bool
{
    return strtolower(request_method()) === strtolower($method);
}



/*
    3. request & query
    ------------------

    request() dient dazu, Daten aus dem Request-Body auszulesen,
    also aus dem $_POST-Array.

    query() liest Daten aus dem Query-String aus,
    also aus dem $_GET-Array.

    Beide Funktionen haben, bis auf den Funktionsnamen,
    die gleiche Form. Sie können beide
    - ohne Argumente
    - mit einem String-$key oder
    - mit einem Array von $keys aufgerufen werden.

    request([string $key | array $keys]) : mixed
    query([string $key | array $keys]) : mixed


    a)
    request() oder query()

    Gibt ein Array zurück, das alle Daten aus
    $_GET bzw. $_POST enthält.

    Anwendungsbeispiel:

    request()
    //=> ['name' => 'Neues Todo', 'id' => '12']

    query()
    //=> ['q' => 'Wetter Kuala Lumpur', 'lang' => 'en']


    b)
    request(string $key) oder query(string $key)

    Gibt den Wert zurück, der in $_GET bzw. $_POST
    unter dem Schlüssel $key gespeichert ist.

    Anwendungsbeispiel:

    request('name')  //=> Pete
    query('id')      //=> 12

    
    c) Optionale Aufgabe
    
    request(array $keys)
    query(array $keys)

    Gibt ein Array zurück, das alle angeforderten Schüssel
    aus dem $_GET bzw. $_POST Array enthält.

    Existiert ein verlangter $key nicht, soll er im
    Ergebnisarray nicht vorkommen.

    Anwendungsbeispiel:

    request(['name', 'password'])
    //=> ['name' => 'Pete', 'password' => '123']

    query(['q', 'page', 'existiert-nicht'])
    //=> ['q' => 'Socken', 'page' => 3]
*/

// Default Parameters

function request($key = null)
{
    return array_extract($_POST, $key);
}

function query($key = null)
{
    return array_extract($_GET, $key);
}

function array_extract(array $array, $key = null)
{
    if ($key === null) {
        return $array;
    }
    
    if (is_array($key)) {
        return array_intersect_key($array, array_flip($key));
    }

    return $array[$key] ?? '';
}


function request_wants_json()
{
    return
        strpos($_SERVER['HTTP_ACCEPT'], '/json') ||
        strpos($_SERVER['HTTP_ACCEPT'], '+json');
}
