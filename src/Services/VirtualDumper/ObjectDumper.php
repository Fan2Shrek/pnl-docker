<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class ObjectDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_object($data);
    }

    public function dump(mixed $data): string
    {
        $dump = '[';

        foreach ($data as $key => $value) {
            $dump .= sprintf("\t'%s' => %s,\n", $key, $this->virtualDumper->dump($value));
        }

        $dump .= ']';

        return $dump;
    }
}
