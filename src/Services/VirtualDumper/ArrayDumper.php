<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class ArrayDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_array($data);
    }

    public function dump(mixed $data, int $indent = 1): string
    {
        $dump = "[\n";

        foreach ($data as $key => $value) {
            $dump .= sprintf(
                "%s'%s' => %s,\n",
                str_repeat("\t", $indent),
                $key,
                $this->virtualDumper->dump($value, $indent + 1)
            );
        }

        $dump .= str_repeat("\t", $indent - 1) . ']';

        return $dump;
    }
}
