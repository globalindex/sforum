<?php
declare(strict_types=1);

/*
    View helpers
    ============
    
    1. html_escape
    --------------

    Schreibe die Funktion html_escape().

    Sie soll einen String für die Ausgabe in ein HTML
    Dokument bereinigen.

    function html_escape(string $string, string $encoding) : string

    Anwendungsbeispiel:
        echo "User input was: " . html_escape($input);
*/

function html_escape(string $string, string $encoding = 'UTF-8') : string
{
    return htmlspecialchars($string, ENT_QUOTES, $encoding);
}


/*
    2. e
    --------------

    Schreibe eine Funktion e().

    function e(string $string) : string

    Die Funktion ist lediglich ein Wrapper um html_escape(),
    so dass wir noch kürzer schreiben können:

        <?= e($user['name']) ?>
*/

function e($string) : string
{
    return html_escape((string) $string);
}


/*
    3. error_for
    ------------

    Schreibe eine Funktion error_for:

    function error_for(string $field, array $errors, string $format) : string

    Die Funktion erhält den Namen eines Formularfeldes
    und ein assoziatives Array mit Fehlermeldungen, deren
    Keys gleich den Namen der Formularfelder sein müssen.

    Die Funktion findet die Fehlermeldung für das entsprechende
    Feld und fomatiert sie gemäß einem printf-Format-String.

    printf ist eine vielseitige String-Formatierfunktion.

    In einem solchen Format String kann mit %s ein Platzhalter
    für einen String eingefügt werden. Dieser String ist unsere
    Fehlermeldung. Das könnte zB so aussehen:

    $format = "<div class=\"alert\">%s</div>"

    Die Fehlermeldung wird dann an die Stelle von %s ausgegeben.
    Der Funktionsaufruf wäre dann:

    sprintf($format, $errors[$field]);

*/


function error_for(
    array $errors,
    string $fieldname,
    string $format = '<p class="mt-2 text-sm text-red-500">%s</p>'
) : string
{
    if (isset($errors[$fieldname])) {
        return sprintf($format, $errors[$fieldname]);
    }

    return '';
}