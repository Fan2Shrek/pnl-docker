<?php

$socket = '/var/run/docker.sock';

set_time_limit(0);
$ch = curl_init();
curl_setopt($ch, CURLOPT_UNIX_SOCKET_PATH, $socket);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 256);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000000);

$writeFunction = function ($ch, $string) {
    echo $string;
    $length = strlen($string);
    printf("Received %d byte\n", $length);
    flush();
    return $length;
};
curl_setopt($ch, CURLOPT_WRITEFUNCTION, $writeFunction);


curl_setopt($ch, CURLOPT_URL, "http:/localhost/version");
$response = curl_exec($ch);

curl_close($ch);

var_dump($response);
