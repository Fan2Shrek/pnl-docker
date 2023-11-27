<?php

namespace Pnl\PNLDocker\Docker;

readonly class DockerConfig
{
    public function __construct(
        public string $containerName,
        public string $image,
        public array $ports,
    ) {
    }
}
