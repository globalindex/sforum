<?php
declare(strict_types=1);

/*
    TEIL 1: Database Basics
    =======================

    Anmerkung:
    Einige der Hilfsfunktionen erwarten eine globale
    Variable namens $database

    Dies ist zwar kein guter Programmierstil (globals are evil!)
    aber mit unseren Mitteln gibt es derzeit keine wirklich
    saubere Lösung für dieses Problem.

    Vorsicht bei folgenden Funktionen:

    - db_delete
        Arbeitet nur mit der `id` Spalte.

    - db_insert und db_update
        Es müssen korrekte Datentypen übergeben werden,
        sonst funktioniert das automatische "escapen" in
        der db_prepare Funktion nicht.

    - db_raw_...
        Bereinigt nichts. Deshalb der Name "raw"
        Alle Werte müssen von Hand bereinigt werden.

    - db_query
        Akzeptiert einen zweiten Parameter, der bestimmt,
        ob ein Datenbank-Fehler als FATAL ERROR "gehandelt"
        wird, oder ob die Funktion den Fehler einfach ignoriert.
*/



/*
    1. db_connect
    -------------

    db_connect(array $config) : mysqli

    Stellt eine Verbindung zur Datenbank her und gibt
    diese Verbindung zurück.

    Anwendungsbeispiel:

    $database = db_connect([
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'wbs'
    ]);
*/
function db_connect(array $config) : mysqli
{
    $db = mysqli_connect(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database'],
        $config['port'] ?? 3306
    );

    if (mysqli_connect_errno()) {
        trigger_error("DB Error: " . mysqli_connect_error(), E_USER_ERROR);
    }

    mysqli_set_charset($db, $config['charset'] ?? 'utf8mb4');

    return $db;
}



/*
    2. db_get_global
    ------------------

    db_get_global() : mysqli

    Diese Funktion überprüft ob die globale Variable
    $database_connection existiert und eine gültige Datenbank-
    Verbindung beinhaltet. Wenn nicht, wird ein
    Fatal Error erzeugt.

    Schau dir dazu folgende Funktionen an:

    trigger_error
    function_exists
*/

function db_get_global($fatal = true)
{
    global $database;

    if ($fatal && (!isset($database) || !$database instanceof mysqli)) {
        trigger_error('The database-module requires a globally defined variable called $database that holds a MySQL/MariaDB connection (mysqli-Object). You can use db_connect() to establish this connection.', E_USER_ERROR);
    }

    return $database;
}


/*
    3. db_disconnect
    ----------------
    
    db_disconnect() : bool

    Bricht die Verbindung zur Datenbank ab.
*/
function db_disconnect()
{
    $db = db_get_global(false);

    if ($db instanceof mysqli) {
        return mysqli_close($db);
    }
}


/*
    4. db_query
    -----------
    
    db_query(string $sql) : mixed

    Feuert eine beliebige Query an die Datenbank.
    Dabei soll eine Fehlerüberprüfung stattfinden.

    Der Rückgabewert von mysqli_query soll unverändert
    zurückgegeben werden.
    Die Funktion reicht das Ergebnis also nur durch.

    Außerdem akzeptiert die Funktion ein zweites Argument,
    das bestimmt, ob ein Datenbank-Error als FATAL ERROR
    "gehandelt" wird, oder ob er einfach ignoriert wird.
*/
function db_query(string $sql, $fatal = true)
{
    $db = db_get_global();

    $result = mysqli_query($db, $sql);

    if ($fatal && mysqli_errno($db)) {
        trigger_error(
            'DB ERROR ' . mysqli_errno($db) .
            ': ' . mysqli_error($db), E_USER_ERROR
        );
    }

    return $result;
}



