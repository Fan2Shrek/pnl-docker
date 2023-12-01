<?php

namespace Pnl\PNLDocker\Services\Docker;

class DockerClient
{
    public function request(string $path, array $param = []): string
    {
        $socket = '/var/run/docker.sock';

        set_time_limit(0);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_UNIX_SOCKET_PATH => $socket,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_BUFFERSIZE => 256,
            CURLOPT_TIMEOUT => 1000000
        ]);

        curl_setopt($ch, CURLOPT_URL, sprintf("http:/localhost/%s?%s", $path, http_build_query($param)));
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public function getContainers(): array
    {
        $response = $this->request('containers/json', ['all' => true]);

        return json_decode($response, true);
    }
}
