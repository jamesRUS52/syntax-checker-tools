<?php

use SyntaxChecker\Checker;

require __DIR__.'\vendor\autoload.php';

if ( count($argv) < 2) {
    print "usage: php client.php <STRING>";
    exit(0);
}

$input_string = $argv[1];

$client = new Checker();
try {
    $result = $client->CheckBrackes($input_string);
    if ($result) {
        print "input string is valid";
    } else {
        print "input string is invalid";
    }
} catch (InvalidArgumentException $ex) {
    print "input string has incorrect charesters";
}
