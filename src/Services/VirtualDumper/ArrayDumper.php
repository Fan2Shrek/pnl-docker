<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class ArrayDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_array($data);
    }

    public function dump(mixed $data): string
    {
        $dump = "[\n";

        foreach ($data as $key => $value) {
            $dump .= sprintf("\t'%s' => %s,\n", $key, $this->virtualDumper->dump($value));
        }

        $dump .= "]";

        return $dump;
    }
}
