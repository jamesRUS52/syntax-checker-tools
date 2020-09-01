<?php

$address = "localhost";
$port = 5000;

if (count($argv) > 1) {
    $address = $argv[2];
}
if (count($argv) > 2) {
    $port = $argv[3];
}

print "Trying connect {$address}:{$port}".PHP_EOL;

try {
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    $result = socket_connect($socket, $address, $port);
    print "connected".PHP_EOL;
    print socket_read($socket, 2048);
    do {
        print "Input your request for checking: ";
        $request = fgets(STDIN);
        if (trim($request) == "quit" || trim($request) == "exit") {
            break;
        }
        socket_write($socket, $request, strlen($request));
        while ($response = trim(socket_read($socket, 2048, PHP_NORMAL_READ))) {
            echo $response . PHP_EOL;
        }
    } while (true);
    socket_close($socket);
} catch (Exception $ex) {
    print($ex->getMessage());
}