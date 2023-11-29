<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

trait VirtualDumperAwareTrait
{
    private VirtualDumperInterface $virtualDumper;

    public function setVirtualDumper(VirtualDumperInterface $virtualDumper): self
    {
        $this->virtualDumper = $virtualDumper;

        return $this;
    }
}
