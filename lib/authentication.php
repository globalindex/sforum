<?php
declare(strict_types=1);

require_once 'session.php';

/*
    Authentication helpers
    ======================

    1. login
    --------
    
    login(mixed $user) : void

    Meldet einen Benutzer am System an.

    Anwendungsbeispiele:
        login(['id' => 1, 'email'=> 'a@moo.de', ...]);
*/

function login(array $user)
{
    session('_user', $user);
}


/*
    2. logout
    ---------

    logout() : void

    Meldet den angemeldeten Benutzer ab.
*/

function logout()
{
    session('_user', null);
}


/*
    3. auth_user
    
    auth_user([string $key]) : mixed

    Kann mit und ohne $key aufgerufen werden.

    Ohne $key:
    
    auth_user() : mixed

    Gibt die gespeicherten Daten des eingeloggten Benutzers zurück.
    Man erhält also wieder das Array bzw die Daten, die man mit login()
    gespeichert hat.
    Gibt es keinen eingeloggten Benutzer, soll die Funktion
    null zurückgeben.

    Mit $key:

    auth_user('email') : mixed

    Gibt den Wert zurück, der unter dem übergebenen
    Key gespeichert ist.
    
    Wenn der Key nicht existiert, soll null TODO zurückgegeben werden.
*/

function auth_user($key = null)
{
    if ($key === null) {
        return session('_user');
    }
    
    return session('_user')[$key] ?? null;
}


/*
    4. auth_id
    
    auth_id() : int

    Gibt die id des angemeldeten Benutzers zurück. Wir gehen in dieser
    Funktion davon aus, dass das Array, das mit login() gespeichert wurde,
    einen Key namens id enthält. Wenn nicht, soll die Funktion null zurückgeben.
*/

function auth_id()
{
    return ($id = auth_user('id')) ? (int) $id : null;
}
