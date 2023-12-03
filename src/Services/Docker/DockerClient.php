<?php

namespace Pnl\PNLDocker\Services\Docker;

use Pnl\App\SettingsProvider;
use Pnl\PNLDocker\Docker\Container;

class DockerClient
{
    private string $socket;

    public function __construct(SettingsProvider $settingsProvider)
    {
        $this->socket = $settingsProvider->get('docker.socket');
    }

    public function request(string $path, string $method = 'GET', array $param = []): string
    {
        set_time_limit(0);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_UNIX_SOCKET_PATH => $this->socket,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_BUFFERSIZE => 256,
            CURLOPT_TIMEOUT => 1000000,
            CURLOPT_CUSTOMREQUEST => $method,
        ]);

        curl_setopt($ch, CURLOPT_URL, sprintf("http:/localhost/%s%s%s", $path, empty($param) ? '' : '?', http_build_query($param)));
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public function getContainers(): array
    {
        $response = $this->request('containers/json', 'GET', ['all' => true]);

        return json_decode($response, true);
    }

    public function start(Container $container): void
    {
        $this->request(sprintf('containers/%s/start', $container->getId()), 'POST');
    }

    public function stop(Container $container): void
    {
        $this->request(sprintf('containers/%s/stop', $container->getId()), 'POST');
    }
}
