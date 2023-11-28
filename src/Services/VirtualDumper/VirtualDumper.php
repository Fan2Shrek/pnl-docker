<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

class VirtualDumper implements VirtualDumperInterface
{
    private array $dumpers = [];

    public function supports(mixed $data): bool
    {
        return true;
    }

    public function dump(mixed $data): string
    {
        $dump = '';

        foreach ($this->dumpers as $dumper) {
            if ($dumper->supports($data)) {
                $dump .= $dumper->dump($data);
            }
        }

        return $dump;
    }

    public function addDumper(VirtualDumperInterface $dumper): self
    {
        if (!in_array($dumper, $this->dumpers, true) && $this !== $dumper) {
            $this->dumpers[] = $dumper;
            if ($dumper instanceof VirtualDumperAwareInterface) {
                $dumper->setVirtualDumper($this);
            }
        }

        return $this;
    }
}
