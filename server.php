<?php

use SyntaxChecker\Checker;

require __DIR__.'\vendor\autoload.php';

set_time_limit(0);
ob_implicit_flush();

$address = '0.0.0.0';
$port = 5000;

if ( count($argv) >= 2) {
    $port = filter_var($argv[1], FILTER_SANITIZE_NUMBER_INT);
    if (!$port || $port < 1  || $port > 65535) {
        print "Wrong port number";
        exit(0);
    }
}

try {
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_bind($socket, $address, $port);
    socket_listen($socket, 5);
    print "Server is up and running on {$address}:{$port}".PHP_EOL;

    $checker = new Checker();
    $checker->setAvailableChars(['(', ')', ' ', '\n', '\r', '\n\r', '\t', PHP_EOL]);

    do {
        $msgsock = socket_accept($socket);
        print "Client connected".PHP_EOL;
        $msg = "Hello from Syntax checker server. Type your string for checking".PHP_EOL;
        socket_write($msgsock, $msg, strlen($msg));

        do {
            $request = trim(@socket_read($msgsock, 2048, PHP_NORMAL_READ));
            if (empty($request)) {
                if (socket_last_error($msgsock) == 10054) {
                    break;
                }
                continue;
            }
            print "input string is: {$request}".PHP_EOL;
            if ($request == 'quit' || $request == 'exit') {
                break;
            }
            try {
                $result = $checker->CheckBrackes($request);
                if ($result) {
                    $response = "input string is valid".PHP_EOL;
                } else {
                    $response = "input string is invalid".PHP_EOL;
                }
            } catch (InvalidArgumentException $ex) {
                $response = $ex->getMessage().PHP_EOL;
            }
            $response = (new DateTime())->format('d.m.Y H:i:s').' '.$response."\0";
            socket_write($msgsock, $response, strlen($response));
        } while (true);
        socket_close($msgsock);
        print "Client disconnected".PHP_EOL;
    } while (true);
    socket_close($socket);
} catch (Exception $ex) {
    print $ex->getMessage();
} finally {
    if (isset($msgsock)) {
        socket_close($msgsock);
    }
    if (isset($socket)) {
        socket_close($socket);
    }
}
