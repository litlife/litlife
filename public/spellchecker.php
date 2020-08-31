<?php

require_once '../vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
try {
    new \SpellChecker\Request();

} catch (Exception $e) {
    if (function_exists('http_response_code'))
        http_response_code(500);
    else
        header('HTTP/1.0 500 Internal Server Error');

    \SpellChecker\Request::send_response(array('error' => $e->getMessage()));
}

?>