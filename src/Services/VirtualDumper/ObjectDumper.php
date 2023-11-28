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

        $reflection = new \ReflectionClass($data);

        foreach ($reflection->getProperties() as $property) {
            $methodName = sprintf('get%s', ucfirst($property->getName()));
            if ($reflection->hasMethod($methodName)) {
                $value = $data->$methodName();
                $dump .= sprintf("\t'%s' => %s,\n", $property->getName(), $this->virtualDumper->dump($value));
            }
        }

        $dump .= ']';

        return $dump;
    }
}
