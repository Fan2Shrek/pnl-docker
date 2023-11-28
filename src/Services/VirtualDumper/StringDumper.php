<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class StringDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_string($data);
    }

    public function dump(mixed $data): string
    {
        return sprintf("'%s'", $data);
    }
}
