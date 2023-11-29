<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class StringDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_string($data);
    }

    public function dump(mixed $data, int $indent = 1): string
    {
        return sprintf("%s'%s'", str_repeat("\t", 0), $data);
    }
}
