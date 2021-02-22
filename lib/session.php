<?php
declare(strict_types=1);

/*
    Session helpers
    ===============
    
    1. session
    ----------

    Schreibe eine Funktion, die Werte in der Session speichern
    und aus der Session auslesen und löschen kann.

    Sollte beim Aufruf der Funktion noch keine Session gestartet sein,
    soll die Funktion eine Fehlermeldung ausgeben.

    session(string $key, [mixed $value]) : mixed

    Schaue dir dazu die folgenden Funktionen an:

    unset();
        // zB unset($variable);
        //    unset($array['key']);
    func_num_args();
    session_status();

    Die Funktion kann auf vier Arten aufgerufen werden:

    a) session()

    Gibt den gesamten Inhalt der Session zurück.

    b) session($key)

    Gibt den Wert zurück, der in der Session unter $key
    gespeichert ist.
    Wird $key nicht gefunden, soll null zurückgegeben werden.

    c) session($key, $value)

    Speichert $value unter dem Schlüssel $key in der Session.

    d) session($key, null)

    Löscht den Eintrag mit den Schlüssel $key aus der Session.

    
    Anwendungsbeispiele:

    session();
    session('name', 'Michael');   //=> Speichert 'Michael'
    session('name');              //=> Liest 'Michael' aus
    session('name', null);        //=> Löscht 'Michael'
*/


function session(string $key = null, $value = null)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        trigger_error('You have to call session_start() before you can use the session module.');
    }

    if (func_num_args() === 0) {
        return $_SESSION;
    }

    if (func_num_args() === 1) {
        return $_SESSION[$key] ?? null;
    }

    if ($value === null) {
        unset($_SESSION[$key]);
    
    } else {
        $_SESSION[$key] = $value;
    }
}
