<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class BooleanDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_bool($data);
    }

    public function dump(mixed $data, int $indent = 1): string
    {
        return $data ? 'true' : 'false';
    }
}
