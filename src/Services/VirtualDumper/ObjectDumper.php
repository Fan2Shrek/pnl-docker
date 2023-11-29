<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class ObjectDumper implements VirtualDumperAwareInterface
{
    use VirtualDumperAwareTrait;

    public function supports(mixed $data): bool
    {
        return is_object($data);
    }

    public function dump(mixed $data, int $indent = 1): string
    {
        $dump = sprintf("new %s(\n", $data::class);

        $reflection = new \ReflectionClass($data);

        foreach ($reflection->getProperties() as $property) {
            $methodName = sprintf('get%s', ucfirst($property->getName()));
            if ($reflection->hasMethod($methodName)) {
                $value = $data->$methodName();
                $dump .= sprintf("%s%s,\n", str_repeat("\t", $indent), $this->virtualDumper->dump($value, $indent + 1));
            } elseif ($reflection->hasMethod($property->getName())) {
                $value = $data->{$property->getName()}();
                $dump .= sprintf("%s%s, \n", str_repeat("\t", $indent), $this->virtualDumper->dump($value, $indent + 1));
            } elseif ($property->isPublic()) {
                $dump .= sprintf("%s%s,\n", str_repeat("\t", $indent), $this->virtualDumper->dump($property->getValue($data), $indent + 1));
            }
        }

        $dump .= str_repeat("\t", $indent - 1) . ')';

        return $dump;
    }
}
