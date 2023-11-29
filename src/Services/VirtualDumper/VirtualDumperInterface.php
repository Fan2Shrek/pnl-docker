<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

interface VirtualDumperInterface
{
    public function supports(mixed $data): bool;

    public function dump(mixed $data, int $indent = 1): string;
}
