<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\DockerConfigBag;
use Pnl\PNLDocker\Services\Docker\Docker;
use Pnl\PNLDocker\Services\VirtualDumper\VirtualDumper;

class DockerRegistryManager
{
    private const REGISTRATION_FILE = __DIR__ . '/../../config/registration.php';

    private array $registry = [];

    private bool $isLoaded = false;

    public function __construct(
        private readonly VirtualDumper $virtualDumper,
        private readonly Docker $docker,
    ) {
    }

    private function load(): DockerConfigBag|array
    {
        if (!$this->isLoaded) {
            if (!file_exists(self::REGISTRATION_FILE)) {
                touch(self::REGISTRATION_FILE);
                $this->save();
            }
            $this->registry = require self::REGISTRATION_FILE;
            $this->isLoaded = true;
        }

        return $this->registry;
    }

    public function get(): array
    {
        return $this->load();
    }

    public function getBagFrom(string $path): ?DockerConfigBag
    {
        if (!isset($this->get()[$path])) {
            return null;
        }

        $registry = $this->get()[$path];

        return $registry;
    }

    public function add(string $path, array|DockerConfigBag $bag): void
    {
        $this->registry[$path] = $bag;
    }

    public function update(array $containers): void
    {
        foreach ($containers as $path => $bag) {
            $this->add($path, $bag);
        }

        $this->save();
    }
    public function save(): void
    {
        $content = $this->virtualDumper->dump($this->registry);

        $file = fopen(self::REGISTRATION_FILE, 'w');

        fwrite($file, sprintf("<?php\n\nreturn %s;", $content));

        fclose($file);
    }
}