/*
    5. db_delete
    ------------

    db_delete(string $table, int|array $ids) : bool

    Löscht einen oder mehrere Datensätze anhand ihrer id bzw ids
    aus der Datenbank.

    Die Funktion soll ein einzelnes Integer oder ein Array von
    Integers akzeptieren.

    Anwendungsbeispiel:

    db_delete('users', 1);
    db_delete('users', [4, 6, 11]);
*/
function db_delete(string $table, $ids) : bool
{
    $ids = (array) $ids;

    $count = count($ids);
    for ($i = 0; $i < $count; $i++) {
        $ids[$i] = (int) $ids[$i];
    }

    $ids = implode(', ', $ids);

    $sql = "DELETE FROM `$table` WHERE `id` IN ($ids)";

    return db_query($sql);
}


/*
    6. db_prepare
    -------------

    db_prepare(mixed $value) : mixed

    Bereitet einen Wert für die Verwendung in einer SQL-Query vor.

    Die Funktion soll Booleans und Strings so bereinigen,
    dass sie direkt in einer SQL-Query verwendet werden können.
    Strings sollen inklusive der SQL-single quotes zurückgegeben werden.

    Anwendungsbeispiel:

    $bereinigt = db_prepare(false);           // 0
    $bereinigt = db_prepare('it\'s a quote'); // "it\\\'s a quote"
    $bereinigt = db_prepare(12)               // 12
*/
function db_prepare($value) : string
{
    if (is_bool($value)) {
        return $value ? '1' : '0';
    }

    if ($value === 'NULL' || $value === 'null' || $value === null) {
        return 'NULL';
    }

    if (is_string($value)) {
        return "'" . mysqli_escape_string(db_get_global(), $value) . "'";
    }

    return (string) $value;
}


/*
    7.) db_insert

    db_insert(string $table, array $data)

    Legt einen neuen Datensatz in der Datenbank an.
    Gibt die Id des neu eingefügten Datensatzes zurück.

    Denke daran die übergebenen Werte mit db_prepare
    zu bereinigen.

    Anwendungsbeispiel:

    $new_id = db_insert('users', [
        'email' => 'carla@moo.de',
        'password' => 'chew-da-hay!'
    ]);

    Schau dir dazu die Funktion mysqli_insert_id an.
*/
function db_insert(string $table, array $data)
{
    $columns = [];
    $values = [];

    foreach ($data as $column => $value) {
        $columns[] = "`$column`";
        $values[] = db_prepare($value);
    }

    $columns = implode(', ', $columns);
    $values = implode(', ', $values);

    $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";

    $success = db_query($sql);

    if ($success) {
        return mysqli_insert_id(db_get_global());
    }

    return false;
}


/*
    8. db_update

    db_update(string $table, int $id, array $data) : bool

    Ändert einen bestehenden Datensatz in der Datenbank ab.
    Der Datensatz wird dabei seiner Id gefunden.

    Anwendungsbeispiel:

    db_update('users', 1, ['password' => 'new-password']);
*/
function db_update(string $table, int $id, array $data) : bool
{
    $pairs = [];

    foreach ($data as $column => $value) {
        $pairs[] = "`$column` = " . db_prepare($value);
    }

    $pairs = implode(', ', $pairs);

    $sql = "UPDATE `$table` SET $pairs WHERE `id` = $id";

    return db_query($sql);
}



/*
    9) db_raw_select

    db_raw_select(string $sql) : array

    Feuert eine (SELECT) Query und gibt das Ergebnis als
    Array von Datensätzen zurück.

    Die Funktion bereinigt ihre Parameter nicht.
    Man übergibt ihr lediglich eine SQL-Query, die sie "blind" abfeuert.
    Deshalb der Name "raw"_select.

    Anwendungsbeispiel:

    $users = db_raw_select("SELECT * FROM `users`);
*/
function db_raw_select(string $sql)
{
    $result = db_query($sql);

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_free_result($result);

    return $data;
}


/*
    10) db_raw_first

    db_raw_first(string $sql) : array

    Macht das Gleiche wie db_raw_select, gibt aber kein Array von
    Datensätzen zurück, sondern einen einzigen Datensatz.
    
    Anwendungsbeispiel:

    $user = db_raw_first("SELECT * FROM `users` where `email` = 'a@moo.de');
*/

function db_raw_first(string $sql)
{
    $result = db_query($sql);

    $data = mysqli_fetch_assoc($result);

    mysqli_free_result($result);

    return $data;
}
