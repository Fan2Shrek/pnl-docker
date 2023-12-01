<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class IntDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_int($data);
    }

    public function dump(mixed $data, int $indent = 1): string
    {
        return $data;
    }
}
