<?php

namespace Pnl\PNLDocker\Services\VirtualDumper;

interface VirtualDumperAwareInterface extends VirtualDumperInterface
{
    public function setVirtualDumper(VirtualDumperInterface $dumper): self;
}
